<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Compra;
use App\Models\InventoryUsageSync;
use App\Models\MetodoPago;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;

/**
 * Class ReporteController
 * 
 * Controlador encargado de la generación de reportes, visualización de métricas
 * y exportación de datos de ventas.
 */
class ReporteController extends Controller
{
    /**
     * Muestra la vista principal de reportes con métricas y filtros.
     * 
     * Este método recopila información sobre ventas y compras basándose en los filtros proporcionados
     * (rango de fechas, estado, método de pago, cliente, cajero).
     * Calcula totales, conteos y productos más vendidos para el dashboard de reportes.
     *
     * @return \Illuminate\View\View Vista con los datos del reporte.
     */
    public function index()
    {
        // Obtener la instancia del request global
        $request = request();
        
        // Configurar rango de fechas (por defecto: mes actual)
        // Se usa startOfDay() y endOfDay() para cubrir el rango completo de horas
        $desde = $request->get('desde') ? \Carbon\Carbon::parse($request->get('desde'))->startOfDay() : now()->startOfMonth();
        $hasta = $request->get('hasta') ? \Carbon\Carbon::parse($request->get('hasta'))->endOfDay() : now()->endOfMonth();
        
        // Obtener filtros opcionales
        $estado = $request->get('estado');
        $metodoPagoId = $request->get('metodo_pago_id');
        $cliente = $request->get('cliente');
        $cajeroId = $request->get('cajero_id');
        
        // Iniciar query base para ventas en el rango de fechas
        $ventasQuery = Venta::whereBetween('fecha', [$desde, $hasta]);
        
        // Aplicar filtros dinámicos si existen
        if ($estado) {
            $ventasQuery->where('estado', $estado);
        }
        if ($metodoPagoId) {
            $ventasQuery->where('metodo_pago_id', $metodoPagoId);
        }
        if ($cliente) {
            // Búsqueda flexible por nombre, apellido o documento del cliente
            $ventasQuery->whereHas('cliente', function($q) use ($cliente) {
                $q->where('nombre','like','%'.$cliente.'%')
                    ->orWhere('apellido','like','%'.$cliente.'%')
                    ->orWhere('documento','like','%'.$cliente.'%');
            });
        }
        if ($cajeroId) {
            $ventasQuery->where('id_usuario', $cajeroId);
        }
        
        // Query base para compras en el mismo rango de fechas
        $comprasQuery = Compra::whereBetween('fecha', [$desde, $hasta]);
        
        // Métricas rápidas del día actual
        $ventasHoy = Venta::whereDate('fecha', today())->sum('total');
        $comprasHoy = Compra::whereDate('fecha', today())->sum('total');
        
        // Calcular totales para el rango seleccionado
        // Se clona el query para no modificar la instancia original y poder reutilizarla
        $ventasRango = (clone $ventasQuery)->sum('total');
        $comprasRango = (clone $comprasQuery)->sum('total');
        $ventasCountRango = (clone $ventasQuery)->count();
        
        // Contar clientes únicos que compraron en el rango
        $clientesConCompraRango = (clone $ventasQuery)->distinct('id_cliente')->count('id_cliente');
        
        // Obtener el top 5 de productos más vendidos en el rango con los filtros aplicados
        // Se utiliza DB::table para un join más eficiente y agregación manual
        $topProductosMes = DB::table('detalle_ventas')
            ->join('ventas','detalle_ventas.id_venta','=','ventas.id_venta')
            ->join('productos','detalle_ventas.id_producto','=','productos.id_producto')
            ->join('clientes','ventas.id_cliente','=','clientes.id_cliente')
            ->whereBetween('ventas.fecha', [$desde, $hasta])
            ->when($estado, function($q) use ($estado){
                $q->where('ventas.estado',$estado);
            })
            ->when($metodoPagoId, function($q) use ($metodoPagoId){
                $q->where('ventas.metodo_pago_id',$metodoPagoId);
            })
            ->when($cliente, function($q) use ($cliente){
                $q->where(function($qq) use ($cliente) {
                    $qq->where('clientes.nombre','like','%'.$cliente.'%')
                        ->orWhere('clientes.apellido','like','%'.$cliente.'%')
                        ->orWhere('clientes.documento','like','%'.$cliente.'%');
                });
            })
            ->when($cajeroId, function($q) use ($cajeroId){
                $q->where('ventas.id_usuario',$cajeroId);
            })
            ->where('ventas.estado','!=','anulada') // Excluir ventas anuladas del top
            ->select('productos.nombre as producto', DB::raw('SUM(detalle_ventas.cantidad) as cantidad'), DB::raw('SUM(detalle_ventas.subtotal) as total'))
            ->groupBy('productos.nombre')
            ->orderByDesc('cantidad') // Ordenar por cantidad vendida
            ->limit(5)
            ->get();
            
        // Obtener lista de ventas para la tabla (limitada a 50 recientes)
        $ventasList = (clone $ventasQuery)->with(['cliente','metodoPago','usuario'])->orderBy('id_venta','desc')->limit(50)->get();
        
        // Cargar datos auxiliares para los filtros de la vista
        $metodosPago = MetodoPago::orderBy('nombre')->get();
        $cajeros = Usuario::where('rol','cajero')->orderBy('nombre')->get();
        
        // Obtener fecha de última sincronización de inventario
        $ultimaSync = InventoryUsageSync::max('applied_at') ?? InventoryUsageSync::max('created_at');
        
        // Retornar vista con todos los datos
        return view('reportes.index', [
            'ventasHoy'=>$ventasHoy,
            'comprasHoy'=>$comprasHoy,
            'ventasMes'=>$ventasRango,
            'comprasMes'=>$comprasRango,
            'ventasCountMes'=>$ventasCountRango,
            'clientesConCompraMes'=>$clientesConCompraRango,
            'topProductosMes'=>$topProductosMes,
            'ultimaSync'=>$ultimaSync,
            'desde'=>$desde->format('Y-m-d'),
            'hasta'=>$hasta->format('Y-m-d'),
            'estado'=>$estado,
            'metodo_pago_id'=>$metodoPagoId,
            'cliente'=>$cliente,
            'cajero_id'=>$cajeroId,
            'metodosPago'=>$metodosPago,
            'cajeros'=>$cajeros,
            'ventasList'=>$ventasList,
        ]);
    }

