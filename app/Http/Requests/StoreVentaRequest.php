<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVentaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

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
            'id_producto' => 'required|array|min:1',
            'id_producto.*' => 'required|exists:productos,id_producto',
            'cantidad' => 'required|array|min:1',
            'cantidad.*' => 'required|integer|min:1',
            'precio_unitario' => 'required|array|min:1',
            'precio_unitario.*' => 'required|numeric|min:0',
        ];
    }
}
