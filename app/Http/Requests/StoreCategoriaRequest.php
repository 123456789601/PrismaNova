<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class StoreCategoriaRequest
 * 
 * Validación de datos para el almacenamiento de una nueva categoría.
 * Asegura la integridad de los datos, incluyendo unicidad del nombre de categoría.
 */
class StoreCategoriaRequest extends FormRequest
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
     * Valida que el nombre sea único y el estado sea válido.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'nombre' => 'required|string|max:100|unique:categorias,nombre',
            'descripcion' => 'nullable|string',
            'estado' => 'required|in:activo,inactivo',
        ];
    }

    /**
     * Prepara los datos para la validación.
     * 
     * Establece el estado por defecto como 'activo' si no se proporciona.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'estado' => $this->estado ?? 'activo',
        ]);
    }
}
