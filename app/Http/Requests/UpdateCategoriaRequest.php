<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateCategoriaRequest
 * 
 * Validación de datos para la actualización de una categoría existente.
 * Asegura la integridad de los datos, permitiendo el mismo nombre
 * solo si pertenece a la categoría que se está actualizando.
 */
class UpdateCategoriaRequest extends FormRequest
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
     * Excluye la categoría actual de la validación de unicidad del nombre.
     *
     * @return array
     */
    public function rules()
    {
        $id = $this->route('categoria');
        $id = is_object($id) ? $id->getKey() : $id;
        return [
            'nombre' => 'required|string|max:100|unique:categorias,nombre,' . $id . ',id_categoria',
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
