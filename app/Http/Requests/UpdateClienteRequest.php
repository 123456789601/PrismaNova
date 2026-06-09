<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateClienteRequest
 * 
 * Validación de datos para la actualización de un cliente existente.
 * Asegura la integridad de los datos, permitiendo el mismo documento o email
 * solo si pertenece al cliente que se está actualizando.
 */
class UpdateClienteRequest extends FormRequest
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
     * Excluye el cliente actual de la validación de unicidad de documento y email.
     *
     * @return array
     */
    public function rules()
    {
        $id = $this->route('cliente');
        $id = is_object($id) ? $id->getKey() : $id;
        return [
            'nombre' => ['required', 'string', 'max:100', 'regex:/^[\pL\s]+$/u'],
            'apellido' => ['required', 'string', 'max:100', 'regex:/^[\pL\s]+$/u'],
            'documento' => ['required', 'string', 'max:50', 'regex:/^[0-9]+$/', 'unique:clientes,documento,' . $id . ',id_cliente'],
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:200',
            'email' => 'nullable|email|max:150|unique:clientes,email,' . $id . ',id_cliente',
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
     * Sanea las entradas para evitar XSS y estandariza el formato.
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
