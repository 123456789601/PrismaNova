<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\MetodoPago;
use App\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

/**
 * Class VentaApiController
 * 
 * Controlador API para la gestión de ventas.
 * Permite listar, crear y exportar ventas, así como calcular totales y descuentos.
 */
class VentaApiController extends Controller
{
    /**
     * Lista las ventas con filtros y resumen estadístico.
     * 
     * Devuelve un JSON con las ventas paginadas y un objeto 'resumen' con los totales
     * de las ventas filtradas (subtotal, descuentos, impuestos, total).
     *
     * @param Request $request Filtros: desde, hasta, estado, metodo_pago_id, cliente, usuario_id.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Verificar autenticación
        $user = Auth::user();
        if (!$user) {
            abort(401);
        }

        // Configurar filtros de fecha (por defecto mes actual)
        $desde = $request->query('desde') ? \Carbon\Carbon::parse($request->query('desde'))->startOfDay() : now()->startOfMonth();
        $hasta = $request->query('hasta') ? \Carbon\Carbon::parse($request->query('hasta'))->endOfDay() : now()->endOfMonth();
        
        // Obtener filtros opcionales
        $estado = $request->query('estado');
        $metodoPagoId = $request->query('metodo_pago_id');
        $cliente = $request->query('cliente');
        $usuarioId = $request->query('usuario_id');
        
        // Construir query base
        $q = Venta::with(['cliente','metodoPago'])
            ->whereBetween('fecha', [$desde, $hasta]);
        
        // Aplicar filtros dinámicos
        if ($estado) {
            $q->where('estado',$estado);
        }
        if ($metodoPagoId) {
            $q->where('metodo_pago_id',$metodoPagoId);
        }
        if ($cliente) {
            // Búsqueda flexible de cliente
            $q->whereHas('cliente', function($qq) use ($cliente) {
                $qq->where('nombre','like','%'.$cliente.'%')
                    ->orWhere('apellido','like','%'.$cliente.'%')
                    ->orWhere('documento','like','%'.$cliente.'%');
            });
        }
        if ($usuarioId) {
            $q->where('id_usuario',$usuarioId);
        }
        
        // Obtener resultados paginados para la tabla
        $ventas = $q->orderBy('id_venta','desc')->paginate(20);
        
        // Calcular resumen financiero sobre el total de registros filtrados (no solo la página actual)
        $resumen = [
            'subtotal' => (float) $q->sum('subtotal'),
            'descuento' => (float) $q->sum('descuento'),
            'impuesto' => (float) $q->sum('impuesto'),
            'total' => (float) $q->sum('total'),
            'conteo' => (int) $q->count(),
        ];
        
        // Retornar respuesta JSON
        return response()->json([
            'filtros' => [
                'desde' => $desde->format('Y-m-d'),
                'hasta' => $hasta->format('Y-m-d'),
                'estado' => $estado,
                'metodo_pago_id' => $metodoPagoId,
                'cliente' => $cliente,
                'usuario_id' => $usuarioId,
            ],
            'resumen' => $resumen,
            'data' => $ventas,
        ]);
    }

    /**
     * Exporta las ventas filtradas a CSV (versión API).
     * 
     * Similar a la exportación en ReporteController pero accesible vía API.
     *
     * @param Request $request Filtros aplicables.
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportCsv(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            abort(401);
        }
        
        // Configuración de filtros (idéntica a index)
        $desde = $request->query('desde') ? \Carbon\Carbon::parse($request->query('desde'))->startOfDay() : now()->startOfMonth();
        $hasta = $request->query('hasta') ? \Carbon\Carbon::parse($request->query('hasta'))->endOfDay() : now()->endOfMonth();
        $estado = $request->query('estado');
        $metodoPagoId = $request->query('metodo_pago_id');
        $cliente = $request->query('cliente');
        $usuarioId = $request->query('usuario_id');
        
        $q = Venta::with(['cliente','metodoPago','detalles'])->whereBetween('fecha', [$desde, $hasta]);
        
        if ($estado) {
            $q->where('estado',$estado);
        }
        if ($metodoPagoId) {
            $q->where('metodo_pago_id',$metodoPagoId);
        }
        if ($cliente) {
            $q->whereHas('cliente', function($qq) use ($cliente) {
                $qq->where('nombre','like','%'.$cliente.'%')
                    ->orWhere('apellido','like','%'.$cliente.'%')
                    ->orWhere('documento','like','%'.$cliente.'%');
            });
        }
        if ($usuarioId) {
            $q->where('id_usuario',$usuarioId);
        }
        
        $rows = $q->orderBy('id_venta','desc')->get();
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="ventas.csv"',
        ];
        
        return response()->streamDownload(function() use ($rows) {
            $out = fopen('php://output', 'w');
            fputs($out, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8
            fputcsv($out, ['ID','Fecha','Cliente','Total','Estado','Método Pago','Cantidad total','Descuento']);
            foreach ($rows as $v) {
                $cantidadTotal = $v->detalles->sum('cantidad');
                fputcsv($out, [
                    $v->id_venta,
                    optional($v->fecha)->format('Y-m-d H:i'),
                    optional($v->cliente)->nombre,
                    number_format($v->total,2,'.',''),
                    $v->estado,
                    optional($v->metodoPago)->nombre ?? $v->metodo_pago,
                    $cantidadTotal,
                    number_format($v->descuento,2,'.',''),
                ]);
            }
            fclose($out);
        }, 'ventas.csv', $headers);
    }

    /**
     * Calcula los totales de una venta incluyendo descuentos automáticos.
     * 
     * Reglas de negocio aplicadas:
     * 1. Descuento por volumen (línea): 5% si cantidad >= 12 unidades.
     * 2. Descuento por monto total (tiers):
     *    - 10% si subtotal >= 200
     *    - 5% si subtotal >= 100 y < 200
     *
     * @param array $items Lista de items con 'precio' y 'cantidad'.
     * @return array Array con 'subtotal', 'descuento', 'impuesto', 'total'.
     */
    public static function computeTotals(array $items): array
    {
        $subtotal = 0.0;
        $descuentoAutoLineas = 0.0;
        
        // Calcular subtotal base y descuentos por línea
        foreach ($items as $it) {
            $cantidad = (int)$it['cantidad'];
            $precio = (float)$it['precio'];
            $lineaSubtotal = $precio * $cantidad;
            $subtotal += $lineaSubtotal;
            
            // Regla: 5% descuento en items con docena o más
            if ($cantidad >= 12) {
                $descuentoAutoLineas += round($lineaSubtotal * 0.05, 2);
            }
        }
        
        // Calcular descuentos por monto total (escalonado)
        $descuentoAutoTiers = 0.0;
        if ($subtotal >= 200) {
            $descuentoAutoTiers = round($subtotal * 0.10, 2);
        } elseif ($subtotal >= 100) {
            $descuentoAutoTiers = round($subtotal * 0.05, 2);
        }
        
        $impuesto = 0.0; // Actualmente no se aplica impuesto adicional
        $descuento = $descuentoAutoLineas + $descuentoAutoTiers;
        $total = max(0, $subtotal - $descuento + $impuesto);
        
        return [
            'subtotal' => round($subtotal, 2),
            'descuento' => round($descuento, 2),
            'impuesto' => round($impuesto, 2),
            'total' => round($total, 2),
        ];
    }

