<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DetalleVenta
 * 
 * Representa una línea individual dentro de una venta.
 * Vincula un producto con la cantidad vendida y el precio aplicado en ese momento.
 *
 * @property int $id_detalle Identificador único del detalle.
 * @property int $id_venta ID de la venta a la que pertenece.
 * @property int $id_producto ID del producto vendido.
 * @property int $cantidad Número de unidades vendidas.
 * @property float $precio_unitario Precio por unidad al momento de la venta.
 * @property float $subtotal Monto total de la línea (cantidad * precio).
 * @property-read \App\Models\Venta $venta Venta padre.
 * @property-read \App\Models\Producto $producto Producto referenciado.
 */
class DetalleVenta extends Model
{
    /**
     * Tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'detalle_ventas';

    /**
     * Clave primaria de la tabla.
     *
     * @var string
     */
    protected $primaryKey = 'id_detalle';

    /**
     * Atributos asignables masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'id_venta',
        'id_producto',
        'cantidad',
        'precio_unitario',
        'subtotal',
    ];

    /**
     * Relación: Un detalle pertenece a una venta específica.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function venta()
    {
        return $this->belongsTo(Venta::class, 'id_venta', 'id_venta');
    }

    /**
     * Relación: Un detalle referencia a un producto específico.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }
}
