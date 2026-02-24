<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\MetodoPago;
use App\Http\Requests\StoreVentaRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Exception;

/**
 * Class VentaController
 * 
 * Gestiona el proceso de ventas en el punto de venta (web).
 * Maneja el registro de ventas, cálculo de descuentos, control de stock y anulación.
 */
class VentaController extends Controller
{
    /**
     * Muestra el historial de ventas con opciones de filtrado.
     * 
     * Permite filtrar por estado (completada/anulada) y rango de fechas.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $estado = request('estado');
        $desde = request('desde');
        $hasta = request('hasta');
        $ventas = Venta::with(['cliente'])
            ->when($estado, fn($q)=>$q->where('estado',$estado))
            ->when($desde, fn($q)=>$q->whereDate('fecha','>=',$desde))
            ->when($hasta, fn($q)=>$q->whereDate('fecha','<=',$hasta))
            ->orderBy('id_venta','desc')->paginate(10);
        return view('ventas.index', compact('ventas'));
    }

    /**
     * Muestra el formulario para realizar una nueva venta.
     * 
     * Carga clientes, productos y métodos de pago activos.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $clientes = Cliente::where('estado','activo')->orderBy('nombre')->get();
        $productos = Producto::where('estado','activo')->orderBy('nombre')->get();
        $metodos = MetodoPago::where('estado','activo')->orderBy('nombre')->get();
        return view('ventas.create', compact('clientes','productos','metodos'));
    }

    /**
     * Muestra el detalle completo de una venta.
     *
     * @param Venta $venta
     * @return \Illuminate\View\View
     */
    public function show(Venta $venta)
    {
        $venta->load(['cliente','usuario','detalles.producto','metodoPago']);
        return view('ventas.show', compact('venta'));
    }

    /**
     * Registra una nueva venta en el sistema.
     * 
     * Realiza múltiples acciones en una transacción:
     * - Calcula subtotales y descuentos automáticos (por volumen y monto total).
     * - Aplica cupones de descuento si existen y son válidos.
     * - Crea la venta y sus detalles.
     * - Descuenta stock de productos (con bloqueo pesimista).
     * - Envía correo electrónico de confirmación al cliente (asíncrono/silencioso).
     *
     * @param StoreVentaRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreVentaRequest $request)
    {
        $data = $request->validated();
        $venta = null;
        try {
            DB::transaction(function () use ($data, &$venta) {
                $subtotal = 0;
                $descuentoAutoLineas = 0;
                $detalles = [];
                foreach ($data['id_producto'] as $i => $idProducto) {
                    $cantidad = (int)$data['cantidad'][$i];
                    $precio = (float)$data['precio_unitario'][$i];
                    $linea = $cantidad * $precio;
                    $subtotal += $linea;
                    if ($cantidad >= 12) {
                        $descuentoAutoLineas += round($linea * 0.05, 2);
                    }
                    $detalles[] = [
                        'id_producto' => $idProducto,
                        'cantidad' => $cantidad,
                        'precio_unitario' => $precio,
                        'subtotal' => $linea,
                    ];
                }
                $descuentoAutoTiers = 0;
                if ($subtotal >= 200) {
                    $descuentoAutoTiers = round($subtotal * 0.10, 2);
                } elseif ($subtotal >= 100) {
                    $descuentoAutoTiers = round($subtotal * 0.05, 2);
                }
                $descuentoManual = isset($data['descuento']) ? (float)$data['descuento'] : 0;
                $descuento = $descuentoManual + $descuentoAutoLineas + $descuentoAutoTiers;
                if (!empty($data['cupon'])) {
                    $cupon = \App\Models\Cupon::where('codigo', $data['cupon'])->first();
                    if ($cupon && $cupon->esValido()) {
                        $extra = $cupon->tipo === 'porcentaje'
                            ? round($subtotal * ($cupon->valor / 100), 2)
                            : (float)$cupon->valor;
                        $descuento += $extra;
                        $cupon->increment('usos');
                    }
                }
                $impuesto = isset($data['impuesto']) ? (float)$data['impuesto'] : 0;
                $total = max(0, $subtotal - $descuento + $impuesto);

                $metodoPagoNombre = $data['metodo_pago'] ?? null;
                $metodoPagoId = $data['metodo_pago_id'] ?? null;
                if (!$metodoPagoNombre && $metodoPagoId) {
                    $mp = MetodoPago::find($metodoPagoId);
                    $metodoPagoNombre = $mp->nombre ?? null;
                }
                $venta = Venta::create([
                    'id_cliente' => $data['id_cliente'],
                    'id_usuario' => Auth::user()->id_usuario,
                    'fecha' => $data['fecha'],
                    'subtotal' => $subtotal,
                    'descuento' => $descuento,
                    'impuesto' => $impuesto,
                    'total' => $total,
                    'metodo_pago' => $metodoPagoNombre,
                    'metodo_pago_id' => $metodoPagoId,
                    'estado' => 'completada',
                ]);

                foreach ($detalles as $d) {
                    $d['id_venta'] = $venta->id_venta;
                    // Bloquear producto y validar stock
                    $producto = Producto::lockForUpdate()->find($d['id_producto']);
                    if ($producto->stock < $d['cantidad']) {
                        throw new Exception('Stock insuficiente para el producto '.$producto->nombre);
                    }
                    $producto->stock = (int)$producto->stock - (int)$d['cantidad'];
                    $producto->save();
                    DetalleVenta::create($d);
                }
            });
        } catch (Exception $e) {
            return back()->with('error', 'Error al registrar la venta: '.$e->getMessage())->withInput();
        }
        try {
            if ($venta) {
                $cli = $venta->cliente()->first();
                $to = $cli->email ?? null;
                if ($to) {
                    $url = route('mis-compras.show', ['venta' => $venta->id_venta]);
                    Mail::raw("Gracias por tu compra. Puedes ver tu comprobante aquí: {$url}", function ($m) use ($to, $venta) {
                        $m->to($to)->subject('Factura de compra #'.$venta->id_venta);
                    });
                }
            }
        } catch (Exception $e) {
            // silencioso: si falla el mail, no romper el flujo
        }
        return redirect()->route('ventas.index')->with('success','Venta registrada');
    }

    /**
     * Anula una venta completada.
     * 
     * Reintegra el stock de los productos y marca la venta como anulada.
     *
     * @param Venta $venta
     * @return \Illuminate\Http\RedirectResponse
     */
    public function anular(Venta $venta)
    {
        if ($venta->estado === 'anulada') {
            return back()->with('error','La venta ya está anulada');
        }
        try {
            DB::transaction(function () use ($venta) {
                $venta->load('detalles');
                foreach ($venta->detalles as $det) {
                    $producto = Producto::lockForUpdate()->find($det->id_producto);
                    $producto->stock = (int)$producto->stock + (int)$det->cantidad;
                    $producto->save();
                }
                $venta->estado = 'anulada';
                $venta->save();
            });
        } catch (Exception $e) {
            return back()->with('error','No se pudo anular: '.$e->getMessage());
        }
        return back()->with('success','Venta anulada');
    }

    /**
     * Genera la vista de factura/comprobante para una venta.
     *
     * @param Venta $venta
     * @return \Illuminate\View\View
     */
    public function factura(Venta $venta)
    {
        $venta->load(['cliente','usuario','detalles.producto','metodoPago']);
        return view('ventas.factura', compact('venta'));
    }
}
