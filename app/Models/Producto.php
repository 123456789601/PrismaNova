<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'productos';
    protected $primaryKey = 'id_producto';
    protected $fillable = [
        'codigo_barras',
        'nombre',
        'descripcion',
        'imagen',
        'id_categoria',
        'id_proveedor',
        'precio_compra',
        'precio_venta',
        'stock',
        'stock_minimo',
        'fecha_vencimiento',
        'estado',
    ];

    protected $appends = ['imagen_url'];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'id_categoria', 'id_categoria');
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor', 'id_proveedor');
    }

    public function detallesCompra()
    {
        return $this->hasMany(DetalleCompra::class, 'id_producto', 'id_producto');
    }

    public function detallesVenta()
    {
        return $this->hasMany(DetalleVenta::class, 'id_producto', 'id_producto');
    }

    public function getRouteKeyName()
    {
        return $this->primaryKey;
    }

    public function getImagenUrlAttribute()
    {
        if (!$this->imagen) return null;
        return asset('storage/'.$this->imagen);
    }
}
