<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\MetodoPago;
use App\Models\Bitacora;
use App\Models\Caja;
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
        if (!Caja::where('estado', 'abierta')->exists()) {
            return redirect()->route('caja.index')->with('error', 'Debe abrir una caja para realizar ventas.');
        }

        $clientes = Cliente::where('estado','activo')->orderBy('nombre')->get();
        $productos = Producto::where('estado','activo')->orderBy('nombre')->get();
        $metodos = MetodoPago::where('estado','activo')->orderBy('nombre')->get();
        return view('ventas.create', compact('clientes','productos','metodos'));
    }

    /**
     * Muestra la interfaz de Punto de Venta (POS).
     *
     * @return \Illuminate\View\View
     */
    public function pos()
    {
        if (!Caja::where('estado', 'abierta')->exists()) {
            return redirect()->route('caja.index')->with('error', 'Debe abrir una caja para realizar ventas.');
        }

        $clientes = Cliente::where('estado','activo')->orderBy('nombre')->get();
        // Cargar productos con relaciones si es necesario, e.g. categoría
        $productos = Producto::with('categoria')->where('estado','activo')->orderBy('nombre')->get();
        $categorias = \App\Models\Categoria::where('estado','activo')->orderBy('nombre')->get();
        $metodos = MetodoPago::where('estado','activo')->orderBy('nombre')->get();
        
        return view('ventas.pos', compact('clientes','productos','categorias','metodos'));
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
     * Devuelve las últimas 10 ventas del usuario actual (JSON).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function recent()
    {
        $ventas = Venta::with('cliente')
            ->where('id_usuario', Auth::id())
            ->orderBy('fecha', 'desc')
            ->limit(10)
            ->get();
            
        return response()->json($ventas);
    }

    /**
     * Muestra una versión imprimible (ticket) de la venta.
     * 
     * @param Venta $venta
     * @return \Illuminate\View\View
     */
    public function ticket(Venta $venta)
    {
        $venta->load(['cliente','usuario','detalles.producto','metodoPago']);
        return view('ventas.ticket', compact('venta'));
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
        if (!Caja::where('estado', 'abierta')->exists()) {
            return redirect()->route('caja.index')->with('error', 'Debe abrir una caja para realizar ventas.');
        }

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

                $montoRecibido = $data['monto_recibido'] ?? null;
                $cambio = null;
                $referencia = $data['referencia_pago'] ?? null;
                $ultimos = $data['ultimos_digitos'] ?? null;

                if ($montoRecibido && $metodoPagoNombre === 'Efectivo') {
                    $cambio = (float)$montoRecibido - $total;
                    if ($cambio < 0) $cambio = 0; // O lanzar excepción si es menor
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
                    'monto_recibido' => $montoRecibido,
                    'cambio' => $cambio,
                    'referencia_pago' => $referencia,
                    'ultimos_digitos' => $ultimos,
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
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al registrar la venta: '.$e->getMessage()
                ], 422);
            }
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
        
        if ($venta) {
            Bitacora::registrar('CREATE', 'ventas', $venta->id_venta, 'Venta registrada. Total: ' . $venta->total);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Venta registrada con éxito',
                'venta_id' => $venta->id_venta,
                'redirect' => route('ventas.show', $venta->id_venta)
            ]);
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
            if (request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'La venta ya está anulada'], 422);
            }
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
                
                Bitacora::registrar('UPDATE', 'ventas', $venta->id_venta, 'Venta anulada');
            });
        } catch (Exception $e) {
            if (request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'No se pudo anular: '.$e->getMessage()], 500);
            }
            return back()->with('error','No se pudo anular: '.$e->getMessage());
        }

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Venta anulada correctamente']);
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
