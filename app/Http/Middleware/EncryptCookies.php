<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

/**
 * Class EncryptCookies
 * 
 * Middleware para el cifrado de cookies.
 * Extiende el middleware base de Laravel para cifrar todas las cookies
 * excepto las especificadas en la lista de excepciones.
 */
class EncryptCookies extends Middleware
{
    /**
     * Los nombres de las cookies que no deben ser cifradas.
     * 
     * XSRF-TOKEN se excluye del cifrado porque Laravel necesita leerla
     * antes de descifrarla para validar el token CSRF.
     *
     * @var array<int, string>
     */
    protected $except = [
        'XSRF-TOKEN',
    ];
}
