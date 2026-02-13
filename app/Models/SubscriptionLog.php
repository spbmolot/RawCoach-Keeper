<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionLog extends Model
{
    protected $fillable = [
        'user_id',
        'subscription_id',
        'payment_id',
        'action',
        'old_status',
        'new_status',
        'old_plan',
        'new_plan',
        'amount',
        'metadata',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'amount' => 'decimal:2',
        ];
    }

    /**
     * Пользователь
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Подписка
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(UserSubscription::class, 'subscription_id');
    }

    /**
     * Платёж
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Создать лог действия
     */
    public static function log(
        string $action,
        User $user,
        ?UserSubscription $subscription = null,
        ?Payment $payment = null,
        array $data = []
    ): self {
        return self::create([
            'user_id' => $user->id,
            'subscription_id' => $subscription?->id,
            'payment_id' => $payment?->id,
            'action' => $action,
            'old_status' => $data['old_status'] ?? null,
            'new_status' => $data['new_status'] ?? null,
            'old_plan' => $data['old_plan'] ?? null,
            'new_plan' => $data['new_plan'] ?? null,
            'amount' => $data['amount'] ?? null,
            'metadata' => $data['metadata'] ?? null,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
        ]);
    }

    /**
     * Получить описание действия
     */
    public function getActionLabel(): string
    {
        return match($this->action) {
            'created' => 'Подписка создана',
            'activated' => 'Подписка активирована',
            'cancelled' => 'Автопродление отключено',
            'expired' => 'Подписка истекла',
            'renewed' => 'Подписка продлена',
            'upgraded' => 'Тариф повышен',
            'downgraded' => 'Тариф понижен',
            'scheduled_change' => 'Запланирована смена тарифа',
            'grace_period' => 'Льготный период',
            'payment_success' => 'Платёж успешен',
            'payment_failed' => 'Платёж не прошёл',
            'role_assigned' => 'Роль назначена',
            'role_removed' => 'Роль снята',
            default => $this->action,
        };
    }
}
