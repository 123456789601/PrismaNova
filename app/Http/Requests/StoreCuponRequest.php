<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class StoreCuponRequest
 * 
 * Validación de datos para el almacenamiento de un nuevo cupón de descuento.
 * Asegura la integridad de los datos, incluyendo unicidad del código y validación de fechas.
 */
class StoreCuponRequest extends FormRequest
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
     * Valida unicidad del código, tipo de descuento, valor y rango de fechas.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'codigo' => 'required|string|max:50|unique:cupones,codigo',
            'tipo' => 'required|in:fijo,porcentaje',
            'valor' => 'required|numeric|min:0',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'estado' => 'required|in:activo,inactivo',
            'uso_maximo' => 'nullable|integer|min:1',
        ];
    }
}
