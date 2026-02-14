<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InactivityReminder extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public User $user;
    public int $daysInactive;

    public function __construct(User $user, int $daysInactive)
    {
        $this->user = $user;
        $this->daysInactive = $daysInactive;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Вы пропустили ' . $this->daysInactive . ' дня — ваш план ждёт!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.inactivity-reminder',
            with: [
                'user' => $this->user,
                'daysInactive' => $this->daysInactive,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
