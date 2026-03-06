<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Class Usuario
 * 
 * Representa a los usuarios del sistema (empleados, administradores).
 * Gestiona la autenticación y autorización mediante roles.
 *
 * @property int $id_usuario Identificador único del usuario.
 * @property string $nombre Nombre de pila.
 * @property string $apellido Apellido(s).
 * @property string|null $documento Documento de identidad (DNI, etc.).
 * @property string $email Correo electrónico (usado para login).
 * @property string $password Contraseña encriptada.
 * @property string $rol Rol asignado (admin, cajero, bodeguero).
 * @property string $estado Estado de la cuenta (activo, inactivo).
 * @property string|null $tema Preferencia de tema visual (light/dark).
 * @property \Illuminate\Support\Carbon $created_at Fecha de registro.
 * @property \Illuminate\Support\Carbon $updated_at Última actualización.
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Venta[] $ventas Ventas realizadas por este usuario.
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Compra[] $compras Compras registradas por este usuario.
 */
class Usuario extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * Tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'usuarios';

    /**
     * Clave primaria de la tabla.
     *
     * @var string
     */
    protected $primaryKey = 'id_usuario';

    /**
     * Indica si la clave primaria es auto-incrementable.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * Tipo de dato de la clave primaria.
     *
     * @var string
     */
    protected $keyType = 'int';

    /**
     * Atributos asignables masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'nombre',
        'apellido',
        'documento',
        'email',
        'password',
        'rol_id',
        'estado',
        'tema',
    ];

    /**
     * Atributos ocultos en la serialización (arrays/JSON).
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Conversión de atributos a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\RestablecerContrasenaNotification($token));
    }

    /**
     * Relación: Un usuario pertenece a un rol.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    /**
     * Relación: Un usuario puede registrar múltiples compras.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function compras()
    {
        return $this->hasMany(Compra::class, 'id_usuario', 'id_usuario');
    }

    /**
     * Relación: Un usuario puede realizar múltiples ventas.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ventas()
    {
        return $this->hasMany(Venta::class, 'id_usuario', 'id_usuario');
    }

    /**
     * Verifica si el usuario tiene un rol específico.
     *
     * @param string|array $role
     * @return bool
     */
    public function hasRole($role)
    {
        if (is_array($role)) {
            return $this->rol && in_array($this->rol->nombre, $role);
        }
        return $this->rol && $this->rol->nombre === $role;
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
