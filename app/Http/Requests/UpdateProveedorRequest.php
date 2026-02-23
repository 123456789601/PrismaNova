<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProveedorRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->route('proveedor');
        $id = is_object($id) ? $id->getKey() : $id;
        return [
            'nombre_empresa' => 'required|string|max:150',
            'nit' => 'required|string|max:50|unique:proveedores,nit,' . $id . ',id_proveedor',
            'contacto' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:200',
            'email' => 'nullable|email|max:150|unique:proveedores,email,' . $id . ',id_proveedor',
            'estado' => 'required|in:activo,inactivo',
        ];
    }
}
