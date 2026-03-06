<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Venta
 * 
 * Representa una transacción de venta completada en el sistema.
 * Almacena totales, impuestos, descuentos y el estado de la transacción.
 *
 * @property int $id_venta Identificador único de la venta.
 * @property int|null $id_cliente ID del cliente (si está registrado).
 * @property int $id_usuario ID del usuario (cajero/vendedor) que procesó la venta.
 * @property \Illuminate\Support\Carbon $fecha Fecha y hora de la transacción.
 * @property float $subtotal Suma de precios unitarios por cantidad antes de descuentos.
 * @property float $descuento Total descontado (por promociones, cupones, etc.).
 * @property float $impuesto Impuestos aplicados.
 * @property float $total Monto final a pagar.
 * @property string $metodo_pago Nombre del método de pago (legacy/texto).
 * @property int|null $metodo_pago_id ID del método de pago normalizado.
 * @property string $estado Estado de la venta (completada, anulada, pendiente).
 * @property-read \App\Models\Cliente|null $cliente Cliente asociado.
 * @property-read \App\Models\Usuario $usuario Vendedor asociado.
 * @property-read \App\Models\MetodoPago|null $metodoPago Objeto del método de pago.
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\DetalleVenta[] $detalles Items individuales de la venta.
 */
class Venta extends Model
{
    /**
     * Tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'ventas';

    /**
     * Clave primaria de la tabla.
     *
     * @var string
     */
    protected $primaryKey = 'id_venta';

    /**
     * Atributos asignables masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'id_cliente',
        'id_usuario',
        'fecha',
        'subtotal',
        'descuento',
        'impuesto',
        'total',
        'metodo_pago',
        'metodo_pago_id',
        'monto_recibido',
        'cambio',
        'referencia_pago',
        'ultimos_digitos',
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
        'descuento' => 'float',
        'impuesto' => 'float',
        'total' => 'float',
    ];

    /**
     * Relación: Una venta pertenece a un cliente (opcional).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente', 'id_cliente');
    }

    /**
     * Relación: Una venta es procesada por un usuario (vendedor).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    /**
     * Relación: Una venta tiene múltiples detalles (productos vendidos).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class, 'id_venta', 'id_venta');
    }

    /**
     * Relación: Una venta tiene un método de pago asociado.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function metodoPago()
    {
        return $this->belongsTo(MetodoPago::class, 'metodo_pago_id', 'id_metodo_pago');
    }
}
