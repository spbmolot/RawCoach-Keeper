<?php

namespace App\Providers;

use App\Services\PaymentService;
use App\Services\Payments\YooKassaService;
use App\Services\Payments\CloudPaymentsService;
use CloudPayments\Manager as CloudPaymentsManager;
use Illuminate\Support\ServiceProvider;
use YooKassa\Client as YooKassaClient;

class PaymentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Регистрируем YooKassa клиент
        $this->app->singleton(YooKassaClient::class, function ($app) {
            $client = new YooKassaClient();
            
            if (config('payments.providers.yookassa.enabled')) {
                $client->setAuth(
                    config('payments.providers.yookassa.shop_id'),
                    config('payments.providers.yookassa.secret_key')
                );
            }
            
            return $client;
        });

        // Регистрируем CloudPayments клиент
        $this->app->singleton(CloudPaymentsManager::class, function ($app) {
            return new CloudPaymentsManager(
                config('payments.providers.cloudpayments.public_id'),
                config('payments.providers.cloudpayments.api_secret')
            );
        });

        // Регистрируем YooKassa сервис
        $this->app->singleton(YooKassaService::class, function ($app) {
            return new YooKassaService(
                $app->make(YooKassaClient::class),
                config('payments.providers.yookassa.webhook_secret')
            );
        });

        // Регистрируем CloudPayments сервис
        $this->app->singleton(CloudPaymentsService::class, function ($app) {
            return new CloudPaymentsService(
                $app->make(CloudPaymentsManager::class),
                config('payments.providers.cloudpayments.webhook_secret')
            );
        });

        // Регистрируем основной платежный сервис
        $this->app->singleton(PaymentService::class, function ($app) {
            return new PaymentService(
                $app->make(YooKassaService::class),
                $app->make(CloudPaymentsService::class)
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Публикуем конфигурацию
        $this->publishes([
            __DIR__.'/../../config/payments.php' => config_path('payments.php'),
        ], 'payment-config');

        // Валидируем конфигурацию при загрузке
        $this->validatePaymentConfig();
    }

    /**
     * Валидация конфигурации платежных провайдеров
     */
    private function validatePaymentConfig(): void
    {
        if (!config('payments.default_provider')) {
            throw new \InvalidArgumentException('Не указан провайдер платежей по умолчанию');
        }

        $defaultProvider = config('payments.default_provider');
        $providerConfig = config("payments.providers.{$defaultProvider}");

        if (!$providerConfig || !$providerConfig['enabled']) {
            throw new \InvalidArgumentException("Провайдер {$defaultProvider} не настроен или отключен");
        }

        // Проверяем обязательные параметры для YooKassa
        if ($defaultProvider === 'yookassa') {
            if (!config('payments.providers.yookassa.shop_id') || 
                !config('payments.providers.yookassa.secret_key')) {
                \Log::warning('Не настроены обязательные параметры для YooKassa. Платежи могут не работать корректно.');
            }
        }

        // Проверяем обязательные параметры для CloudPayments
        if ($defaultProvider === 'cloudpayments') {
            if (!config('payments.providers.cloudpayments.public_id') || 
                !config('payments.providers.cloudpayments.api_secret')) {
                \Log::warning('Не настроены обязательные параметры для CloudPayments. Платежи могут не работать корректно.');
            }
        }
    }
}
