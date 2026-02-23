<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'clientes';
    protected $primaryKey = 'id_cliente';
    protected $fillable = [
        'nombre',
        'apellido',
        'documento',
        'telefono',
        'direccion',
        'email',
        'estado',
    ];

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'id_cliente', 'id_cliente');
    }

    public function getRouteKeyName()
    {
        return $this->primaryKey;
    }
}
