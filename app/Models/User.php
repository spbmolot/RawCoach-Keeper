<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\FilamentUser;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles;

    /**
     * Per-request кеш активной подписки (избегаем повторных SQL-запросов)
     */
    protected ?UserSubscription $_cachedSubscription;
    protected bool $_subscriptionLoaded = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'birth_date',
        'gender',
        'avatar',
        'height',
        'weight',
        'target_weight',
        'activity_level',
        'dietary_preferences',
        'allergies',
        'disliked_foods',
        'email_notifications',
        'sms_notifications',
        'push_notifications',
        'timezone',
        'language',
        'bio',
        'settings',
        'is_active',
        'onboarding_completed_at',
        'onboarding_goal',
        'referral_code',
        'referred_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birth_date' => 'date',
            'height' => 'decimal:2',
            'weight' => 'decimal:2',
            'target_weight' => 'decimal:2',
            'dietary_preferences' => 'array',
            'allergies' => 'array',
            'disliked_foods' => 'array',
            'settings' => 'array',
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'push_notifications' => 'boolean',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'onboarding_completed_at' => 'datetime',
        ];
    }

    /**
     * Активная подписка пользователя
     */
    public function activeSubscription()
    {
        return $this->hasOne(UserSubscription::class)
            ->where('status', 'active')
            ->where('ends_at', '>', now());
    }

    /**
     * Все подписки пользователя
     */
    public function subscriptions()
    {
        return $this->hasMany(UserSubscription::class);
    }

    /**
     * Платежи пользователя
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Анкеты пользователя
     */
    public function questionnaires()
    {
        return $this->hasMany(Questionnaire::class);
    }

    /**
     * Персональные планы
     */
    public function personalPlans()
    {
        return $this->hasMany(PersonalPlan::class);
    }

    /**
     * Избранные рецепты
     */
    public function favoriteRecipes()
    {
        return $this->belongsToMany(Recipe::class, 'user_favorite_recipes')
            ->withTimestamps();
    }

    /**
     * Избранные меню
     */
    public function favoriteMenus()
    {
        return $this->belongsToMany(Menu::class, 'user_favorite_menus')
            ->withTimestamps();
    }

    /**
     * Использования купонов
     */
    public function couponUsages()
    {
        return $this->hasMany(CouponUsage::class);
    }

    /**
     * Email-серии пользователя
     */
    public function emailSequences()
    {
        return $this->hasMany(EmailSequence::class);
    }

    /**
     * Дневник питания
     */
    public function foodDiaryEntries()
    {
        return $this->hasMany(FoodDiaryEntry::class);
    }

    /**
     * Лог веса
     */
    public function weightLogs()
    {
        return $this->hasMany(WeightLog::class)->orderBy('date');
    }

    /**
     * Достижения пользователя
     */
    public function achievements()
    {
        return $this->belongsToMany(Achievement::class, 'user_achievements')
            ->withPivot('unlocked_at')
            ->withTimestamps();
    }

    /**
     * Кто пригласил этого пользователя
     */
    public function referrer()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    /**
     * Рефералы этого пользователя (кого он пригласил)
     */
    public function referrals()
    {
        return $this->hasMany(Referral::class, 'referrer_id');
    }

    /**
     * Награды за рефералов
     */
    public function referralRewards()
    {
        return $this->hasMany(ReferralReward::class);
    }

    /**
     * Замены рецептов в меню
     */
    public function mealSwaps()
    {
        return $this->hasMany(UserMealSwap::class);
    }

    /**
     * Активный персональный план
     */
    public function activePersonalPlan()
    {
        return $this->hasOne(PersonalPlan::class)
            ->where('status', 'active')
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now());
    }

    /**
     * Последняя заполненная анкета
     */
    public function latestQuestionnaire()
    {
        return $this->hasOne(Questionnaire::class)
            ->where('is_completed', true)
            ->latest('completed_at');
    }

    /**
     * Получить активную подписку с plan (мемоизировано per-request)
     */
    public function getCachedSubscription(): ?UserSubscription
    {
        if (!$this->_subscriptionLoaded) {
            $this->_cachedSubscription = $this->activeSubscription()->with('plan')->first();
            $this->_subscriptionLoaded = true;
        }
        return $this->_cachedSubscription;
    }

    /**
     * Сбросить кеш подписки (при изменении)
     */
    public function clearSubscriptionCache(): void
    {
        $this->_subscriptionLoaded = false;
        $this->_cachedSubscription = null;
    }

    /**
     * Проверка активной подписки
     */
    public function hasActiveSubscription(): bool
    {
        return $this->getCachedSubscription() !== null;
    }

    /**
     * Проверка прохождения онбординга
     */
    public function hasCompletedOnboarding(): bool
    {
        return $this->onboarding_completed_at !== null;
    }

    /**
     * Отметить онбординг как завершённый
     */
    public function completeOnboarding(): void
    {
        $this->update(['onboarding_completed_at' => now()]);
    }

    /**
     * Проверка использования пробного периода
     */
    public function hasUsedTrial(): bool
    {
        return $this->subscriptions()
            ->whereHas('plan', function($query) {
                $query->where('type', 'trial');
            })
            ->exists();
    }

    /**
     * Проверка доступа к контенту
     */
    public function canAccessContent(): bool
    {
        return $this->hasActiveSubscription() || $this->hasRole(['admin', 'editor']);
    }

    /**
     * Получить полное имя
     */
    public function getFullName(): string
    {
        if ($this->first_name && $this->last_name) {
            return "{$this->first_name} {$this->last_name}";
        }
        
        return $this->name;
    }

    /**
     * Получить возраст
     */
    public function getAge(): ?int
    {
        return $this->birth_date ? $this->birth_date->age : null;
    }

    /**
     * Проверка заполненности профиля
     */
    public function isProfileComplete(): bool
    {
        return !empty($this->first_name) && 
               !empty($this->last_name) && 
               !empty($this->phone) && 
               !empty($this->birth_date) && 
               !empty($this->gender);
    }

    /**
     * Проверка наличия персональных данных для планирования
     */
    public function hasPersonalData(): bool
    {
        return !empty($this->height) && 
               !empty($this->weight) && 
               !empty($this->activity_level);
    }

    /**
     * Получить ИМТ (индекс массы тела)
     */
    public function getBMI(): ?float
    {
        if (!$this->height || !$this->weight) {
            return null;
        }

        $heightInMeters = $this->height / 100;
        return round($this->weight / ($heightInMeters * $heightInMeters), 1);
    }

    /**
     * Получить статус ИМТ
     */
    public function getBMIStatus(): ?string
    {
        $bmi = $this->getBMI();
        
        if (!$bmi) {
            return null;
        }

        if ($bmi < 18.5) return 'Недостаточный вес';
        if ($bmi < 25) return 'Нормальный вес';
        if ($bmi < 30) return 'Избыточный вес';
        return 'Ожирение';
    }

    /**
     * Обновить время последнего входа
     */
    public function updateLastLogin(string $ip = null): void
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ip ?? request()->ip(),
        ]);
    }

    /**
     * Проверка активности пользователя
     */
    public function isActive(): bool
    {
        return $this->is_active && $this->email_verified_at;
    }

    /**
     * Деактивировать пользователя
     */
    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    /**
     * Активировать пользователя
     */
    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    /**
     * Проверка доступа к панели администратора Filament
     * Determine if the user can access the Filament admin panel.
     */
    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        return $this->hasAnyRole(['admin', 'editor']) && $this->is_active;
    }
}
