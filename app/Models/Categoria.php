<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Categoria
 * 
 * Representa una agrupación lógica de productos (familia de productos).
 * Permite clasificar y organizar el inventario.
 *
 * @property int $id_categoria Identificador único de la categoría.
 * @property string $nombre Nombre de la categoría.
 * @property string|null $descripcion Descripción breve.
 * @property string $estado Estado de la categoría (activa, inactiva).
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Producto[] $productos Productos pertenecientes a esta categoría.
 */
class Categoria extends Model
{
    /**
     * Tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'categorias';

    /**
     * Clave primaria de la tabla.
     *
     * @var string
     */
    protected $primaryKey = 'id_categoria';

    /**
     * Atributos asignables masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
    ];

    /**
     * Relación: Una categoría contiene múltiples productos.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productos()
    {
        return $this->hasMany(Producto::class, 'id_categoria', 'id_categoria');
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
