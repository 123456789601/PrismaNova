<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SanitizeInput
{
    protected array $except = [
        'password','password_confirmation','_token',
    ];

    public function handle(Request $request, Closure $next)
    {
        $input = $request->all();
        array_walk_recursive($input, function (&$value, $key) {
            if (is_string($value) && !in_array($key, $this->except, true)) {
                $value = trim(strip_tags($value));
            }
        });
        $request->merge($input);
        return $next($request);
    }
}
