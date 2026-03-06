<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateUsuarioRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $usuario = $this->route('usuario');
        // Si el enrutamiento devuelve el objeto modelo, obtenemos su ID
        $id = $usuario instanceof \App\Models\Usuario ? $usuario->id_usuario : $usuario;

        return [
            'nombre' => ['required', 'string', 'max:100', 'regex:/^[\pL\s]+$/u'],
            'apellido' => ['required', 'string', 'max:100', 'regex:/^[\pL\s]+$/u'],
            'documento' => ['required', 'regex:/^[0-9]+$/', 'digits_between:1,50', 'unique:usuarios,documento,' . $id . ',id_usuario'],
            'email' => 'required|email|max:150|unique:usuarios,email,' . $id . ',id_usuario',
            'password' => ['nullable', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
            'rol_id' => 'required|exists:roles,id',
            'estado' => 'required|in:activo,inactivo',
        ];
    }

    public function messages()
    {
        return [
            'nombre.regex' => 'El nombre no puede contener números ni caracteres especiales.',
            'apellido.regex' => 'El apellido no puede contener números ni caracteres especiales.',
            'documento.regex' => 'El documento solo puede contener números (sin espacios ni guiones).',
            'documento.digits_between' => 'El documento debe tener entre 1 y 50 dígitos.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.letters' => 'La contraseña debe contener al menos una letra.',
            'password.mixed' => 'La contraseña debe contener letras mayúsculas y minúsculas.',
            'password.numbers' => 'La contraseña debe contener al menos un número.',
            'password.symbols' => 'La contraseña debe contener al menos un símbolo.',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'documento' => trim(strip_tags($this->documento)),
            'nombre' => mb_convert_case(trim(strip_tags($this->nombre)), MB_CASE_TITLE, 'UTF-8'),
            'apellido' => mb_convert_case(trim(strip_tags($this->apellido)), MB_CASE_TITLE, 'UTF-8'),
            'email' => strtolower(trim(strip_tags($this->email))),
        ]);
    }
}
