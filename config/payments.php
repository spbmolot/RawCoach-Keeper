<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Платежные провайдеры
    |--------------------------------------------------------------------------
    |
    | Конфигурация для различных платежных провайдеров
    |
    */

    'default_provider' => env('PAYMENT_DEFAULT_PROVIDER', null),

    'providers' => [
        'yookassa' => [
            'enabled' => env('YOOKASSA_ENABLED', false),
            'shop_id' => env('YOOKASSA_SHOP_ID'),
            'secret_key' => env('YOOKASSA_SECRET_KEY'),
            'webhook_secret' => env('YOOKASSA_WEBHOOK_SECRET'),
            'test_mode' => env('YOOKASSA_TEST_MODE', true),
            'currency' => 'RUB',
            'webhook_url' => env('APP_URL') . '/webhooks/yookassa',
        ],

        'cloudpayments' => [
            'enabled' => env('CLOUDPAYMENTS_ENABLED', false),
            'public_id' => env('CLOUDPAYMENTS_PUBLIC_ID'),
            'api_secret' => env('CLOUDPAYMENTS_API_SECRET'),
            'webhook_secret' => env('CLOUDPAYMENTS_WEBHOOK_SECRET'),
            'test_mode' => env('CLOUDPAYMENTS_TEST_MODE', true),
            'currency' => 'RUB',
            'webhook_url' => env('APP_URL') . '/webhooks/cloudpayments',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Настройки платежей
    |--------------------------------------------------------------------------
    */

    'settings' => [
        // Минимальная сумма платежа
        'min_amount' => 100,

        // Максимальная сумма платежа
        'max_amount' => 100000,

        // Валюта по умолчанию
        'default_currency' => 'RUB',

        // Время жизни платежа (в минутах)
        'payment_lifetime' => 60,

        // Автоматическое подтверждение платежей
        'auto_capture' => true,

        // Включить логирование всех операций
        'enable_logging' => env('PAYMENT_LOGGING', true),

        // Включить тестовый режим
        'test_mode' => env('PAYMENT_TEST_MODE', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Комиссии провайдеров
    |--------------------------------------------------------------------------
    */

    'fees' => [
        'yookassa' => [
            'percentage' => 2.8, // %
            'fixed' => 0, // руб
        ],
        'cloudpayments' => [
            'percentage' => 2.5, // %
            'fixed' => 0, // руб
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Уведомления
    |--------------------------------------------------------------------------
    */

    'notifications' => [
        // Email уведомления о платежах
        'email_notifications' => env('PAYMENT_EMAIL_NOTIFICATIONS', true),

        // Telegram уведомления (если настроен бот)
        'telegram_notifications' => env('PAYMENT_TELEGRAM_NOTIFICATIONS', false),

        // Уведомления администраторам
        'admin_notifications' => [
            'failed_payments' => true,
            'large_payments' => true, // > 10000 руб
            'refunds' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Автопродление подписок
    |--------------------------------------------------------------------------
    */

    'auto_renewal' => [
        // Включить автопродление
        'enabled' => env('AUTO_RENEWAL_ENABLED', true),

        // За сколько дней до истечения создавать новый платеж
        'days_before_expiry' => 3,

        // Максимальное количество попыток автопродления
        'max_attempts' => 3,

        // Интервал между попытками (в часах)
        'retry_interval' => 24,
    ],

    /*
    |--------------------------------------------------------------------------
    | Возвраты
    |--------------------------------------------------------------------------
    */

    'refunds' => [
        // Автоматические возвраты при отмене подписки
        'auto_refund_on_cancel' => false,

        // Период для возврата без штрафа (в днях)
        'free_refund_period' => 7,

        // Комиссия за возврат после бесплатного периода (%)
        'refund_fee_percentage' => 10,
    ],

    /*
    |--------------------------------------------------------------------------
    | Скидки и промокоды
    |--------------------------------------------------------------------------
    */

    'discounts' => [
        // Максимальная скидка по промокоду (%)
        'max_discount_percentage' => 50,

        // Максимальная скидка по промокоду (руб)
        'max_discount_amount' => 5000,

        // Скидка на годовую подписку (%)
        'annual_discount' => 20,
    ],
];
