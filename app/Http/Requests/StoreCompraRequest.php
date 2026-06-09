<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class StoreCompraRequest
 * 
 * Validación de datos para el almacenamiento de una nueva compra.
 * Asegura que la compra tenga un proveedor válido, productos, cantidades y precios correctos.
 */
class StoreCompraRequest extends FormRequest
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
     * Valida la existencia de proveedor y productos, así como la estructura del array de detalles.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id_proveedor' => 'required|exists:proveedores,id_proveedor',
            'fecha' => 'required|date',
            'impuesto' => 'nullable|numeric|min:0',
            'id_producto' => 'required|array|min:1',
            'id_producto.*' => 'required|exists:productos,id_producto',
            'cantidad' => 'required|array|min:1',
            'cantidad.*' => 'required|integer|min:1',
            'precio_compra' => 'required|array|min:1',
            'precio_compra.*' => 'required|numeric|min:0',
        ];
    }
}
