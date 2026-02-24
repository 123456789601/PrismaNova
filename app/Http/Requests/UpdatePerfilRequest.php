<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdatePerfilRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::check();
    }

    public function rules()
    {
        $id = Auth::user()->id_usuario ?? 0;
        return [
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'documento' => 'required|digits_between:1,50|unique:usuarios,documento,' . $id . ',id_usuario',
            'email' => 'required|email|max:150|unique:usuarios,email,' . $id . ',id_usuario',
            'password' => 'nullable|string|min:6|confirmed',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:200',
            'tema' => 'nullable|in:light,dark',
        ];
    }
}
