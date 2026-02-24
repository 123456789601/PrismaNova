<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\MovimientoCaja;
use App\Http\Requests\StoreMovimientoCajaRequest;
use Illuminate\Support\Facades\DB;
use Exception;

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
            return back()->with('error','Solo se registran movimientos en caja abierta');
        }
        MovimientoCaja::create(array_merge(
            $request->validated(),
            ['id_caja' => $caja->id_caja, 'fecha' => now()]
        ));
        return back()->with('success','Movimiento registrado');
    }
}
