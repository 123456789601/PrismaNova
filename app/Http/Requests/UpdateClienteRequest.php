<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClienteRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->route('cliente');
        $id = is_object($id) ? $id->getKey() : $id;
        return [
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'documento' => 'required|string|max:50|unique:clientes,documento,' . $id . ',id_cliente',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:200',
            'email' => 'nullable|email|max:150|unique:clientes,email,' . $id . ',id_cliente',
            'estado' => 'required|in:activo,inactivo',
        ];
    }
}
