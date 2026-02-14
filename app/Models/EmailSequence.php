<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailSequence extends Model
{
    protected $fillable = [
        'user_id',
        'sequence_type',
        'current_step',
        'total_steps',
        'last_sent_at',
        'next_send_at',
        'completed_at',
        'unsubscribed_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'last_sent_at' => 'datetime',
            'next_send_at' => 'datetime',
            'completed_at' => 'datetime',
            'unsubscribed_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Серия активна (не завершена, не отписана)
     */
    public function isActive(): bool
    {
        return is_null($this->completed_at) && is_null($this->unsubscribed_at);
    }

    /**
     * Пора отправлять следующее письмо
     */
    public function isReadyToSend(): bool
    {
        return $this->isActive()
            && $this->current_step < $this->total_steps
            && ($this->next_send_at === null || $this->next_send_at->lte(now()));
    }

    /**
     * Пометить шаг как отправленный и назначить следующую отправку
     */
    public function advanceStep(int $delayHours = 24): void
    {
        $this->current_step++;
        $this->last_sent_at = now();

        if ($this->current_step >= $this->total_steps) {
            $this->completed_at = now();
            $this->next_send_at = null;
        } else {
            $this->next_send_at = now()->addHours($delayHours);
        }

        $this->save();
    }

    /**
     * Создать welcome-серию для пользователя
     */
    public static function startWelcomeSeries(User $user): self
    {
        return self::updateOrCreate(
            ['user_id' => $user->id, 'sequence_type' => 'welcome'],
            [
                'current_step' => 0,
                'total_steps' => 5,
                'next_send_at' => now(),
                'completed_at' => null,
                'unsubscribed_at' => null,
            ]
        );
    }

    /**
     * Создать реактивационную серию
     */
    public static function startReactivation(User $user): self
    {
        return self::updateOrCreate(
            ['user_id' => $user->id, 'sequence_type' => 'reactivation'],
            [
                'current_step' => 0,
                'total_steps' => 3,
                'next_send_at' => now(),
                'completed_at' => null,
                'unsubscribed_at' => null,
            ]
        );
    }
}
