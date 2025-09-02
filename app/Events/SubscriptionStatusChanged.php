<?php

namespace App\Events;

use App\Models\UserSubscription;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SubscriptionStatusChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public UserSubscription $subscription;
    public string $status;

    /**
     * Create a new event instance.
     */
    public function __construct(UserSubscription $subscription, string $status)
    {
        $this->subscription = $subscription;
        $this->status = $status;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('subscription.' . $this->subscription->user_id),
        ];
    }
}
