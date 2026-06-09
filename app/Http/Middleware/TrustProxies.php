<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

/**
 * Class TrustProxies
 * 
 * Middleware para configurar proxies de confianza.
 * Permite que la aplicación funcione correctamente detrás de balanceadores de carga
 * o proxies inversos (como Nginx, Apache, AWS ELB, etc).
 */
class TrustProxies extends Middleware
{
    /**
     * Los proxies de confianza para esta aplicación.
     * 
     * Si es null, se confía en todos los proxies. En producción debería
     * configurarse con las IPs específicas de los proxies.
     *
     * @var array<int, string>|string|null
     */
    protected $proxies;

    /**
     * Los encabezados que deben usarse para detectar proxies.
     * 
     * Define qué cabeceras HTTP deben ser confiables para determinar
     * la IP real del cliente, el protocolo (HTTP/HTTPS), el host y el puerto.
     *
     * @var int
     */
    protected $headers =
        Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_AWS_ELB;
}
