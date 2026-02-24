<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Proveedor
 * 
 * Representa una entidad proveedora de productos.
 * Mantiene la información de contacto y fiscal.
 *
 * @property int $id_proveedor Identificador único del proveedor.
 * @property string $nombre_empresa Razón social o nombre comercial.
 * @property string|null $nit Número de Identificación Tributaria (o similar).
 * @property string $contacto Nombre de la persona de contacto.
 * @property string|null $telefono Teléfono de contacto.
 * @property string|null $direccion Dirección física.
 * @property string|null $email Correo electrónico.
 * @property string $estado Estado del proveedor (activo, inactivo).
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Compra[] $compras Compras realizadas a este proveedor.
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Producto[] $productos Productos suministrados por este proveedor.
 */
class Proveedor extends Model
{
    /**
     * Tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'proveedores';

    /**
     * Clave primaria de la tabla.
     *
     * @var string
     */
    protected $primaryKey = 'id_proveedor';

    /**
     * Atributos asignables masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'nombre_empresa',
        'nit',
        'contacto',
        'telefono',
        'direccion',
        'email',
        'estado',
    ];

    /**
     * Relación: Un proveedor puede tener múltiples compras registradas.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function compras()
    {
        return $this->hasMany(Compra::class, 'id_proveedor', 'id_proveedor');
    }

    /**
     * Relación: Un proveedor puede suministrar múltiples productos.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productos()
    {
        return $this->hasMany(Producto::class, 'id_proveedor', 'id_proveedor');
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
