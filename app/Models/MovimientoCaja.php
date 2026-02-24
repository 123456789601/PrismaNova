<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MovimientoCaja
 * 
 * Registra cada operación de entrada o salida de dinero de una caja.
 * Permite auditar el flujo de efectivo durante un turno.
 *
 * @property int $id_movimiento Identificador único del movimiento.
 * @property int $id_caja ID de la caja asociada.
 * @property string $tipo Tipo de movimiento (ingreso, egreso).
 * @property float $monto Cantidad de dinero movida.
 * @property string|null $descripcion Detalle o justificación del movimiento.
 * @property \Illuminate\Support\Carbon $fecha Momento exacto de la operación.
 * @property-read \App\Models\Caja $caja Caja afectada.
 */
class MovimientoCaja extends Model
{
    /**
     * Tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'movimientos_caja';

    /**
     * Clave primaria de la tabla.
     *
     * @var string
     */
    protected $primaryKey = 'id_movimiento';

    /**
     * Indica si el modelo gestiona marcas de tiempo (created_at, updated_at).
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Atributos asignables masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'id_caja',
        'tipo',
        'monto',
        'descripcion',
        'fecha',
    ];

    /**
     * Conversión de atributos a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'fecha' => 'datetime',
        'monto' => 'float',
    ];

    /**
     * Relación: Un movimiento pertenece a una caja.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function caja()
    {
        return $this->belongsTo(Caja::class, 'id_caja', 'id_caja');
    }
}
