<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Class RegistrationController
 * 
 * Gestiona el registro de nuevos usuarios en el sistema (clientes finales).
 * Crea tanto el usuario de acceso como el perfil de cliente asociado.
 */
class RegistrationController extends Controller
{
    /**
     * Muestra el formulario de registro.
     *
     * @return \Illuminate\View\View
     */
    public function show()
    {
        return view('auth.register');
    }

    /**
     * Procesa el registro de un nuevo usuario.
     * 
     * Valida datos, crea el usuario con rol 'cliente', crea el registro en tabla clientes,
     * e inicia sesión automáticamente.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required','string','max:100'],
            'apellido' => ['required','string','max:100'],
            'documento' => ['required','string','max:50','unique:usuarios,documento'],
            'email' => ['required','email','max:150','unique:usuarios,email'],
            'password' => ['required','string','min:6','confirmed'],
        ]);

        // Crear usuario de sistema (login)
        $usuario = new Usuario([
            'nombre' => $data['nombre'],
            'apellido' => $data['apellido'],
            'documento' => $data['documento'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'rol' => 'cliente',
            'estado' => 'activo',
        ]);

        if (Schema::hasColumn('usuarios', 'username')) {
            $usuario->username = Str::slug(explode('@', $data['email'])[0]);
        }
        $usuario->save();

        // Crear perfil de cliente asociado
        Cliente::create([
            'nombre' => $data['nombre'],
            'apellido' => $data['apellido'],
            'documento' => $data['documento'],
            'telefono' => null,
            'direccion' => null,
            'email' => $data['email'],
            'estado' => 'activo',
        ]);

        // Autenticar automáticamente tras registro exitoso
        Auth::login($usuario);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success','Registro completado');
    }
}
