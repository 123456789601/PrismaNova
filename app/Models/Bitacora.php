<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bitacora extends Model
{
    use HasFactory;

    protected $table = 'bitacoras';
    protected $primaryKey = 'id_bitacora';

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
     * Relación con el usuario que realizó la acción.
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    /**
     * Registra una nueva entrada en la bitácora.
     *
     * @param string $accion
     * @param string|null $tabla
     * @param int|null $registroId
     * @param string|null $descripcion
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
