<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Log;

class LogAuthEvents
{
    /**
     * Register the listeners for the subscriber.
     */
    public function subscribe($events): array
    {
        return [
            Login::class => 'handleLogin',
            Logout::class => 'handleLogout',
            Failed::class => 'handleFailed',
            Registered::class => 'handleRegistered',
            PasswordReset::class => 'handlePasswordReset',
            Verified::class => 'handleVerified',
            Lockout::class => 'handleLockout',
        ];
    }

    public function handleLogin(Login $event): void
    {
        // Обновляем last_login_at
        $event->user->forceFill([
            'last_login_at' => now(),
        ])->saveQuietly();

        Log::channel('auth')->info('User logged in', [
            'user_id' => $event->user->id,
            'email' => $event->user->email,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'guard' => $event->guard,
        ]);
    }

    public function handleLogout(Logout $event): void
    {
        if ($event->user) {
            // Обновляем last_logout_at
            $event->user->forceFill([
                'last_logout_at' => now(),
            ])->saveQuietly();

            Log::channel('auth')->info('User logged out', [
                'user_id' => $event->user->id,
                'email' => $event->user->email,
                'ip' => request()->ip(),
            ]);
        }
    }

    public function handleFailed(Failed $event): void
    {
        Log::channel('auth')->warning('Login failed', [
            'email' => $event->credentials['email'] ?? 'unknown',
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'guard' => $event->guard,
        ]);

        Log::channel('security')->warning('Failed login attempt', [
            'email' => $event->credentials['email'] ?? 'unknown',
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public function handleRegistered(Registered $event): void
    {
        Log::channel('auth')->info('New user registered', [
            'user_id' => $event->user->id,
            'email' => $event->user->email,
            'ip' => request()->ip(),
        ]);
    }

    public function handlePasswordReset(PasswordReset $event): void
    {
        Log::channel('auth')->info('Password reset', [
            'user_id' => $event->user->id,
            'email' => $event->user->email,
            'ip' => request()->ip(),
        ]);

        Log::channel('security')->info('Password reset completed', [
            'user_id' => $event->user->id,
            'email' => $event->user->email,
            'ip' => request()->ip(),
        ]);
    }

    public function handleVerified(Verified $event): void
    {
        Log::channel('auth')->info('Email verified', [
            'user_id' => $event->user->id,
            'email' => $event->user->email,
        ]);
    }

    public function handleLockout(Lockout $event): void
    {
        Log::channel('security')->warning('Account lockout (too many attempts)', [
            'email' => $event->request->input('email', 'unknown'),
            'ip' => $event->request->ip(),
            'user_agent' => $event->request->userAgent(),
        ]);
    }
}
