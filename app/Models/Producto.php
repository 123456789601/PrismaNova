<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Producto
 * 
 * Representa un producto en el inventario del sistema.
 * Gestiona la información de precios, stock, y relaciones con categorías y proveedores.
 *
 * @property int $id_producto Identificador único del producto.
 * @property string|null $codigo_barras Código de barras único del producto.
 * @property string $nombre Nombre comercial del producto.
 * @property string|null $descripcion Descripción detallada.
 * @property string|null $imagen Ruta relativa de la imagen almacenada.
 * @property int $id_categoria ID de la categoría a la que pertenece.
 * @property int|null $id_proveedor ID del proveedor asociado.
 * @property float $precio_compra Costo de adquisición.
 * @property float $precio_venta Precio de venta al público.
 * @property int $stock Cantidad actual en inventario.
 * @property int|null $stock_minimo Cantidad mínima recomendada antes de reponer.
 * @property string|null $fecha_vencimiento Fecha de caducidad del producto.
 * @property string $estado Estado del producto (activo/inactivo).
 * @property-read string|null $imagen_url URL completa para acceder a la imagen.
 * @property-read \App\Models\Categoria $categoria Categoría asociada.
 * @property-read \App\Models\Proveedor|null $proveedor Proveedor asociado.
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\DetalleCompra[] $detallesCompra Detalles de compras donde aparece este producto.
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\DetalleVenta[] $detallesVenta Detalles de ventas donde aparece este producto.
 */
class Producto extends Model
{
    use SoftDeletes;

    /**
     * Tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'productos';

    /**
     * Clave primaria de la tabla.
     *
     * @var string
     */
    protected $primaryKey = 'id_producto';

    protected $fillable = [
        'codigo_barras',
        'nombre',
        'descripcion',
        'imagen',
        'id_categoria',
        'id_proveedor',
        'precio_compra',
        'precio_venta',
        'stock',
        'stock_minimo',
        'fecha_vencimiento',
        'estado',
    ];

    /**
     * Atributos que deben ser mutados a fechas.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Atributos adicionales que se agregan a la serialización del modelo (JSON).
     *
     * @var array
     */
    protected $appends = ['imagen_url'];

    /**
     * Relación: Un producto pertenece a una categoría.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'id_categoria', 'id_categoria');
    }

    /**
     * Relación: Un producto pertenece opcionalmente a un proveedor.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor', 'id_proveedor');
    }

    /**
     * Relación: Un producto puede estar en múltiples detalles de compra.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function detallesCompra()
    {
        return $this->hasMany(DetalleCompra::class, 'id_producto', 'id_producto');
    }

    /**
     * Relación: Un producto puede estar en múltiples detalles de venta.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function detallesVenta()
    {
        return $this->hasMany(DetalleVenta::class, 'id_producto', 'id_producto');
    }

    /**
     * Obtener el nombre de la clave de ruta para el binding implícito.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return $this->primaryKey;
    }

    /**
     * Accesor: Obtener la URL completa de la imagen.
     * Si no hay imagen, retorna null.
     *
     * @return string|null
     */
    public function getImagenUrlAttribute()
    {
        if (!$this->imagen) return null;
        return asset('storage/'.$this->imagen);
    }
}
