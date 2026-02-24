<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Cupon
 * 
 * Gestiona los cupones de descuento aplicables a las ventas.
 * Incluye lógica para validar la vigencia y límites de uso.
 *
 * @property int $id_cupon Identificador único del cupón.
 * @property string $codigo Código alfanumérico que el usuario ingresa.
 * @property string $tipo Tipo de descuento (porcentaje, monto fijo).
 * @property float $valor Valor del descuento.
 * @property \Illuminate\Support\Carbon|null $fecha_inicio Fecha desde la cual es válido.
 * @property \Illuminate\Support\Carbon|null $fecha_fin Fecha hasta la cual es válido.
 * @property string $estado Estado del cupón (activo, inactivo).
 * @property int|null $uso_maximo Límite de veces que se puede usar.
 * @property int $usos Cantidad de veces que ha sido usado.
 */
class Cupon extends Model
{
    /**
     * Tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'cupones';

    /**
     * Clave primaria de la tabla.
     *
     * @var string
     */
    protected $primaryKey = 'id_cupon';

    /**
     * Atributos asignables masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'codigo','tipo','valor','fecha_inicio','fecha_fin','estado','uso_maximo','usos'
    ];

    /**
     * Conversión de atributos a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'valor' => 'float',
    ];

    /**
     * Valida si el cupón puede ser aplicado en este momento.
     * Verifica estado, fechas y límites de uso.
     *
     * @return bool True si es válido, False si no.
     */
    public function esValido(): bool
    {
        if ($this->estado !== 'activo') return false;
        $now = now();
        if ($this->fecha_inicio && $now->lt($this->fecha_inicio)) return false;
        if ($this->fecha_fin && $now->gt($this->fecha_fin)) return false;
        if ($this->uso_maximo !== null && $this->usos >= $this->uso_maximo) return false;
        return true;
    }
}
