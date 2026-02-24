<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

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
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'Credenciales inválidas.',
        ])->onlyInput('email');
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
