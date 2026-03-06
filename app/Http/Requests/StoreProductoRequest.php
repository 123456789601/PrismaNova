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
        // Limpiar formato de moneda en precios (eliminar $, S/ y convertir coma a punto)
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
