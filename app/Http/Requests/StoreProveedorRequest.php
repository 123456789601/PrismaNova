<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProveedorRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nombre_empresa' => 'required|string|max:150',
            'nit' => 'required|string|max:50|unique:proveedores,nit',
            'contacto' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:200',
            'email' => 'nullable|email|max:150|unique:proveedores,email',
            'estado' => 'required|in:activo,inactivo',
        ];
    }

    /**
     * Preparar los datos para la validación.
     * Sanear entradas para evitar XSS y estandarizar formato.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'nombre_empresa' => mb_convert_case(trim(strip_tags($this->nombre_empresa)), MB_CASE_TITLE, 'UTF-8'),
            'nit' => trim(strip_tags($this->nit)),
            'contacto' => mb_convert_case(trim(strip_tags($this->contacto)), MB_CASE_TITLE, 'UTF-8'),
            'telefono' => trim(strip_tags($this->telefono)),
            'direccion' => trim(strip_tags($this->direccion)),
            'email' => strtolower(trim(strip_tags($this->email))),
        ]);
    }
}
