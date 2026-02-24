<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DetalleCompra
 * 
 * Representa una línea individual dentro de una compra.
 * Registra el producto adquirido, la cantidad y el costo unitario.
 *
 * @property int $id_detalle_compra Identificador único del detalle.
 * @property int $id_compra ID de la compra padre.
 * @property int $id_producto ID del producto adquirido.
 * @property int $cantidad Unidades compradas.
 * @property float $precio_compra Costo unitario de adquisición.
 * @property float $subtotal Costo total de la línea (cantidad * precio_compra).
 * @property-read \App\Models\Compra $compra Compra a la que pertenece.
 * @property-read \App\Models\Producto $producto Producto adquirido.
 */
class DetalleCompra extends Model
{
    /**
     * Tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'detalle_compras';

    /**
     * Clave primaria de la tabla.
     *
     * @var string
     */
    protected $primaryKey = 'id_detalle_compra';

    /**
     * Atributos asignables masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'id_compra',
        'id_producto',
        'cantidad',
        'precio_compra',
        'subtotal',
    ];

    /**
     * Relación: Un detalle pertenece a una compra específica.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function compra()
    {
        return $this->belongsTo(Compra::class, 'id_compra', 'id_compra');
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
