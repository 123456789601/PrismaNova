<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Compra;
use App\Models\InventoryUsageSync;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class ReporteController extends Controller
{
    public function index()
    {
        $ventasHoy = Venta::whereDate('fecha', today())->sum('total');
        $comprasHoy = Compra::whereDate('fecha', today())->sum('total');
        $ventasMes = Venta::whereBetween('fecha', [now()->startOfMonth(), now()->endOfMonth()])->sum('total');
        $comprasMes = Compra::whereBetween('fecha', [now()->startOfMonth(), now()->endOfMonth()])->sum('total');
        $ultimaSync = InventoryUsageSync::max('applied_at') ?? InventoryUsageSync::max('created_at');
        return view('reportes.index', compact('ventasHoy','comprasHoy','ventasMes','comprasMes','ultimaSync'));
    }

    public function syncLogs()
    {
        $logs = InventoryUsageSync::orderBy('id','desc')->paginate(20);
        return view('reportes.sync', compact('logs'));
    }

    public function syncRun()
    {
        Artisan::call('inventory:sync-usage');
        return redirect()->route('reportes.sync')->with('success','Sincronización ejecutada');
    }
}
