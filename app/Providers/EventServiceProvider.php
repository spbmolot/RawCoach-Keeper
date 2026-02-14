<?php

namespace App\Providers;

use App\Events\PaymentProcessed;
use App\Events\SubscriptionStatusChanged;
use App\Listeners\SendPaymentNotification;
use App\Listeners\SendSubscriptionNotification;
use App\Listeners\StartWelcomeEmailSeries;
use App\Listeners\SyncSubscriptionRole;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
            StartWelcomeEmailSeries::class,
        ],
        
        // Payment events
        PaymentProcessed::class => [
            SendPaymentNotification::class,
        ],
        
        // Subscription events
        SubscriptionStatusChanged::class => [
            SendSubscriptionNotification::class,
            SyncSubscriptionRole::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