    /**
     * Registra una nueva venta en el sistema.
     * 
     * Proceso transaccional que incluye:
     * 1. Validación de stock (lockeo pesimista).
     * 2. Cálculo de totales.
     * 3. Aplicación de cupones.
     * 4. Creación de registros Venta y DetalleVenta.
     * 5. Actualización de stock.
     * 6. Envío de correo electrónico (opcional).
     *
     * @param Request $request Datos de la venta (items, cliente, metodo_pago, cupon).
     * @return \Illuminate\Http\JsonResponse Venta creada o error.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            abort(401);
        }
        $rol = '';
        $rawRole = $user->getAttribute('rol');
        if (is_string($rawRole) && trim($rawRole) !== '') {
            $rol = $rawRole;
        } else {
            $rolName = optional($user->rol)->nombre;
            if (is_string($rolName) && trim($rolName) !== '') {
                $rol = $rolName;
            }
        }
        $rol = strtolower(trim($rol));
        
        // Validación de datos de entrada
        $data = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id_producto' => 'required|integer|exists:productos,id_producto',
            'items.*.cantidad' => 'required|integer|min:1',
            'metodo_pago' => 'required|string|max:20',
            'referencia_pago' => 'nullable|string|max:50',
            'ultimos_digitos' => 'nullable|string|size:4',
            'id_cliente' => 'nullable|integer|exists:clientes,id_cliente',
            'cupon' => 'nullable|string|max:50',
        ]);

        $metodoRaw = strtolower(trim((string) ($data['metodo_pago'] ?? '')));
        $allowed = $rol === 'cliente'
            ? ['tarjeta', 'transferencia']
            : ['efectivo', 'tarjeta', 'transferencia'];

        if (!in_array($metodoRaw, $allowed, true)) {
            return response()->json(['error' => 'Método de pago no permitido.'], 422);
        }

        $metodoPagoNombre = [
            'efectivo' => 'Efectivo',
            'tarjeta' => 'Tarjeta',
            'transferencia' => 'Transferencia',
        ][$metodoRaw] ?? (string) ($data['metodo_pago'] ?? '');

        if ($metodoRaw === 'tarjeta') {
            if (empty($data['referencia_pago']) || empty($data['ultimos_digitos'])) {
                return response()->json(['error' => 'Faltan datos de tarjeta.'], 422);
            }
        }
        if ($metodoRaw === 'transferencia') {
            if (empty($data['referencia_pago'])) {
                return response()->json(['error' => 'Ingrese la referencia o comprobante de la transferencia.'], 422);
            }
            $data['ultimos_digitos'] = null;
        }

        // Determinar ID del cliente según el rol del usuario actual
        $idCliente = null;
        if ($rol === 'cliente') {
            $cli = Cliente::where('email', $user->email)
                ->orWhere('documento', $user->documento)
                ->first();

            if (!$cli) {
                $documento = (string) ($user->documento ?? '');
                if ($documento === '' || $user->email === null) {
                    return response()->json(['error' => 'No se pudo asociar el cliente a la venta.'], 422);
                }

                try {
                    $cli = Cliente::create([
                        'nombre' => (string) ($user->nombre ?? 'Cliente'),
                        'apellido' => (string) ($user->apellido ?? ''),
                        'documento' => $documento,
                        'telefono' => null,
                        'direccion' => null,
                        'email' => (string) $user->email,
                        'estado' => 'activo',
                    ]);
                } catch (\Throwable $e) {
                    $cli = Cliente::where('email', $user->email)
                        ->orWhere('documento', $user->documento)
                        ->first();
                }
            }

            if (!$cli) {
                return response()->json(['error' => 'No se pudo asociar el cliente a la venta.'], 422);
            }

            $idCliente = $cli->id_cliente;
        } else {
            // Si es personal, se usa el cliente enviado en el request
            $idCliente = $data['id_cliente'] ?? null;
            if (!$idCliente) {
                return response()->json(['error' => 'Debe seleccionar un cliente para registrar la venta.'], 422);
            }
        }

        DB::beginTransaction();
        try {
            $detalles = [];
            $itemsCalculo = [];
            
            // Procesar cada item: validar stock y preparar datos
            foreach ($data['items'] as $item) {
                // Bloqueo pesimista para evitar condiciones de carrera en stock
                $producto = Producto::lockForUpdate()->findOrFail($item['id_producto']);
                
                if ($producto->stock < $item['cantidad']) {
                    throw new \RuntimeException('Stock insuficiente para producto '.$producto->nombre);
                }
                
                $precio = $producto->precio_venta;
                $itemsCalculo[] = ['precio' => $precio, 'cantidad' => $item['cantidad']];
                $detalles[] = [
                    'producto' => $producto,
                    'cantidad' => $item['cantidad'],
                    'precio' => $precio,
                    'subtotal' => $precio * $item['cantidad'],
                ];
                
                // Decrementar stock
                $producto->decrement('stock', $item['cantidad']);
            }
            
            // Calcular totales base
            $tot = self::computeTotals($itemsCalculo);
            
            // Aplicar cupón si existe
            if (!empty($data['cupon'])) {
                $cupon = \App\Models\Cupon::where('codigo', $data['cupon'])->first();
                if ($cupon && $cupon->esValido()) {
                    $extra = $cupon->tipo === 'porcentaje'
                        ? round($tot['subtotal'] * ($cupon->valor / 100), 2)
                        : (float)$cupon->valor;
                        
                    $tot['descuento'] = round($tot['descuento'] + $extra, 2);
                    $tot['total'] = max(0, $tot['subtotal'] - $tot['descuento'] + $tot['impuesto']);
                    
                    // Registrar uso del cupón
                    $cupon->increment('usos');
                }
            }

            // Crear registro de Venta
            $metodoPagoId = null;
            try {
                $metodoPagoId = MetodoPago::where('nombre', $metodoPagoNombre)->value('id_metodo_pago');
            } catch (\Throwable $e) {
                $metodoPagoId = null;
            }

            $venta = Venta::create([
                'id_cliente' => $idCliente,
                'id_usuario' => $user->id_usuario,
                'fecha' => now(),
                'subtotal' => $tot['subtotal'],
                'descuento' => $tot['descuento'],
                'impuesto' => $tot['impuesto'],
                'total' => $tot['total'],
                'metodo_pago' => $metodoPagoNombre,
                'metodo_pago_id' => $metodoPagoId,
                'referencia_pago' => $data['referencia_pago'] ?? null,
                'ultimos_digitos' => $data['ultimos_digitos'] ?? null,
                'estado' => 'completada',
            ]);

            // Crear detalles de venta
            foreach ($detalles as $d) {
                DetalleVenta::create([
                    'id_venta' => $venta->id_venta,
                    'id_producto' => $d['producto']->id_producto,
                    'cantidad' => $d['cantidad'],
                    'precio_unitario' => $d['precio'],
                    'subtotal' => $d['subtotal'],
                ]);
            }

            Bitacora::registrar('CREATE', 'ventas', $venta->id_venta, 'Venta creada via API');

            DB::commit();
            if ($request->hasSession()) {
                $request->session()->forget('cart');
            }
            
            // Cargar relaciones para la respuesta
            $venta->load('detalles');
            
            // Intentar enviar correo de confirmación (no bloqueante)
            try {
                $to = null;
                if ($idCliente) {
                    $cli = \App\Models\Cliente::find($idCliente);
                    $to = $cli->email ?? null;
                }
                if ($to) {
                    $url = route('mis-compras.show', ['venta' => $venta->id_venta]);
                    Mail::raw("Gracias por tu compra. Puedes ver tu comprobante aquí: {$url}", function ($m) use ($to, $venta) {
                        $m->to($to)->subject('Factura de compra #'.$venta->id_venta);
                    });
                }
            } catch (\Throwable $mailE) {
                // Silenciar errores de envío de correo para no fallar la transacción
            }
            
            return response()->json($venta, 201);
            
        } catch (\Throwable $e) {
            DB::rollBack();
            // Retornar error controlado
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
