<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\MovimientoCaja;
use App\Http\Requests\StoreMovimientoCajaRequest;
use Illuminate\Support\Facades\DB;
use App\Models\Bitacora;
use Exception;

use App\Models\Venta;

/**
 * Class CajaController
 * 
 * Gestiona el flujo de caja (apertura, cierre y movimientos).
 * Permite controlar el dinero en efectivo del punto de venta.
 */
class CajaController extends Controller
{
    /**
     * Muestra el historial de sesiones de caja.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $cajas = Caja::orderBy('id_caja','desc')->paginate(10);
        return view('caja.index', compact('cajas'));
    }

    /**
     * Devuelve el estado actual de la caja abierta (JSON para POS).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function estadoActual()
    {
        $caja = Caja::where('estado', 'abierta')->first();
        
        if (!$caja) {
            return response()->json([
                'abierta' => false,
                'mensaje' => 'No hay caja abierta'
            ]);
        }

        // Calcular ventas en efectivo desde la apertura
        $ventasEfectivo = Venta::where('fecha', '>=', $caja->fecha_apertura)
            ->where('estado', 'completada')
            ->where('metodo_pago', 'Efectivo')
            ->sum('total');

        // Calcular ventas con tarjeta desde la apertura
        $ventasTarjeta = Venta::where('fecha', '>=', $caja->fecha_apertura)
            ->where('estado', 'completada')
            ->where('metodo_pago', 'Tarjeta')
            ->sum('total');
            
        // Movimientos manuales
        $ingresos = $caja->movimientos()->where('tipo', 'ingreso')->sum('monto');
        $egresos = $caja->movimientos()->where('tipo', 'egreso')->sum('monto');
        
        $saldoEsperado = $caja->monto_inicial + $ventasEfectivo + $ingresos - $egresos;

        // Obtener últimos movimientos
        $movimientos = $caja->movimientos()
            ->orderBy('fecha', 'desc')
            ->orderBy('id_movimiento', 'desc')
            ->take(10)
            ->get()
            ->map(function ($mov) {
                return [
                    'tipo' => $mov->tipo,
                    'monto' => $mov->monto,
                    'descripcion' => $mov->descripcion,
                    'hora' => $mov->fecha ? $mov->fecha->format('H:i') : ''
                ];
            });

        return response()->json([
            'abierta' => true,
            'id_caja' => $caja->id_caja,
            'fecha_apertura' => $caja->fecha_apertura->format('d/m/Y H:i'),
            'monto_inicial' => $caja->monto_inicial,
            'ventas_efectivo' => $ventasEfectivo,
            'ventas_tarjeta' => $ventasTarjeta,
            'ingresos' => $ingresos,
            'egresos' => $egresos,
            'saldo_esperado' => $saldoEsperado,
            'movimientos' => $movimientos
        ]);
    }

    /**
     * Muestra el detalle de una sesión de caja, incluyendo sus movimientos.
     *
     * @param Caja $caja
     * @return \Illuminate\View\View
     */
    public function show(Caja $caja)
    {
        $caja->load('movimientos');
        return view('caja.show', compact('caja'));
    }

    /**
     * Abre una nueva sesión de caja.
     * 
     * Verifica que no haya otra caja abierta previamente.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function abrir()
    {
        $abierta = Caja::where('estado','abierta')->first();
        if ($abierta) {
            return back()->with('error','Ya existe una caja abierta');
        }
        $caja = Caja::create([
            'fecha_apertura' => now(),
            'monto_inicial' => 0,
            'estado' => 'abierta',
        ]);
        Bitacora::registrar('CREATE', 'caja', $caja->id_caja, 'Caja abierta');
        return redirect()->route('caja.show',$caja)->with('success','Caja abierta');
    }

    /**
     * Cierra una sesión de caja abierta.
     * 
     * Calcula el monto final sumando/restando movimientos al monto inicial.
     * Utiliza una transacción para asegurar la integridad de los datos.
     *
     * @param Caja $caja
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cerrar(Caja $caja)
    {
        if ($caja->estado !== 'abierta') {
            return back()->with('error','La caja no está abierta');
        }
        try {
            DB::transaction(function () use ($caja) {
                $caja->load('movimientos');
                $saldo = $caja->monto_inicial;
                foreach ($caja->movimientos as $m) {
                    if ($m->tipo === 'ingreso') {
                        $saldo += $m->monto;
                    } else {
                        $saldo -= $m->monto;
                    }
                }
                $caja->monto_final = $saldo;
                $caja->fecha_cierre = now();
                $caja->estado = 'cerrada';
                $caja->save();
                Bitacora::registrar('UPDATE', 'caja', $caja->id_caja, 'Caja cerrada. Saldo final: ' . $saldo);
            });
        } catch (Exception $e) {
            return back()->with('error','No se pudo cerrar: '.$e->getMessage());
        }
        return back()->with('success','Caja cerrada');
    }

    /**
     * Registra un movimiento (ingreso/egreso) en la caja actual.
     * 
     * Solo permite registrar movimientos si la caja está abierta.
     *
     * @param StoreMovimientoCajaRequest $request
     * @param Caja $caja
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeMovimiento(StoreMovimientoCajaRequest $request, Caja $caja)
    {
        if ($caja->estado !== 'abierta') {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Solo se registran movimientos en caja abierta'], 422);
            }
            return back()->with('error','Solo se registran movimientos en caja abierta');
        }
        $mov = MovimientoCaja::create(array_merge(
            $request->validated(),
            ['id_caja' => $caja->id_caja, 'fecha' => now()]
        ));
        Bitacora::registrar('CREATE', 'movimientos_caja', $mov->id_movimiento, 'Movimiento de caja registrado: ' . $mov->descripcion);
        
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Movimiento registrado correctamente',
                'movimiento' => $mov
            ]);
        }

        return back()->with('success','Movimiento registrado');
    }
}
