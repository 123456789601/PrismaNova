<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class StoreVentaRequest
 * 
 * Validación de datos para el almacenamiento de una nueva venta.
 * Asegura que la venta tenga un cliente válido, productos, cantidades y precios correctos.
 */
class StoreVentaRequest extends FormRequest
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
     * Valida la existencia de cliente y productos, así como la estructura del array de detalles.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id_cliente' => 'required|exists:clientes,id_cliente',
            'fecha' => 'required|date',
            'descuento' => 'nullable|numeric|min:0',
            'impuesto' => 'nullable|numeric|min:0',
            'cupon' => 'nullable|string|max:50',
            'metodo_pago' => 'nullable|string|max:50',
            'metodo_pago_id' => 'nullable|exists:metodos_pago,id_metodo_pago',
            'monto_recibido' => 'nullable|numeric|min:0',
            'referencia_pago' => 'nullable|string|max:50',
            'ultimos_digitos' => 'nullable|string|size:4',
            'id_producto' => 'required|array|min:1',
            'id_producto.*' => 'required|exists:productos,id_producto',
            'cantidad' => 'required|array|min:1',
            'cantidad.*' => 'required|integer|min:1',
            'precio_unitario' => 'required|array|min:1',
            'precio_unitario.*' => 'required|numeric|min:0',
        ];
    }

    /**
     * Preparar los datos para la validación.
     */
    protected function prepareForValidation()
    {
        if ($this->has('cupon')) {
            $this->merge([
                'cupon' => trim(strip_tags($this->cupon)),
            ]);
        }
        if ($this->has('metodo_pago')) {
             $this->merge([
                'metodo_pago' => trim(strip_tags($this->metodo_pago)),
            ]);
        }
    }
}
