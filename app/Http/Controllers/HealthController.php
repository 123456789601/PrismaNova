<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class HealthController extends Controller
{
    public function index()
    {
        $health = [];
        
        // System Info
        $health['system'] = [
            'php_version' => phpversion(),
            'laravel_version' => app()->version(),
            'server_os' => php_uname('s') . ' ' . php_uname('r'),
            'server_ip' => request()->server('SERVER_ADDR') ?? '127.0.0.1',
            'timezone' => config('app.timezone'),
        ];

        // Database
        try {
            $pdo = DB::connection()->getPdo();
            $health['database'] = 'OK';
            
            // Get DB Size (MySQL specific)
            $dbName = DB::connection()->getDatabaseName();
            $size = DB::select("SELECT sum(data_length + index_length) / 1024 / 1024 as size FROM information_schema.TABLES WHERE table_schema = ?", [$dbName]);
            $health['db_size'] = round($size[0]->size ?? 0, 2) . ' MB';
            
            // Get Table Counts
            $health['counts'] = [
                'users' => \App\Models\Usuario::count(),
                'products' => \App\Models\Producto::count(),
                'sales' => \App\Models\Venta::count(),
            ];
        } catch (\Exception $e) {
            $health['database'] = 'ERROR: ' . $e->getMessage();
            $health['db_size'] = 'Unknown';
            $health['counts'] = ['users' => 0, 'products' => 0, 'sales' => 0];
        }

        // Disk Space
        $diskFree = disk_free_space(base_path());
        $diskTotal = disk_total_space(base_path());
        $health['disk'] = [
            'free' => $this->formatBytes($diskFree),
            'total' => $this->formatBytes($diskTotal),
            'percent' => round((($diskTotal - $diskFree) / $diskTotal) * 100, 2)
        ];

        // Cache
        try {
            Cache::put('health_check', true, 1);
            $health['cache'] = Cache::get('health_check') ? 'OK' : 'ERROR';
        } catch (\Exception $e) {
            $health['cache'] = 'ERROR: ' . $e->getMessage();
        }

        // Logs (Last 50 lines)
        $logFile = storage_path('logs/laravel.log');
        $logs = [];
        if (file_exists($logFile)) {
            $lines = file($logFile);
            $logs = array_slice($lines, -50);
            $logs = array_reverse($logs);
        }

        return view('admin.health', compact('health', 'logs'));
    }

    public function optimize()
    {
        try {
            Artisan::call('optimize:clear');
            return back()->with('success', 'Sistema optimizado y caché limpia correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al optimizar: ' . $e->getMessage());
        }
    }

    private function formatBytes($bytes, $precision = 2) { 
        $units = array('B', 'KB', 'MB', 'GB', 'TB'); 
    
        $bytes = max($bytes, 0); 
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
        $pow = min($pow, count($units) - 1); 
    
        $bytes /= pow(1024, $pow); 
    
        return round($bytes, $precision) . ' ' . $units[$pow]; 
    }
}
