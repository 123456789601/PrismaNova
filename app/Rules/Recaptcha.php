<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Http;

/**
 * Class Recaptcha
 * 
 * Regla de validación personalizada para verificar el token de reCAPTCHA de Google.
 * Valida que el usuario haya completado el desafío de reCAPTCHA correctamente.
 */
class Recaptcha implements Rule
{
    /**
     * Determina si la regla de validación pasa.
     * 
     * Envía el token a la API de Google para verificar su autenticidad.
     * Omite la validación si está en entorno de testing o si no están configuradas las claves.
     *
     * @param  string  $attribute Nombre del atributo being validated.
     * @param  mixed  $value Valor del token de reCAPTCHA.
     * @return bool True si la validación es exitosa, False en caso contrario.
     */
    public function passes($attribute, $value)
    {
        // Omitir validación si está en testing o las claves no están configuradas
        if (app()->environment('testing') || !config('services.recaptcha.secret')) {
            return true;
        }

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config('services.recaptcha.secret'),
            'response' => $value,
            'remoteip' => request()->ip(),
        ]);

        return $response->json()['success'] ?? false;
    }

    /**
     * Obtiene el mensaje de error de validación.
     *
     * @return string Mensaje de error en español.
     */
    public function message()
    {
        return 'La verificación de reCAPTCHA falló. Por favor intenta de nuevo.';
    }
}
