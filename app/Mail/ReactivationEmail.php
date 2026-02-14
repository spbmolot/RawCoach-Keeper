<?php

namespace App\Mail;

use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReactivationEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public User $user;
    public int $step;
    public ?UserSubscription $lastSubscription;

    /**
     * Шаги реактивационной серии
     */
    public static array $steps = [
        1 => [
            'subject' => 'Мы скучаем! Вернитесь в RawPlan',
            'view' => 'emails.reactivation.step1-we-miss-you',
            'delay' => 72,
        ],
        2 => [
            'subject' => 'Что нового в RawPlan — свежее меню ждёт вас',
            'view' => 'emails.reactivation.step2-whats-new',
            'delay' => 120,
        ],
        3 => [
            'subject' => 'Последний шанс — скидка 30% на возвращение',
            'view' => 'emails.reactivation.step3-last-chance',
            'delay' => 0,
        ],
    ];

    public function __construct(User $user, int $step, ?UserSubscription $lastSubscription = null)
    {
        $this->user = $user;
        $this->step = $step;
        $this->lastSubscription = $lastSubscription;
    }

    public function envelope(): Envelope
    {
        $config = self::$steps[$this->step] ?? self::$steps[1];

        return new Envelope(
            subject: $config['subject'],
        );
    }

    public function content(): Content
    {
        $config = self::$steps[$this->step] ?? self::$steps[1];

        return new Content(
            view: $config['view'],
            with: [
                'user' => $this->user,
                'step' => $this->step,
                'lastSubscription' => $this->lastSubscription,
                'daysSinceExpired' => $this->lastSubscription?->ends_at?->diffInDays(now()),
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }

    public static function getDelayHours(int $step): int
    {
        return self::$steps[$step]['delay'] ?? 72;
    }
}
