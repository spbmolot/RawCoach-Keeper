<?php

namespace App\Listeners;

use App\Events\SubscriptionStatusChanged;
use App\Notifications\SubscriptionNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendSubscriptionNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(SubscriptionStatusChanged $event): void
    {
        try {
            $user = $event->subscription->user;
            
            if ($user && $user->email) {
                $user->notify(new SubscriptionNotification($event->subscription, $event->status));
                
                Log::info('Subscription notification sent', [
                    'subscription_id' => $event->subscription->id,
                    'user_id' => $user->id,
                    'status' => $event->status,
                    'email' => $user->email
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send subscription notification', [
                'subscription_id' => $event->subscription->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
