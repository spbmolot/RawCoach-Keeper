# RawCoach-Keeper

Starter Laravel 11 application for rawplan.ru with:

- Jetstream with Livewire for authentication and user dashboard
- Filament Admin panel
- Role and permission management via spatie/laravel-permission
- Basic billing models (`Plan`, `UserSubscription`, `Payment`) and placeholders for YooKassa and CloudPayments integrations
- Simple advertising models (`AdPlacement`, `AdCampaign`, `AdCreative`)

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
npm install
npm run build
php artisan migrate
```

## Infrastructure Requirements

- PHP 8.2+
- Database (MySQL, PostgreSQL, etc.)
- Redis for cache and queues
- Queue worker (e.g. Supervisor)
- SSL/TLS termination

## Production Deployment

1. Configure `.env` with database, Redis, queue and other production settings.
2. Run migrations: `php artisan migrate --force`.
3. Build assets: `npm run build`.
4. Optimize configuration and routes: `php artisan config:cache` and `php artisan route:cache`.
5. Start queue worker: `php artisan queue:work`.
6. Start scheduler: `php artisan schedule:work` or cron `* * * * * php artisan schedule:run`.

## Webhooks

Payment providers send notifications to:

- `POST /webhook/yookassa`
- `POST /webhook/cloudpayments`

Controllers currently contain TODOs for future implementation.
