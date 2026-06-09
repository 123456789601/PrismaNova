<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class StoreClienteRequest
 * 
 * Validación de datos para el almacenamiento de un nuevo cliente.
 * Asegura la integridad de los datos del cliente, incluyendo unicidad de documento y email.
 */
class StoreClienteRequest extends FormRequest
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
     * Valida formato de nombres (solo letras), documento (solo números) y unicidad.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'nombre' => ['required', 'string', 'max:100', 'regex:/^[\pL\s]+$/u'],
            'apellido' => ['required', 'string', 'max:100', 'regex:/^[\pL\s]+$/u'],
            'documento' => ['required', 'string', 'max:50', 'regex:/^[0-9]+$/', 'unique:clientes,documento'],
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:200',
            'email' => 'nullable|email|max:150|unique:clientes,email',
            'estado' => 'required|in:activo,inactivo',
        ];
    }

    /**
     * Obtiene los mensajes de error de validación personalizados.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'nombre.regex' => 'El nombre no puede contener números ni caracteres especiales.',
            'apellido.regex' => 'El apellido no puede contener números ni caracteres especiales.',
            'documento.regex' => 'El documento solo puede contener números (sin espacios ni guiones).',
        ];
    }

    /**
     * Prepara los datos para la validación.
     * 
     * Sanea las entradas para evitar XSS y estandariza el formato
     * (títulos para nombres, minúsculas para email, solo dígitos para documento).
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'nombre' => mb_convert_case(trim(strip_tags($this->nombre)), MB_CASE_TITLE, 'UTF-8'),
            'apellido' => mb_convert_case(trim(strip_tags($this->apellido)), MB_CASE_TITLE, 'UTF-8'),
            'documento' => preg_replace('/[^0-9]/', '', trim(strip_tags($this->documento))),
            'telefono' => preg_replace('/[^0-9+]/', '', trim(strip_tags($this->telefono))),
            'direccion' => trim(strip_tags($this->direccion)),
            'email' => $this->email ? strtolower(trim(strip_tags($this->email))) : null,
        ]);
    }
}
