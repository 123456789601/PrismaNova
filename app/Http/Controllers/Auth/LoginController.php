<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

use App\Models\Bitacora;

use App\Rules\Recaptcha;

/**
 * Class LoginController
 * 
 * Gestiona el proceso de autenticación de usuarios.
 * Maneja el inicio de sesión, validación de credenciales y cierre de sesión seguro.
 */
class LoginController extends Controller
{
    /**
     * Muestra el formulario de inicio de sesión.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Procesa la solicitud de inicio de sesión.
     * 
     * Valida credenciales, intenta autenticar y regenera la sesión por seguridad.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $rules = [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ];

        if (config('services.recaptcha.site_key')) {
            $rules['g-recaptcha-response'] = ['required', new Recaptcha];
        }

        $credentials = $request->validate($rules);
        
        // Remove recaptcha from credentials before attempting login
        unset($credentials['g-recaptcha-response']);

        $this->ensureIsNotRateLimited($request);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::clear($this->throttleKey($request));
            $request->session()->regenerate();
            Bitacora::registrar('LOGIN', 'usuarios', Auth::id(), 'Inicio de sesión exitoso');
            return redirect()->intended(route('dashboard'));
        }

        RateLimiter::hit($this->throttleKey($request));

        return back()->withErrors([
            'email' => 'Credenciales inválidas.',
        ])->onlyInput('email');
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function ensureIsNotRateLimited(Request $request)
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     *
     * @return string
     */
    protected function throttleKey(Request $request)
    {
        return Str::transliterate(Str::lower($request->input('email')).'|'.$request->ip());
    }

    /**
     * Cierra la sesión del usuario.
     * 
     * Invalida la sesión actual, regenera el token CSRF y elimina cookies de sesión.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        $userId = Auth::id();
        if ($userId) {
            Bitacora::registrar('LOGOUT', 'usuarios', $userId, 'Cierre de sesión');
        }
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Limpiar cookies de sesión para evitar conflictos
        $sessionCookie = config('session.cookie', 'laravel_session');
        Cookie::queue(Cookie::forget($sessionCookie));
        Cookie::queue(Cookie::forget('XSRF-TOKEN'));
        
        return redirect()->route('login');
    }
}
