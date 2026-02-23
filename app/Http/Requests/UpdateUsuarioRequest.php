<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUsuarioRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->route('usuario');
        return [
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'documento' => 'required|string|max:50|unique:usuarios,documento,' . $id . ',id_usuario',
            'email' => 'required|email|max:150|unique:usuarios,email,' . $id . ',id_usuario',
            'password' => 'nullable|string|min:6|confirmed',
            'rol' => 'required|in:admin,cajero,bodeguero,cliente',
            'estado' => 'required|in:activo,inactivo',
        ];
    }
}
