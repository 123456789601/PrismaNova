<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * Una lista de los tipos de excepción que no se reportan.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * Una lista de las entradas que nunca se guardan en sesión para excepciones de validación.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Registrar los callbacks de manejo de excepciones para la aplicación.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (HttpExceptionInterface $e, $request) {
            if ((int) $e->getStatusCode() !== 419) {
                return null;
            }

            $sessionToken = (string) ($request->session()->token() ?? '');
            $requestToken = (string) ($request->input('_token') ?: $request->header('X-CSRF-TOKEN') ?: '');
            $cookieNames = array_keys((array) $request->cookies->all());
            sort($cookieNames);

            $h = function (string $v): string {
                if ($v === '') {
                    return '';
                }
                return substr(hash('sha256', $v), 0, 12);
            };

            Log::warning('csrf_mismatch', [
                'path' => $request->path(),
                'method' => $request->method(),
                'expects_json' => $request->expectsJson(),
                'user_id' => optional($request->user())->id_usuario,
                'session_cookie' => config('session.cookie'),
                'session_id_hash' => $h((string) $request->session()->getId()),
                'session_token_hash' => $h($sessionToken),
                'request_token_hash' => $h($requestToken),
                'cookie_names' => $cookieNames,
                'referer' => (string) $request->headers->get('referer', ''),
                'origin' => (string) $request->headers->get('origin', ''),
                'message' => (string) $e->getMessage(),
            ]);

            if ($request->expectsJson()) {
                return response()->json(['error' => 'csrf_mismatch'], 419);
            }

            return response('CSRF token mismatch.', 419);
        });
    }
}
