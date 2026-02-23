<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cupon extends Model
{
    protected $table = 'cupones';
    protected $primaryKey = 'id_cupon';
    protected $fillable = [
        'codigo','tipo','valor','fecha_inicio','fecha_fin','estado','uso_maximo','usos'
    ];
    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'valor' => 'float',
    ];

    public function esValido(): bool
    {
        if ($this->estado !== 'activo') return false;
        $now = now();
        if ($this->fecha_inicio && $now->lt($this->fecha_inicio)) return false;
        if ($this->fecha_fin && $now->gt($this->fecha_fin)) return false;
        if ($this->uso_maximo !== null && $this->usos >= $this->uso_maximo) return false;
        return true;
    }
}
