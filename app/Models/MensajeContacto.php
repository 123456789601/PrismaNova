<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MensajeContacto
 * 
 * Modelo para los mensajes de contacto enviados desde el formulario público.
 * Almacena los mensajes que los usuarios envían al administrador.
 */
class MensajeContacto extends Model
{
    use HasFactory;

    /**
     * Atributos asignables masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'nombre',
        'email',
        'mensaje',
        'leido'
    ];
}
