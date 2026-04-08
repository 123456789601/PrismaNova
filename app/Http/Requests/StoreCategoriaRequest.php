<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoriaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nombre' => 'required|string|max:100|unique:categorias,nombre',
            'descripcion' => 'nullable|string',
            'estado' => 'required|in:activo,inactivo',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'estado' => $this->estado ?? 'activo',
        ]);
    }
}
