<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class RedirectIfAuthenticated
 * 
 * Middleware para redirigir usuarios ya autenticados.
 * Evita que usuarios logueados accedan a páginas de login o registro.
 */
class RedirectIfAuthenticated
{
    /**
     * Maneja una solicitud entrante.
     * 
     * Verifica si el usuario está autenticado en alguno de los guards especificados.
     * Si está autenticado, lo redirige a la página principal (HOME).
     *
     * @param  \Illuminate\Http\Request  $request Solicitud HTTP entrante.
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next Siguiente middleware en la cadena.
     * @param  string|null  ...$guards Guards de autenticación a verificar (web, api, etc).
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
}
