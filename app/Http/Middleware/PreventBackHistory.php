<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Class PreventBackHistory
 * 
 * Middleware para prevenir la navegación hacia atrás después del cierre de sesión.
 * Establece cabeceras HTTP para deshabilitar el caché del navegador.
 */
class PreventBackHistory
{
    /**
     * Maneja la solicitud entrante y deshabilita el caché del navegador.
     * 
     * Establece cabeceras Cache-Control, Pragma y Expires para evitar que
     * el navegador guarde la página en caché, previniendo el acceso
     * a páginas protegidas después de cerrar sesión usando el botón "atrás".
     *
     * @param  \Illuminate\Http\Request  $request Solicitud HTTP entrante.
     * @param  \Closure  $next Siguiente middleware en la cadena.
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        $response->headers->set('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Fri, 01 Jan 1990 00:00:00 GMT');
        return $response;
    }
}
