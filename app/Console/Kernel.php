<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

/**
 * Class Kernel
 * 
 * Kernel de consola de la aplicación.
 * Gestiona la programación de comandos y el registro de comandos personalizados.
 */
class Kernel extends ConsoleKernel
{
    /**
     * Define el cronograma de comandos de la aplicación.
     * 
     * Programa la ejecución automática de comandos de mantenimiento:
     * - Sincronización de uso de inventario cada 10 minutos
     * - Verificación de stock diariamente a las 08:00
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule Instancia del programador de tareas.
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('inventory:sync-usage')->everyTenMinutes();
        $schedule->command('stock:check')->dailyAt('08:00');
    }

    /**
     * Registra los comandos de la aplicación.
     * 
     * Carga automáticamente todos los comandos del directorio Commands
     * e incluye las rutas de consola definidas en routes/console.php.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
