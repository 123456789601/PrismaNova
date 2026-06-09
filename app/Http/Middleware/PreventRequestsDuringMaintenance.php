<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance as Middleware;

/**
 * Class PreventRequestsDuringMaintenance
 * 
 * Middleware para prevenir solicitudes durante el modo de mantenimiento.
 * Extiende el middleware base de Laravel para permitir acceso a ciertas rutas
 * incluso cuando la aplicación está en mantenimiento.
 */
class PreventRequestsDuringMaintenance extends Middleware
{
    /**
     * Las URIs que deben ser accesibles mientras el modo de mantenimiento está activado.
     * 
     * Aquí se pueden agregar rutas que deben seguir funcionando durante el mantenimiento,
     * como endpoints de health checks o webhooks críticos.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
    ];
}
