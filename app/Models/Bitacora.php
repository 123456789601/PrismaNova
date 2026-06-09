<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Bitacora
 * 
 * Registra el historial de acciones realizadas en el sistema.
 * Permite auditar cambios, accesos y operaciones importantes para seguridad y trazabilidad.
 *
 * @property int $id_bitacora Identificador único del registro de bitácora.
 * @property int|null $id_usuario ID del usuario que realizó la acción.
 * @property string $accion Tipo de acción realizada (CREATE, UPDATE, DELETE, LOGIN, etc).
 * @property string|null $tabla Tabla de la base de datos afectada.
 * @property int|null $registro_id ID del registro específico afectado.
 * @property string|null $descripcion Descripción detallada de la acción.
 * @property string|null $ip Dirección IP desde donde se realizó la acción.
 * @property string|null $navegador Navegador web del usuario.
 * @property \Illuminate\Support\Carbon $created_at Fecha y hora del registro.
 * @property-read \App\Models\Usuario|null $usuario Usuario que realizó la acción.
 */
class Bitacora extends Model
{
    use HasFactory;

    /**
     * Tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'bitacoras';

    /**
     * Clave primaria de la tabla.
     *
     * @var string
     */
    protected $primaryKey = 'id_bitacora';

    /**
     * Atributos asignables masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'id_usuario',
        'accion',
        'tabla',
        'registro_id',
        'descripcion',
        'ip',
        'navegador',
    ];

    /**
     * Relación: Un registro de bitácora pertenece a un usuario.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    /**
     * Registra una nueva entrada en la bitácora.
     * 
     * Método estático conveniente para registrar acciones desde cualquier parte del sistema.
     * Captura automáticamente el usuario autenticado, IP y navegador.
     *
     * @param string $accion Tipo de acción (CREATE, UPDATE, DELETE, LOGIN, etc).
     * @param string|null $tabla Tabla afectada (opcional).
     * @param int|null $registroId ID del registro afectado (opcional).
     * @param string|null $descripcion Descripción detallada de la acción (opcional).
     * @return void
     */
    public static function registrar($accion, $tabla = null, $registroId = null, $descripcion = null)
    {
        self::create([
            'id_usuario' => auth()->id(),
            'accion' => $accion,
            'tabla' => $tabla,
            'registro_id' => $registroId,
            'descripcion' => $descripcion,
            'ip' => request()->ip(),
            'navegador' => request()->userAgent(),
        ]);
    }
}
