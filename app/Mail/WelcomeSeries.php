<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeSeries extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public User $user;
    public int $step;

    /**
     * Конфигурация шагов welcome-серии
     * step => [subject, view, delay_hours_to_next]
     */
    public static array $steps = [
        1 => [
            'subject' => 'Добро пожаловать в RawPlan! 🌿',
            'view' => 'emails.welcome-series.step1-welcome',
            'delay' => 24,
        ],
        2 => [
            'subject' => 'Как устроен RawPlan — краткий гид',
            'view' => 'emails.welcome-series.step2-how-it-works',
            'delay' => 48,
        ],
        3 => [
            'subject' => 'Попробуйте наши бесплатные рецепты',
            'view' => 'emails.welcome-series.step3-free-recipes',
            'delay' => 48,
        ],
        4 => [
            'subject' => 'Что входит в подписку RawPlan',
            'view' => 'emails.welcome-series.step4-subscription-benefits',
            'delay' => 72,
        ],
        5 => [
            'subject' => 'Специальное предложение для вас — скидка 25%',
            'view' => 'emails.welcome-series.step5-special-offer',
            'delay' => 0,
        ],
    ];

    public function __construct(User $user, int $step)
    {
        $this->user = $user;
        $this->step = $step;
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
                'totalSteps' => count(self::$steps),
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }

    /**
     * Задержка в часах до следующего шага
     */
    public static function getDelayHours(int $step): int
    {
        return self::$steps[$step]['delay'] ?? 24;
    }
}
