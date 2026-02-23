<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Cliente;
use App\Models\Compra;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\InventoryUsageSync;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        $rol = Auth::user()->rol ?? null;
        $key = 'kpis_'.$rol.'_'.now()->format('Ymd_Hi');
        $stats = Cache::remember($key, 60, function () use ($rol) {
            $s = [];
            if ($rol === 'admin') {
                $s['ventas_hoy_total'] = Venta::whereDate('fecha', today())->sum('total');
                $s['ventas_hoy_count'] = Venta::whereDate('fecha', today())->count();
                $s['compras_hoy_total'] = Compra::whereDate('fecha', today())->sum('total');
                $s['compras_hoy_count'] = Compra::whereDate('fecha', today())->count();
                $s['ventas_mes_total'] = Venta::whereBetween('fecha', [now()->startOfMonth(), now()->endOfMonth()])->sum('total');
                $s['compras_mes_total'] = Compra::whereBetween('fecha', [now()->startOfMonth(), now()->endOfMonth()])->sum('total');
                $s['stock_bajo'] = Producto::whereColumn('stock','<=','stock_minimo')->count();
                $s['ultima_sync'] = InventoryUsageSync::max('applied_at') ?? InventoryUsageSync::max('created_at');
            }
            if (in_array($rol, ['admin','cajero'])) {
                $s['ventas_hoy_total'] = $s['ventas_hoy_total'] ?? Venta::whereDate('fecha', today())->sum('total');
                $s['ventas_hoy_count'] = $s['ventas_hoy_count'] ?? Venta::whereDate('fecha', today())->count();
                $s['caja_abierta'] = Caja::where('estado','abierta')->exists();
            }
            if (in_array($rol, ['admin','bodeguero'])) {
                $s['stock_bajo'] = $s['stock_bajo'] ?? Producto::whereColumn('stock','<=','stock_minimo')->count();
                $s['productos_total'] = Producto::count();
                $s['compras_mes_total'] = $s['compras_mes_total'] ?? Compra::whereBetween('fecha', [now()->startOfMonth(), now()->endOfMonth()])->sum('total');
            }
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
        return view('dashboard', compact('stats','rol'));
    }
}
