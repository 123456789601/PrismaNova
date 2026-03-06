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
use Illuminate\Validation\Rules\Password;

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
            'nombre' => ['required','string','max:100', 'regex:/^[\pL\s]+$/u'],
            'apellido' => ['required','string','max:100', 'regex:/^[\pL\s]+$/u'],
            'documento' => ['required','string','max:50','regex:/^[0-9]+$/','unique:usuarios,documento'],
            'email' => ['required','email','max:150','unique:usuarios,email'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
        ], [
            'nombre.regex' => 'El nombre no puede contener números ni caracteres especiales.',
            'apellido.regex' => 'El apellido no puede contener números ni caracteres especiales.',
            'documento.regex' => 'El documento solo puede contener números (sin espacios ni guiones).',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.letters' => 'La contraseña debe contener al menos una letra.',
            'password.mixed' => 'La contraseña debe contener letras mayúsculas y minúsculas.',
            'password.numbers' => 'La contraseña debe contener al menos un número.',
            'password.symbols' => 'La contraseña debe contener al menos un símbolo.',
        ]);

        // Obtener rol cliente
        $rolCliente = \App\Models\Rol::where('nombre', 'cliente')->first();
        $rolId = $rolCliente ? $rolCliente->id : null;

        // Crear usuario de sistema (login)
        $usuario = new Usuario([
            'nombre' => $data['nombre'],
            'apellido' => $data['apellido'],
            'documento' => $data['documento'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'rol_id' => $rolId,
            'estado' => 'activo',
        ]);

        if (Schema::hasColumn('usuarios', 'username')) {
            $usuario->username = Str::slug(explode('@', $data['email'])[0]);
        }
        $usuario->save();

        // Crear o actualizar perfil de cliente asociado
        // Si ya existe un cliente con este email, lo usamos (o actualizamos) en lugar de fallar
        $cliente = Cliente::where('email', $data['email'])->first();
        
        if (!$cliente) {
            // Verificar si existe por documento para evitar duplicidad de documento
            $clientePorDoc = Cliente::where('documento', $data['documento'])->first();
            
            if (!$clientePorDoc) {
                Cliente::create([
                    'nombre' => $data['nombre'],
                    'apellido' => $data['apellido'],
                    'documento' => $data['documento'],
                    'telefono' => null,
                    'direccion' => null,
                    'email' => $data['email'],
                    'estado' => 'activo',
                ]);
            } else {
                // Si existe por documento pero no por email, actualizamos el email del cliente existente
                // esto asume que el usuario verificó su email al registrarse (o lo hará)
                $clientePorDoc->email = $data['email'];
                $clientePorDoc->save();
            }
        } else {
            // El cliente ya existe por email, no hacemos nada (preservamos sus datos actuales)
            // Opcionalmente podríamos actualizar nombre/apellido si están vacíos en el registro existente
        }

        // Autenticar automáticamente tras registro exitoso
        Auth::login($usuario);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success','Registro completado');
    }
}
