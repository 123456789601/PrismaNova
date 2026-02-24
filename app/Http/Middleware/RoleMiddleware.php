<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

/**
 * Class RoleMiddleware
 * 
 * Middleware para la verificación de roles de usuario.
 * Restringe el acceso a rutas basándose en el rol del usuario autenticado.
 */
class RoleMiddleware
{
    /**
     * Maneja la petición entrante.
     * 
     * Verifica si el usuario está autenticado y si su rol coincide con alguno de los permitidos.
     * Si no tiene permiso, aborta con error 403 (Forbidden).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles  Lista de roles permitidos (varargs)
     * @return mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        $user = Auth::user();
        if (!in_array($user->rol, $roles, true)) {
            abort(403);
        }
        return $next($request);
    }
}
