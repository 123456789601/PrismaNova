<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Cliente
 * 
 * Representa a un cliente del negocio.
 * Mantiene información de contacto e historial de ventas.
 *
 * @property int $id_cliente Identificador único del cliente.
 * @property string $nombre Nombre de pila.
 * @property string $apellido Apellido(s).
 * @property string|null $documento Documento de identidad (DNI, RUC, etc.).
 * @property string|null $telefono Número de teléfono de contacto.
 * @property string|null $direccion Dirección física.
 * @property string|null $email Correo electrónico.
 * @property string $estado Estado del cliente (activo/inactivo).
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Venta[] $ventas Ventas asociadas al cliente.
 */
class Cliente extends Model
{
    /**
     * Tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'clientes';

    /**
     * Clave primaria de la tabla.
     *
     * @var string
     */
    protected $primaryKey = 'id_cliente';

    /**
     * Atributos asignables masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'nombre',
        'apellido',
        'documento',
        'telefono',
        'direccion',
        'email',
        'estado',
    ];

    /**
     * Relación: Un cliente puede tener múltiples ventas.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ventas()
    {
        return $this->hasMany(Venta::class, 'id_cliente', 'id_cliente');
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
}
