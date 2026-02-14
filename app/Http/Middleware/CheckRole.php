<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // Если пользователь не авторизован
        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Для доступа к этой странице необходимо войти в систему.');
        }

        // Проверяем, есть ли у пользователя одна из требуемых ролей
        if (!$user->hasAnyRole($roles)) {
            Log::channel('security')->warning('Unauthorized role access attempt', [
                'user_id' => $user->id,
                'required_roles' => $roles,
                'user_roles' => $user->getRoleNames()->toArray(),
                'url' => $request->fullUrl(),
                'ip' => $request->ip(),
            ]);
            abort(403, 'У вас нет прав доступа к этой странице.');
        }

        return $next($request);
    }
}
