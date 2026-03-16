<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
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
