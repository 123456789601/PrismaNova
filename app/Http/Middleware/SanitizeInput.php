<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Class SanitizeInput
 * 
 * Middleware para la limpieza y sanitización automática de los inputs de la petición.
 * Elimina etiquetas HTML y espacios en blanco innecesarios de los campos de texto.
 */
class SanitizeInput
{
    /**
     * Campos que deben ser excluidos de la sanitización.
     * 
     * @var array
     */
    protected array $except = [
        'password','password_confirmation','_token',
    ];

    /**
     * Procesa la petición y limpia los datos de entrada.
     * 
     * Recorre recursivamente todos los inputs y aplica trim() y strip_tags()
     * a los valores de tipo string que no estén en la lista de exclusión.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $input = $request->all();
        array_walk_recursive($input, function (&$value, $key) {
            if (is_string($value) && !in_array($key, $this->except, true)) {
                $value = trim(strip_tags($value));
            }
        });
        $request->merge($input);
        return $next($request);
    }
}
