<?php

namespace App\Notifications;

use App\Models\UserSubscription;
use App\Mail\SubscriptionExpiring;
use App\Mail\SubscriptionExpired;
use App\Mail\SubscriptionRenewed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public UserSubscription $subscription;
    public string $type;

    /**
     * Create a new notification instance.
     */
    public function __construct(UserSubscription $subscription, string $type)
    {
        $this->subscription = $subscription;
        $this->type = $type;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return match ($this->type) {
            'expiring' => (new SubscriptionExpiring($this->subscription))->to($notifiable->email),
            'expired' => (new SubscriptionExpired($this->subscription))->to($notifiable->email),
            'renewed' => (new SubscriptionRenewed($this->subscription))->to($notifiable->email),
            default => throw new \InvalidArgumentException("Unknown subscription notification type: {$this->type}")
        };
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $messages = [
            'expiring' => 'Ваша подписка скоро истечет',
            'expired' => 'Ваша подписка истекла',
            'renewed' => 'Подписка успешно продлена',
        ];

        return [
            'subscription_id' => $this->subscription->id,
            'type' => $this->type,
            'plan_name' => $this->subscription->plan->name,
            'ends_at' => $this->subscription->ends_at,
            'message' => $messages[$this->type] ?? 'Уведомление о подписке',
        ];
    }
}
