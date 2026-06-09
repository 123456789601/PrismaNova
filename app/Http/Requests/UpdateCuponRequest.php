<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateCuponRequest
 * 
 * Validación de datos para la actualización de un cupón existente.
 * Asegura la integridad de los datos, permitiendo el mismo código
 * solo si pertenece al cupón que se está actualizando.
 */
class UpdateCuponRequest extends FormRequest
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
     * Excluye el cupón actual de la validación de unicidad del código.
     *
     * @return array
     */
    public function rules()
    {
        $id = $this->route('cupone');
        $id = is_object($id) ? $id->getKey() : $id;
        return [
            'codigo' => 'required|string|max:50|unique:cupones,codigo,' . $id . ',id_cupon',
            'tipo' => 'required|in:fijo,porcentaje',
            'valor' => 'required|numeric|min:0',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'estado' => 'required|in:activo,inactivo',
            'uso_maximo' => 'nullable|integer|min:1',
        ];
    }
}
