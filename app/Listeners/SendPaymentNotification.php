<?php

namespace App\Listeners;

use App\Events\PaymentProcessed;
use App\Notifications\PaymentNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendPaymentNotification implements ShouldQueue
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
    public function handle(PaymentProcessed $event): void
    {
        try {
            $user = $event->payment->user;
            
            if ($user && $user->email) {
                $user->notify(new PaymentNotification($event->payment, $event->status));
                
                Log::info('Payment notification sent', [
                    'payment_id' => $event->payment->id,
                    'user_id' => $user->id,
                    'status' => $event->status,
                    'email' => $user->email
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send payment notification', [
                'payment_id' => $event->payment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
