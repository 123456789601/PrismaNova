<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Configuracion
 * 
 * Modelo para la configuración global del sistema.
 * Almacena pares clave-valor para configuraciones como nombre de tienda, impuestos, etc.
 */
class Configuracion extends Model
{
    use HasFactory;

    /**
     * Atributos que no son asignables masivamente.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Clave primaria de la tabla.
     *
     * @var string
     */
    protected $primaryKey = 'id';
}
