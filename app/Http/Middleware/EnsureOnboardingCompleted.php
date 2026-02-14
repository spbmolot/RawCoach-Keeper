<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOnboardingCompleted
{
    /**
     * Перенаправляет новых пользователей на онбординг-воронку.
     * Пропускает админов, редакторов и тех, кто уже прошёл онбординг.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        // Админы и редакторы не проходят онбординг
        if ($user->hasAnyRole(['admin', 'editor', 'root'])) {
            return $next($request);
        }

        // Если онбординг уже пройден — пропускаем
        if ($user->hasCompletedOnboarding()) {
            return $next($request);
        }

        // Не перенаправляем если уже на маршрутах онбординга
        if ($request->routeIs('onboarding.*')) {
            return $next($request);
        }

        // Не перенаправляем с logout, api и webhook маршрутов
        if ($request->routeIs('logout', 'api.*', 'webhook.*', 'filament.*')) {
            return $next($request);
        }

        return redirect()->route('onboarding.welcome');
    }
}