    /**
     * Muestra el historial de logs de sincronización de inventario.
     *
     * @return \Illuminate\View\View
     */
    public function syncLogs()
    {
        $logs = InventoryUsageSync::orderBy('id','desc')->paginate(20);
        return view('reportes.sync', compact('logs'));
    }

    /**
     * Ejecuta manualmente la sincronización de uso de inventario.
     * Llama al comando de artisan 'inventory:sync-usage'.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function syncRun()
    {
        Artisan::call('inventory:sync-usage');
        return redirect()->route('reportes.sync')->with('success','Sincronización ejecutada');
    }

    /**
     * Exporta el listado de ventas a un archivo CSV.
     * 
     * Genera un archivo CSV descargable con los datos de las ventas filtradas.
     * Utiliza streamDownload para manejar eficientemente grandes volúmenes de datos sin cargar todo en memoria.
     *
     * @param Request $request Filtros aplicables a la exportación.
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportVentasCsv(Request $request)
    {
        // Configurar rango de fechas y filtros (idéntico al index)
        $desde = $request->get('desde') ? \Carbon\Carbon::parse($request->get('desde'))->startOfDay() : now()->startOfMonth();
        $hasta = $request->get('hasta') ? \Carbon\Carbon::parse($request->get('hasta'))->endOfDay() : now()->endOfMonth();
        $estado = $request->get('estado');
        $metodoPagoId = $request->get('metodo_pago_id');
        $cliente = $request->get('cliente');
        $cajeroId = $request->get('cajero_id');
        
        // Construir query con relaciones necesarias para el reporte
        $q = Venta::with(['cliente','metodoPago','detalles','usuario'])->whereBetween('fecha', [$desde, $hasta]);
        
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
        if ($cajeroId) {
            $q->where('id_usuario',$cajeroId);
        }
        
        // Obtener resultados ordenados
        $rows = $q->orderBy('id_venta','desc')->get();
        
        // Headers para forzar la descarga del CSV
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="ventas.csv"',
        ];
        
        // Generar stream de descarga
        return response()->streamDownload(function() use ($rows) {
            $out = fopen('php://output', 'w');
            // Agregar BOM para correcta visualización de caracteres especiales en Excel
            fputs($out, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Escribir cabeceras del CSV
            fputcsv($out, ['ID','Fecha','Cliente','Cajero','Total','Estado','Método Pago','Cantidad total','Descuento']);
            
            // Iterar y escribir cada venta
            foreach ($rows as $v) {
                $cantidadTotal = $v->detalles->sum('cantidad');
                fputcsv($out, [
                    $v->id_venta,
                    optional($v->fecha)->format('Y-m-d H:i'),
                    optional($v->cliente)->nombre,
                    optional($v->usuario)->nombre,
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
}
