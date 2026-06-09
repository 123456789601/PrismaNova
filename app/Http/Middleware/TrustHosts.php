<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustHosts as Middleware;

/**
 * Class TrustHosts
 * 
 * Middleware para configurar hosts de confianza.
 * Protege contra ataques de reenvío de host permitiendo solo
 * solicitudes desde hosts configurados como confiables.
 */
class TrustHosts extends Middleware
{
    /**
     * Obtiene los patrones de host que deben ser de confianza.
     * 
     * Por defecto, confía en todos los subdominios de la URL de la aplicación.
     * Esto es útil para aplicaciones con múltiples subdominios.
     *
     * @return array<int, string|null> Lista de patrones de hosts confiables.
     */
    public function hosts()
    {
        return [
            $this->allSubdomainsOfApplicationUrl(),
        ];
    }
}
