<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Class SecurityHeaders
 * 
 * Middleware para añadir cabeceras de seguridad HTTP a todas las respuestas.
 * Protege contra ataques comunes como XSS, Clickjacking, MIME sniffing, etc.
 */
class SecurityHeaders
{
    /**
     * Añade cabeceras de seguridad a la respuesta HTTP.
     * 
     * X-Frame-Options: Previene clickjacking.
     * X-Content-Type-Options: Previene MIME sniffing.
     * Referrer-Policy: Controla la información de referido.
     * X-XSS-Protection: Deshabilita el filtro XSS obsoleto de navegadores.
     * Permissions-Policy: Restringe el acceso a características del navegador.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('X-XSS-Protection', '0');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
        return $response;
    }
}
