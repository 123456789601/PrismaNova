<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class VentaSuspendida
 * 
 * Modelo para las ventas suspendidas (en espera).
 * Permite guardar temporalmente el contenido del carrito para recuperarlo posteriormente.
 */
class VentaSuspendida extends Model
{
    use HasFactory;

    /**
     * Tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'ventas_suspendidas';

    /**
     * Clave primaria de la tabla.
     *
     * @var string
     */
    protected $primaryKey = 'id_venta_suspendida';

    /**
     * Atributos asignables masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'id_usuario',
        'id_cliente',
        'contenido',
        'total',
        'nota',
    ];

    /**
     * Conversión de atributos a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'contenido' => 'array',
        'total' => 'float',
    ];

    /**
     * Relación: Una venta suspendida pertenece a un usuario.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    /**
     * Relación: Una venta suspendida puede estar asociada a un cliente.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente', 'id_cliente');
    }
}
