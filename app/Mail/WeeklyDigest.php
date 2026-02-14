<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class WeeklyDigest extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public User $user;
    public Collection $weekDays;
    public Collection $newRecipes;
    public ?string $menuTitle;

    public function __construct(
        User $user,
        Collection $weekDays,
        Collection $newRecipes,
        ?string $menuTitle = null
    ) {
        $this->user = $user;
        $this->weekDays = $weekDays;
        $this->newRecipes = $newRecipes;
        $this->menuTitle = $menuTitle;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Ваше меню на неделю — RawPlan',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.weekly-digest',
            with: [
                'user' => $this->user,
                'weekDays' => $this->weekDays,
                'newRecipes' => $this->newRecipes,
                'menuTitle' => $this->menuTitle,
                'hasSubscription' => $this->user->hasActiveSubscription(),
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
