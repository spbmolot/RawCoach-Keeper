<?php

namespace App\Listeners;

use App\Models\EmailSequence;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class StartWelcomeEmailSeries implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Задержка первого письма — 1 час после регистрации,
     * чтобы не перегружать пользователя сразу.
     */
    public int $delay = 3600;

    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        try {
            $user = $event->user;

            EmailSequence::startWelcomeSeries($user);

            Log::info('Welcome email series started', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to start welcome series', [
                'user_id' => $event->user->id ?? null,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
