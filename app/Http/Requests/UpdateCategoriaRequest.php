<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoriaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

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
}
