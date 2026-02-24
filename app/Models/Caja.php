<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Caja
 * 
 * Representa una sesión de caja (turno) en el punto de venta.
 * Controla la apertura, cierre y arqueo de dinero.
 *
 * @property int $id_caja Identificador único de la sesión de caja.
 * @property \Illuminate\Support\Carbon $fecha_apertura Fecha y hora de apertura.
 * @property float $monto_inicial Dinero base en caja al iniciar.
 * @property \Illuminate\Support\Carbon|null $fecha_cierre Fecha y hora de cierre.
 * @property float|null $monto_final Dinero contado al cerrar la caja.
 * @property string $estado Estado de la caja (abierta, cerrada).
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\MovimientoCaja[] $movimientos Movimientos financieros asociados a esta sesión.
 */
class Caja extends Model
{
    /**
     * Tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'caja';

    /**
     * Clave primaria de la tabla.
     *
     * @var string
     */
    protected $primaryKey = 'id_caja';

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
        'fecha_apertura',
        'monto_inicial',
        'fecha_cierre',
        'monto_final',
        'estado',
    ];

    /**
     * Conversión de atributos a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'fecha_apertura' => 'datetime',
        'fecha_cierre' => 'datetime',
        'monto_inicial' => 'float',
        'monto_final' => 'float',
    ];

    /**
     * Relación: Una caja tiene múltiples movimientos (ingresos/egresos).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function movimientos()
    {
        return $this->hasMany(MovimientoCaja::class, 'id_caja', 'id_caja');
    }
}
