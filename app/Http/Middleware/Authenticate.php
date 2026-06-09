<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

/**
 * Class Authenticate
 * 
 * Middleware de autenticación personalizado.
 * Extiende el middleware base de Laravel para redirigir usuarios no autenticados al login.
 */
class Authenticate extends Middleware
{
    /**
     * Obtiene la ruta a la que el usuario debe ser redirigido cuando no está autenticado.
     *
     * @param  \Illuminate\Http\Request  $request Solicitud HTTP entrante.
     * @return string|null Ruta de login o null si es una petición JSON.
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('login');
        }
    }
}
