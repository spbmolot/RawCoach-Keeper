<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled Tasks (Cron Jobs)
|--------------------------------------------------------------------------
|
| Для работы планировщика добавьте в crontab:
| * * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
|
*/

// Проверка истекших подписок - каждый час
Schedule::command('subscriptions:check-expired --update')
    ->hourly()
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/subscriptions-check.log'));

// Обработка автопродлений - каждые 6 часов
// Создаёт платежи для подписок, истекающих в ближайшие 3 дня
Schedule::command('subscriptions:process-renewals --days=3')
    ->everySixHours()
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/subscriptions-renewals.log'));

// Синхронизация ролей - раз в сутки (на случай рассинхронизации)
Schedule::command('subscriptions:sync-roles')
    ->daily()
    ->at('03:00')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/subscriptions-sync-roles.log'));

// Повторные попытки списания для подписок в grace period - каждые 12 часов
Schedule::command('subscriptions:retry-failed')
    ->twiceDaily(9, 21)
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/subscriptions-retry-failed.log'));

// Уведомления о скором истечении подписки - ежедневно в 10:00
Schedule::command('subscriptions:notify-expiring --days=3')
    ->dailyAt('10:00')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/subscriptions-notify-expiring.log'));
