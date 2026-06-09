<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TrimStrings as Middleware;

/**
 * Class TrimStrings
 * 
 * Middleware para eliminar espacios en blanco de los inputs.
 * Extiende el middleware base de Laravel para recortar automáticamente
 * los espacios en blanco al inicio y final de los valores de entrada.
 */
class TrimStrings extends Middleware
{
    /**
     * Los nombres de los atributos que no deben ser recortados.
     * 
     * Los campos de contraseña se excluyen para evitar alterar
     * contraseñas que intencionalmente puedan tener espacios.
     *
     * @var array<int, string>
     */
    protected $except = [
        'current_password',
        'password',
        'password_confirmation',
    ];
}
