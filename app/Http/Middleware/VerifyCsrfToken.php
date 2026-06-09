<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

/**
 * Class VerifyCsrfToken
 * 
 * Middleware para la verificación de tokens CSRF.
 * Extiende el middleware base de Laravel para validar que las solicitudes
 * POST, PUT, PATCH y DELETE incluyan un token CSRF válido.
 */
class VerifyCsrfToken extends Middleware
{
    /**
     * Las URIs que deben ser excluidas de la verificación CSRF.
     * 
     * Aquí se pueden agregar rutas que no requieren protección CSRF,
     * como webhooks de servicios externos o APIs públicas.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
    ];
}
