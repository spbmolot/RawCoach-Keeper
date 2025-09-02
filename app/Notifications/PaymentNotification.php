<?php

namespace App\Notifications;

use App\Models\Payment;
use App\Mail\PaymentSuccessful;
use App\Mail\PaymentFailed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Mail;

class PaymentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Payment $payment;
    public string $type;

    /**
     * Create a new notification instance.
     */
    public function __construct(Payment $payment, string $type = 'success')
    {
        $this->payment = $payment;
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
        if ($this->type === 'success') {
            return (new PaymentSuccessful($this->payment))->to($notifiable->email);
        } else {
            return (new PaymentFailed($this->payment))->to($notifiable->email);
        }
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'payment_id' => $this->payment->id,
            'type' => $this->type,
            'amount' => $this->payment->amount,
            'currency' => $this->payment->currency,
            'status' => $this->payment->status,
            'message' => $this->type === 'success' 
                ? 'Платеж успешно обработан'
                : 'Проблема с обработкой платежа',
        ];
    }
}
