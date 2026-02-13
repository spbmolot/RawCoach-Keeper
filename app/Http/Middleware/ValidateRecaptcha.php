<?php

namespace App\Http\Middleware;

use App\Rules\Recaptcha;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ValidateRecaptcha
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $action  reCAPTCHA action name
     */
    public function handle(Request $request, Closure $next, string $action = ''): Response
    {
        if (config('recaptcha.enabled') && $request->isMethod('POST')) {
            Validator::make($request->all(), [
                'recaptcha_token' => ['required', new Recaptcha($action)],
            ])->validate();
        }

        return $next($request);
    }
}
