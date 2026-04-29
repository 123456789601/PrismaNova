<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Rules\Recaptcha;

class ForgotPasswordController extends Controller
{
    /**
     * Muestra el formulario para solicitar un enlace de restablecimiento de contraseña.
     *
     * @return \Illuminate\View\View
     */
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    /**
     * Enviar un enlace de restablecimiento al usuario proporcionado.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
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
