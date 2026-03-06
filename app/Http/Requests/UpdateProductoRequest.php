<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->route('producto');
        $id = is_object($id) ? $id->getKey() : $id;
        return [
            'codigo_barras' => 'nullable|string|max:50|unique:productos,codigo_barras,' . $id . ',id_producto',
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string|max:191',
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'id_categoria' => 'required|exists:categorias,id_categoria',
            'id_proveedor' => 'nullable|exists:proveedores,id_proveedor',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'stock_minimo' => 'nullable|integer|min:0',
            'fecha_vencimiento' => 'nullable|date',
            'estado' => 'required|in:activo,inactivo',
        ];
    }

    protected function prepareForValidation()
    {
        // Limpiar formato de moneda en precios
        $precioCompra = $this->precio_compra;
        if ($precioCompra) {
            $precioCompra = str_replace(['$', 'S/', ' '], '', (string)$precioCompra);
            $precioCompra = str_replace(',', '.', $precioCompra);
        }

        $precioVenta = $this->precio_venta;
        if ($precioVenta) {
            $precioVenta = str_replace(['$', 'S/', ' '], '', (string)$precioVenta);
            $precioVenta = str_replace(',', '.', $precioVenta);
        }

        $this->merge([
            'nombre' => mb_convert_case(trim(strip_tags($this->nombre ?? '')), MB_CASE_TITLE, 'UTF-8'),
            'descripcion' => trim(strip_tags($this->descripcion ?? '')),
            'id_proveedor' => $this->id_proveedor ?: null,
            'fecha_vencimiento' => $this->fecha_vencimiento ?: null,
            'stock_minimo' => $this->stock_minimo !== null && $this->stock_minimo !== '' ? $this->stock_minimo : 0,
            'codigo_barras' => $this->codigo_barras ? trim(strip_tags($this->codigo_barras)) : null,
            'precio_compra' => $precioCompra,
            'precio_venta' => $precioVenta,
        ]);
    }
}
