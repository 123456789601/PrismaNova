<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class StoreProductoRequest
 * 
 * Validación de datos para el almacenamiento de un nuevo producto.
 * Asegura la integridad de los datos del producto, incluyendo unicidad de código de barras.
 */
class StoreProductoRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para hacer esta solicitud.
     * 
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Obtiene las reglas de validación que se aplican a la solicitud.
     * 
     * Valida campos obligatorios y formatos (imagen, numéricos, fechas).
     *
     * @return array
     */
    public function rules()
    {
        return [
            'codigo_barras' => 'nullable|string|max:50|unique:productos,codigo_barras',
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
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
}
