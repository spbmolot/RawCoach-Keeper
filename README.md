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

## Webhooks

Payment providers send notifications to:

- `POST /webhook/yookassa`
- `POST /webhook/cloudpayments`

Controllers currently contain TODOs for future implementation.
