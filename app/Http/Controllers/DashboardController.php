<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Cliente;
use App\Models\Compra;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\InventoryUsageSync;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * DashboardController
 * 
 * Controlador principal para la vista del dashboard.
 * Gestiona la lógica de visualización de KPIs, gráficas y estadísticas
 * personalizadas según el rol del usuario (admin, cajero, bodeguero, cliente).
 */
class DashboardController extends Controller
{
    /**
     * Muestra el dashboard principal.
     * 
     * Calcula y cachea estadísticas clave para optimizar el rendimiento.
     * La cache se invalida cada 60 segundos o cambia según el usuario/rol.
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Obtener rol y usuario actual
        $rol = strtolower(trim(Auth::user()->rol->nombre ?? ''));
        $userId = Auth::user()->id_usuario ?? null;
        
        // Obtener filtro de cajero si existe (para vista de admin)
        $cajeroId = request()->get('cajero_id');
        
        // Generar clave de cache única basada en rol, usuario y minuto actual
        // Esto evita consultas excesivas a la base de datos
        $key = 'kpis_'.$rol.'_'.$userId.'_'.($cajeroId ? '_c'.$cajeroId : '').'_'.now()->format('Ymd_Hi');
        
        $stats = Cache::remember($key, 60, function () use ($rol, $userId, $cajeroId) {
            $s = [];
            
            // --- Lógica para ADMINISTRADORES ---
            if ($rol === 'admin') {
                // KPIs Globales del día
                $s['ventas_hoy_total'] = Venta::whereDate('fecha', today())->sum('total');
                $s['ventas_hoy_count'] = Venta::whereDate('fecha', today())->count();
                $s['compras_hoy_total'] = Compra::whereDate('fecha', today())->sum('total');
                $s['compras_hoy_count'] = Compra::whereDate('fecha', today())->count();
                
                // KPIs Globales del mes
                $startMonth = now()->startOfMonth();
                $endMonth = now()->endOfMonth();
                
                $s['ventas_mes_total'] = Venta::whereBetween('fecha', [$startMonth, $endMonth])->sum('total');
                $s['compras_mes_total'] = Compra::whereBetween('fecha', [$startMonth, $endMonth])->sum('total');
                
                // Alertas de inventario y sincronización
                $s['stock_bajo'] = Producto::whereColumn('stock','<=','stock_minimo')->count();
                $s['productos_stock_bajo'] = Producto::whereColumn('stock','<=','stock_minimo')
                    ->select('id_producto','nombre','stock','stock_minimo')
                    ->orderBy('stock','asc')
                    ->limit(5)
                    ->get();
                
                $s['ultima_sync'] = InventoryUsageSync::max('applied_at') ?? InventoryUsageSync::max('created_at');
                
                // Ultimas 5 ventas
                $s['ultimas_ventas'] = Venta::with('cliente')
                    ->orderBy('fecha','desc')
                    ->limit(5)
                    ->get();
                
                // --- Gráfica Global de Ventas Diarias (Mes Actual) ---
                $rangos = Venta::selectRaw('DATE(fecha) as d, SUM(total) as t')
                    ->whereBetween('fecha', [$startMonth, $endMonth])
                    ->groupBy('d')->orderBy('d')->get();
                
                $dias = [];
                $serieDias = [];
                $cursor = $startMonth->copy();
                
                // Rellenar días sin ventas con 0 para mantener la continuidad de la gráfica
                while ($cursor->lte($endMonth)) {
                    $label = $cursor->format('Y-m-d');
                    $dias[] = $cursor->format('d'); // Etiqueta eje X (día del mes)
                    $serieDias[] = (float) optional($rangos->firstWhere('d', $label))->t ?? 0.0;
                    $cursor->addDay();
                }
                $s['labels_dias'] = $dias;
                $s['serie_dias'] = $serieDias;

                // --- Top 5 Productos del Mes (Para Gráfica de Dona) ---
                $s['top_productos'] = DB::table('detalle_ventas')
                    ->join('ventas', 'detalle_ventas.id_venta', '=', 'ventas.id_venta')
                    ->join('productos', 'detalle_ventas.id_producto', '=', 'productos.id_producto')
                    ->select('productos.nombre', DB::raw('SUM(detalle_ventas.cantidad) as total_vendido'))
                    ->whereBetween('ventas.fecha', [$startMonth, $endMonth])
                    ->where('ventas.estado', '!=', 'anulada')
                    ->groupBy('productos.id_producto', 'productos.nombre')
                    ->orderByDesc('total_vendido')
                    ->limit(5)
                    ->get();
                
                // --- Gráfica Filtrada por Cajero (si se selecciona uno) ---
                if ($cajeroId) {
                    $rangosCajero = Venta::selectRaw('DATE(fecha) as d, SUM(total) as t')
                        ->whereBetween('fecha', [$startMonth, $endMonth])
                        ->where('id_usuario', $cajeroId)
                        ->groupBy('d')->orderBy('d')->get();
                        
                    $diasC = [];
                    $serieDiasC = [];
                    $cursorC = $startMonth->copy();
                    
                    while ($cursorC->lte($endMonth)) {
                        $labelC = $cursorC->format('Y-m-d');
                        $diasC[] = $cursorC->format('d');
                        $serieDiasC[] = (float) optional($rangosCajero->firstWhere('d', $labelC))->t ?? 0.0;
                        $cursorC->addDay();
                    }
                    $s['labels_dias_cajero'] = $diasC;
                    $s['serie_dias_cajero'] = $serieDiasC;
                }
                
                // --- Top Cajeros del Mes (Tabla de Rendimiento) ---
                $agg = Venta::selectRaw('id_usuario, COUNT(*) as c, SUM(total) as t')
                    ->whereBetween('fecha', [$startMonth, $endMonth])
                    ->whereNotNull('id_usuario')
                    ->groupBy('id_usuario')
                    ->orderByDesc('t')
                    ->limit(10)
                    ->get();
                    
                $users = Usuario::whereIn('id_usuario', $agg->pluck('id_usuario'))->get()->keyBy('id_usuario');
                $totalVentasMes = $s['ventas_mes_total'] > 0 ? $s['ventas_mes_total'] : 1; // Evitar división por cero

                $s['top_cajeros'] = $agg->map(function($a) use ($users, $totalVentasMes) {
                    $u = $users->get($a->id_usuario);
                    
                    // 1. Calcular porcentaje de contribución al total del mes
                    $porcentaje = ($a->t / $totalVentasMes) * 100;
                    
                    // 2. Calcular tendencia comparado con el mes anterior
                    $prevMonthStart = now()->subMonth()->startOfMonth();
                    $prevMonthEnd = now()->subMonth()->endOfMonth();
                    
                    $prevTotal = Venta::where('id_usuario', $a->id_usuario)
                        ->whereBetween('fecha', [$prevMonthStart, $prevMonthEnd])
                        ->sum('total');
                        
                    $tendencia = 0;
                    if ($prevTotal > 0) {
                        // Fórmula de variación porcentual: ((Actual - Anterior) / Anterior) * 100
                        $tendencia = (($a->t - $prevTotal) / $prevTotal) * 100;
                    } elseif ($a->t > 0) {
                        // Si no vendió nada el mes pasado y ahora sí, es un aumento del 100% (simbólico)
                        $tendencia = 100; 
                    }
                    
                    return [
                        'id_usuario' => $a->id_usuario,
                        'nombre' => $u->nombre ?? '-',
                        'apellido' => $u->apellido ?? '',
                        'ventas' => (int) $a->c,
                        'total' => (float) $a->t,
                        'porcentaje' => round($porcentaje, 1),
                        'tendencia' => round($tendencia, 1),
                    ];
                });
            }
            
            // --- Lógica Común para ADMIN y CAJERO ---
            if (in_array($rol, ['admin','cajero'])) {
                // Asegurar que los datos básicos estén disponibles
                $s['ventas_hoy_total'] = $s['ventas_hoy_total'] ?? Venta::whereDate('fecha', today())->sum('total');
                $s['ventas_hoy_count'] = $s['ventas_hoy_count'] ?? Venta::whereDate('fecha', today())->count();
                $s['caja_abierta'] = Caja::where('estado','abierta')->exists();
                
                // --- Lógica Específica para CAJERO ---
                if ($rol === 'cajero') {
                    // Gráfica de ventas por hora (solo mis ventas)
                    $horas = Venta::selectRaw('HOUR(fecha) as h, SUM(total) as t')
                        ->whereDate('fecha', today())
                        ->where('id_usuario', $userId)
                        ->groupBy('h')->orderBy('h')->get();
                        
                    $labelsHoras = [];
                    $serieHoras = [];
                    // Iterar las 24 horas del día
                    for ($i=0; $i<24; $i++) {
                        $labelsHoras[] = str_pad($i,2,'0',STR_PAD_LEFT) . ':00';
                        $serieHoras[] = (float) optional($horas->firstWhere('h', $i))->t ?? 0.0;
                    }
                    $s['labels_horas'] = $labelsHoras;
                    $s['serie_horas'] = $serieHoras;
                    
                    // KPIs personales del día
                    $s['ventas_hoy_usuario_total'] = Venta::whereDate('fecha', today())->where('id_usuario',$userId)->sum('total');
                    $s['ventas_hoy_usuario_count'] = Venta::whereDate('fecha', today())->where('id_usuario',$userId)->count();
                    
                    // Top productos vendidos hoy por este cajero
                    $s['mis_top_productos_hoy'] = Venta::join('detalle_ventas', 'ventas.id_venta', '=', 'detalle_ventas.id_venta')
                        ->join('productos', 'detalle_ventas.id_producto', '=', 'productos.id_producto')
                        ->where('ventas.id_usuario', $userId)
                        ->whereDate('ventas.fecha', today())
                        ->select('productos.nombre as producto', DB::raw('sum(detalle_ventas.cantidad) as cantidad'))
                        ->groupBy('productos.nombre')
                        ->orderByDesc('cantidad')
                        ->limit(5)
                        ->get();
                }
            }
            
            // --- Lógica para ADMIN y BODEGUERO ---
            if (in_array($rol, ['admin','bodeguero'])) {
                $s['stock_bajo'] = $s['stock_bajo'] ?? Producto::whereColumn('stock','<=','stock_minimo')->count();
                $s['productos_stock_bajo'] = $s['productos_stock_bajo'] ?? Producto::whereColumn('stock','<=','stock_minimo')
                    ->select('id_producto','nombre','stock','stock_minimo')
                    ->orderBy('stock','asc')
                    ->limit(5)
                    ->get();
                    
                $s['productos_total'] = Producto::count();
                $s['compras_mes_total'] = $s['compras_mes_total'] ?? Compra::whereBetween('fecha', [now()->startOfMonth(), now()->endOfMonth()])->sum('total');
            }
            
            // --- Lógica para CLIENTE ---
            if ($rol === 'cliente') {
                $u = Auth::user();
                $cliente = Cliente::where('email', $u->email)->orWhere('documento', $u->documento)->first();
                if ($cliente) {
                    $s['mis_compras'] = Venta::where('id_cliente', $cliente->id_cliente)->count();
                    $s['gasto_mes'] = Venta::where('id_cliente', $cliente->id_cliente)
                        ->whereBetween('fecha', [now()->startOfMonth(), now()->endOfMonth()])
                        ->sum('total');
                } else {
                    $s['mis_compras'] = 0;
                    $s['gasto_mes'] = 0;
                }
            }
            
            return $s;
        });
        
        // Obtener lista de cajeros para el filtro (solo admin)
        $cajeros = $rol === 'admin' ? Usuario::whereHas('rol', function($q) {
            $q->where('nombre', 'cajero');
        })->orderBy('nombre')->get() : collect();

        // Obtener sugerencias personalizadas para el cliente (basado en sus compras)
        $sugerencias = collect();
        if ($rol === 'cliente') {
            $cliente = Cliente::where('email', Auth::user()->email)->orWhere('documento', Auth::user()->documento)->first();
            if ($cliente) {
                // Obtener categorías que el cliente ha comprado más
                $idsCategorias = DB::table('detalle_ventas')
                    ->join('ventas', 'detalle_ventas.id_venta', '=', 'ventas.id_venta')
                    ->join('productos', 'detalle_ventas.id_producto', '=', 'productos.id_producto')
                    ->where('ventas.id_cliente', $cliente->id_cliente)
                    ->distinct()
                    ->pluck('productos.id_categoria');

                if ($idsCategorias->isNotEmpty()) {
                    $sugerencias = Producto::whereIn('id_categoria', $idsCategorias)
                        ->where('stock', '>', 0)
                        ->where('estado', 'activo')
                        ->inRandomOrder()
                        ->take(4)
                        ->get();
                } else {
                    // Si no ha comprado nada, sugerir productos aleatorios populares
                    $sugerencias = Producto::where('stock', '>', 0)
                        ->where('estado', 'activo')
                        ->inRandomOrder()
                        ->take(4)
                        ->get();
                }
            }
        }
        
        return view('dashboard', [
            'stats' => $stats,
            'rol' => $rol,
            'cajeros' => $cajeros,
            'cajero_id' => $cajeroId,
            'sugerencias' => $sugerencias,
        ]);
    }
}
