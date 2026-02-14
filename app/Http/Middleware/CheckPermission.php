<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $user = $request->user();

        // Если пользователь не авторизован
        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Для доступа к этой странице необходимо войти в систему.');
        }

        // Проверяем, есть ли у пользователя одно из требуемых разрешений
        if (!$user->hasAnyPermission($permissions)) {
            Log::channel('security')->warning('Unauthorized permission access attempt', [
                'user_id' => $user->id,
                'required_permissions' => $permissions,
                'url' => $request->fullUrl(),
                'ip' => $request->ip(),
            ]);
            abort(403, 'У вас нет прав доступа к этой функции.');
        }

        return $next($request);
    }
}
