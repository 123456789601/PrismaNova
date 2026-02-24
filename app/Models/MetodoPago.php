<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MetodoPago
 * 
 * Representa los diferentes métodos de pago aceptados en el sistema (Efectivo, Tarjeta, Yape, etc.).
 *
 * @property int $id_metodo_pago Identificador único del método de pago.
 * @property string $nombre Nombre del método de pago.
 * @property string $estado Estado del método (activo, inactivo).
 */
class MetodoPago extends Model
{
    /**
     * Tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'metodos_pago';

    /**
     * Clave primaria de la tabla.
     *
     * @var string
     */
    protected $primaryKey = 'id_metodo_pago';

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
    protected $fillable = ['nombre','estado'];
}
