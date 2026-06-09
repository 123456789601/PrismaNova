<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateProveedorRequest
 * 
 * Validación de datos para la actualización de un proveedor existente.
 * Asegura la integridad de los datos, permitiendo el mismo NIT o email
 * solo si pertenece al proveedor que se está actualizando.
 */
class UpdateProveedorRequest extends FormRequest
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
     * Excluye el proveedor actual de la validación de unicidad de NIT y email.
     *
     * @return array
     */
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

    /**
     * Prepara los datos para la validación.
     * 
     * Sanea las entradas para evitar XSS y estandariza el formato.
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
