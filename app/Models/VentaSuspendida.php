<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VentaSuspendida extends Model
{
    use HasFactory;

    protected $table = 'ventas_suspendidas';
    protected $primaryKey = 'id_venta_suspendida';

    protected $fillable = [
        'id_usuario',
        'id_cliente',
        'contenido',
        'total',
        'nota',
    ];

    protected $casts = [
        'contenido' => 'array',
        'total' => 'float',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente', 'id_cliente');
    }
}
