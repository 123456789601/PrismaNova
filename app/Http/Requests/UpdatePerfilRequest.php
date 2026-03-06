<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

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
            'nombre' => ['required', 'string', 'max:100', 'regex:/^[\pL\s]+$/u'],
            'apellido' => ['required', 'string', 'max:100', 'regex:/^[\pL\s]+$/u'],
            'documento' => ['required', 'regex:/^[0-9]+$/', 'digits_between:1,50', 'unique:usuarios,documento,' . $id . ',id_usuario'],
            'email' => 'required|email|max:150|unique:usuarios,email,' . $id . ',id_usuario',
            'password' => ['nullable', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:200',
            'tema' => 'nullable|in:light,dark',
        ];
    }

    public function messages()
    {
        return [
            'nombre.regex' => 'El nombre no puede contener números ni caracteres especiales.',
            'apellido.regex' => 'El apellido no puede contener números ni caracteres especiales.',
            'documento.regex' => 'El documento solo puede contener números (sin espacios ni guiones).',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.letters' => 'La contraseña debe contener al menos una letra.',
            'password.mixed' => 'La contraseña debe contener letras mayúsculas y minúsculas.',
            'password.numbers' => 'La contraseña debe contener al menos un número.',
            'password.symbols' => 'La contraseña debe contener al menos un símbolo.',
        ];
    }
}
