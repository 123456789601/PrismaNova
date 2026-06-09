<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Rol
 * 
 * Modelo para los roles de usuario en el sistema.
 * Define los diferentes tipos de usuarios (admin, cajero, bodeguero, cliente).
 */
class Rol extends Model
{
    use HasFactory;

    /**
     * Tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'roles';

    /**
     * Atributos asignables masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    /**
     * Relación: Un rol tiene muchos usuarios.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'rol_id');
    }
}
