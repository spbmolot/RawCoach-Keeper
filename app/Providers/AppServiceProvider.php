<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Платёжные сервисы регистрируются в PaymentServiceProvider
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Rate limiting для API
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Rate limiting для авторизации (защита от брутфорса)
        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        // Rate limiting для контактной формы
        RateLimiter::for('contact', function (Request $request) {
            return Limit::perMinute(3)->by($request->ip());
        });

        // Rate limiting для вебхуков
        RateLimiter::for('webhooks', function (Request $request) {
            return Limit::perMinute(120)->by($request->ip());
        });
    }
}
