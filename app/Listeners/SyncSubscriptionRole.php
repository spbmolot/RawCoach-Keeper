<?php

namespace App\Listeners;

use App\Events\SubscriptionStatusChanged;
use App\Services\SubscriptionRoleService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SyncSubscriptionRole implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct(
        private SubscriptionRoleService $roleService
    ) {
    }

    /**
     * Handle the event.
     */
    public function handle(SubscriptionStatusChanged $event): void
    {
        $this->roleService->handleSubscriptionStatusChange(
            $event->subscription,
            $event->status
        );
    }
}
