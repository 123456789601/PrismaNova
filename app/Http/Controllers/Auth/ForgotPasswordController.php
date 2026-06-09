<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Rules\Recaptcha;

/**
 * Class ForgotPasswordController
 * 
 * Gestiona el proceso de solicitud de restablecimiento de contraseña.
 * Permite a los usuarios solicitar un enlace por correo para recuperar su cuenta.
 */
class ForgotPasswordController extends Controller
{
    /**
     * Muestra el formulario para solicitar un enlace de restablecimiento de contraseña.
     *
     * @return \Illuminate\View\View Vista del formulario de solicitud.
     */
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    /**
     * Envía un enlace de restablecimiento de contraseña al correo del usuario.
     * 
     * Valida el correo, verifica reCAPTCHA si está configurado, y envía el enlace
     * usando el broker de contraseñas de Laravel configurado para el modelo Usuario.
     *
     * @param  \Illuminate\Http\Request  $request Solicitud con el correo electrónico.
     * @return \Illuminate\Http\RedirectResponse Redirección con mensaje de éxito o error.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $rules = ['email' => 'required|email'];

        if (config('services.recaptcha.site_key')) {
            $rules['g-recaptcha-response'] = ['required', new Recaptcha];
        }

        $request->validate($rules);

        // Intentamos enviar el enlace. Laravel usa el Broker configurado en config/auth.php
        // En nuestro caso, está configurado para usar el modelo Usuario.
        try {
            $status = Password::broker('usuarios')->sendResetLink(
                $request->only('email')
            );

            return $status == Password::RESET_LINK_SENT
                        ? back()->with(['status' => __($status)])
                        : back()->withErrors(['email' => __($status)]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error enviando email de recuperación: ' . $e->getMessage());
            return back()->withErrors(['email' => 'No se pudo enviar el correo. Verifique la configuración del servidor de correos.']);
        }
    }
}
