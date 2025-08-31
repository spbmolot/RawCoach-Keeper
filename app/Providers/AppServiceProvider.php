<?php

namespace App\Providers;

use App\Services\Payments\CloudPaymentsService;
use App\Services\Payments\YooKassaService;
use CloudPayments\Manager as CloudPaymentsManager;
use Illuminate\Support\ServiceProvider;
use YooKassa\Client as YooKassaClient;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CloudPaymentsService::class, function () {
            $sdk = new CloudPaymentsManager(
                config('services.cloudpayments.public_id'),
                config('services.cloudpayments.secret_key')
            );

            return new CloudPaymentsService($sdk, config('services.cloudpayments.secret_key'));
        });

        $this->app->bind(YooKassaService::class, function () {
            $sdk = new YooKassaClient();
            $sdk->setAuth(
                config('services.yookassa.shop_id'),
                config('services.yookassa.secret_key')
            );

            return new YooKassaService($sdk, config('services.yookassa.webhook_secret'));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
