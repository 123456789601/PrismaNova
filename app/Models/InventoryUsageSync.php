<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class InventoryUsageSync
 * 
 * Modelo técnico para registrar sincronizaciones de uso de inventario.
 * Utilizado para integraciones con sistemas externos o procesos en lote.
 *
 * @property int $id Identificador automático.
 * @property string $external_id Identificador en el sistema externo.
 * @property array $payload Datos JSON recibidos/enviados.
 * @property \Illuminate\Support\Carbon|null $applied_at Fecha de aplicación del cambio.
 */
class InventoryUsageSync extends Model
{
    /**
     * Tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'inventory_usage_syncs';

    /**
     * Atributos asignables masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'external_id',
        'payload',
        'applied_at',
    ];

    /**
     * Conversión de atributos a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'payload' => 'array',
        'applied_at' => 'datetime',
    ];
}
