<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

/**
 * Class ResetPasswordController
 * 
 * Gestiona el proceso de restablecimiento de contraseña.
 * Permite a los usuarios establecer una nueva contraseña usando el token enviado por correo.
 */
class ResetPasswordController extends Controller
{
    /**
     * Crea una nueva instancia del controlador.
     * 
     * Aplica el middleware 'guest' para asegurar que solo usuarios no autenticados
     * puedan acceder a las rutas de restablecimiento de contraseña.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Muestra el formulario de restablecimiento de contraseña para el token dado.
     *
     * @param  \Illuminate\Http\Request  $request Solicitud con el correo del usuario.
     * @param  string|null  $token Token de restablecimiento enviado por correo.
     * @return \Illuminate\View\View Vista del formulario de nueva contraseña.
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.passwords.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    /**
     * Restablece la contraseña del usuario.
     * 
     * Valida el token, correo y nueva contraseña, luego actualiza la contraseña
     * del usuario usando el broker de contraseñas de Laravel configurado para el modelo Usuario.
     * También regenera el token de "remember me" y dispara el evento de restablecimiento.
     *
     * @param  \Illuminate\Http\Request  $request Solicitud con token, email y nueva contraseña.
     * @return \Illuminate\Http\RedirectResponse Redirección al login con mensaje de éxito o error.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        // Intentamos restablecer la contraseña usando el broker 'usuarios'
        $status = Password::broker('usuarios')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status == Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('status', __($status))
                    : back()->withErrors(['email' => __($status)]);
    }
}
