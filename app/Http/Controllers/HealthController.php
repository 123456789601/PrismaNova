<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

/**
 * Class HealthController
 * 
 * Controlador para el monitoreo de salud del sistema.
 * Proporciona información sobre el estado del servidor, base de datos, caché y logs.
 */
class HealthController extends Controller
{
    /**
     * Muestra el panel de salud del sistema.
     * 
     * Recopila información sobre:
     * - Versión de PHP y Laravel
     * - Estado de la base de datos y tamaño
     * - Espacio en disco
     * - Estado del caché
     * - Últimas líneas del log
     *
     * @return \Illuminate\View\View Vista con información de salud.
     */
    public function index()
    {
        $health = [];
        
        // Información del sistema
        $health['system'] = [
            'php_version' => phpversion(),
            'laravel_version' => app()->version(),
            'server_os' => php_uname('s') . ' ' . php_uname('r'),
            'server_ip' => request()->server('SERVER_ADDR') ?? '127.0.0.1',
            'timezone' => config('app.timezone'),
        ];

        // Base de datos
        try {
            $pdo = DB::connection()->getPdo();
            $health['database'] = 'OK';
            
            // Obtener tamaño de BD (específico de MySQL)
            $dbName = DB::connection()->getDatabaseName();
            $size = DB::select("SELECT sum(data_length + index_length) / 1024 / 1024 as size FROM information_schema.TABLES WHERE table_schema = ?", [$dbName]);
            $health['db_size'] = round($size[0]->size ?? 0, 2) . ' MB';
            
            // Obtener conteos de tablas
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

        // Espacio en disco
        $diskFree = disk_free_space(base_path());
        $diskTotal = disk_total_space(base_path());
        $health['disk'] = [
            'free' => $this->formatBytes($diskFree),
            'total' => $this->formatBytes($diskTotal),
            'percent' => round((($diskTotal - $diskFree) / $diskTotal) * 100, 2)
        ];

        // Caché
        try {
            Cache::put('health_check', true, 1);
            $health['cache'] = Cache::get('health_check') ? 'OK' : 'ERROR';
        } catch (\Exception $e) {
            $health['cache'] = 'ERROR: ' . $e->getMessage();
        }

        // Logs (últimas 50 líneas)
        $logFile = storage_path('logs/laravel.log');
        $logs = [];
        if (file_exists($logFile)) {
            $lines = file($logFile);
            $logs = array_slice($lines, -50);
            $logs = array_reverse($logs);
        }

        return view('admin.health', compact('health', 'logs'));
    }

    /**
     * Optimiza el sistema limpiando la caché.
     * 
     * Ejecuta el comando artisan optimize:clear para limpiar
     * caché de configuración, rutas, vistas, etc.
     *
     * @return \Illuminate\Http\RedirectResponse Redirección con mensaje de resultado.
     */
    public function optimize()
    {
        try {
            Artisan::call('optimize:clear');
            return back()->with('success', 'Sistema optimizado y caché limpia correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al optimizar: ' . $e->getMessage());
        }
    }

    /**
     * Formatea bytes a una unidad legible (KB, MB, GB, etc).
     *
     * @param  int  $bytes Cantidad de bytes.
     * @param  int  $precision Número de decimales.
     * @return string Valor formateado con unidad.
     */
    private function formatBytes($bytes, $precision = 2) { 
        $units = array('B', 'KB', 'MB', 'GB', 'TB'); 
    
        $bytes = max($bytes, 0); 
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
        $pow = min($pow, count($units) - 1); 
    
        $bytes /= pow(1024, $pow); 
    
        return round($bytes, $precision) . ' ' . $units[$pow]; 
    }
}
