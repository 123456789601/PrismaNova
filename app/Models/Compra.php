<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Compra
 * 
 * Representa una adquisición de mercancía a un proveedor.
 * Registra el ingreso de stock y los costos asociados.
 *
 * @property int $id_compra Identificador único de la compra.
 * @property int $id_proveedor Proveedor al que se le compró.
 * @property int $id_usuario Usuario que registró la compra.
 * @property \Illuminate\Support\Carbon $fecha Fecha de la transacción.
 * @property float $subtotal Monto antes de impuestos.
 * @property float $impuesto Impuestos aplicados.
 * @property float $total Costo total de la adquisición.
 * @property string $estado Estado de la compra (completada, anulada).
 * @property-read \App\Models\Proveedor $proveedor Proveedor asociado.
 * @property-read \App\Models\Usuario $usuario Usuario responsable.
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\DetalleCompra[] $detalles Detalles de productos adquiridos.
 */
class Compra extends Model
{
    /**
     * Tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'compras';

    /**
     * Clave primaria de la tabla.
     *
     * @var string
     */
    protected $primaryKey = 'id_compra';

    /**
     * Atributos asignables masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'id_proveedor',
        'id_usuario',
        'fecha',
        'subtotal',
        'impuesto',
        'total',
        'estado',
    ];

    /**
     * Conversión de atributos a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'fecha' => 'datetime',
        'subtotal' => 'float',
        'impuesto' => 'float',
        'total' => 'float',
    ];

    /**
     * Relación: Una compra se realiza a un proveedor.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor', 'id_proveedor');
    }

    /**
     * Relación: Una compra es registrada por un usuario.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    /**
     * Relación: Una compra tiene múltiples detalles de productos.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function detalles()
    {
        return $this->hasMany(DetalleCompra::class, 'id_compra', 'id_compra');
    }
}
