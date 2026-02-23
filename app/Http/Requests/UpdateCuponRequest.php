<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCuponRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

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
