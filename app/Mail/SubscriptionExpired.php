<?php

namespace App\Mail;

use App\Models\UserSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionExpired extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public UserSubscription $subscription;

    /**
     * Create a new message instance.
     */
    public function __construct(UserSubscription $subscription)
    {
        $this->subscription = $subscription;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Ваша подписка истекла - RawPlan',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.subscription-expired',
            with: [
                'subscription' => $this->subscription,
                'user' => $this->subscription->user,
                'plan' => $this->subscription->plan,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
