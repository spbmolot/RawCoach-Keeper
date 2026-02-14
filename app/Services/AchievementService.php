<?php

namespace App\Services;

use App\Models\Achievement;
use App\Models\User;
use Carbon\Carbon;

class AchievementService
{
    /**
     * Проверить и выдать новые достижения пользователю
     */
    public function checkAndUnlock(User $user): array
    {
        $newlyUnlocked = [];
        $unlockedIds = $user->achievements()->pluck('achievements.id')->toArray();

        $achievements = Achievement::whereNotIn('id', $unlockedIds)->get();

        foreach ($achievements as $achievement) {
            if ($this->isConditionMet($user, $achievement)) {
                $user->achievements()->attach($achievement->id, [
                    'unlocked_at' => now(),
                ]);
                $newlyUnlocked[] = $achievement;
            }
        }

        return $newlyUnlocked;
    }

    /**
     * Проверить условие конкретного достижения
     */
    private function isConditionMet(User $user, Achievement $achievement): bool
    {
        $value = $achievement->condition_value;

        return match ($achievement->condition_type) {
            'diary_entries' => $user->foodDiaryEntries()->count() >= $value,
            'diary_days' => $user->foodDiaryEntries()->distinct('date')->count('date') >= $value,
            'diary_streak' => $this->getDiaryStreak($user) >= $value,
            'weight_logs' => $user->weightLogs()->count() >= $value,
            'weight_lost' => $this->getWeightLost($user) >= $value,
            'weight_target' => $this->hasReachedTargetWeight($user),
            'recipes_tried' => $user->foodDiaryEntries()->whereNotNull('recipe_id')->distinct('recipe_id')->count('recipe_id') >= $value,
            'recipes_favorited' => $user->favoriteRecipes()->count() >= $value,
            'subscription_days' => $this->getSubscriptionDays($user) >= $value,
            'profile_complete' => $user->isProfileComplete(),
            'meals_per_day' => $this->hasLoggedFullDay($user, $value),
            default => false,
        };
    }

    /**
     * Серия дней подряд с записями в дневнике
     */
    public function getDiaryStreak(User $user): int
    {
        $dates = $user->foodDiaryEntries()
            ->select('date')
            ->distinct()
            ->orderByDesc('date')
            ->pluck('date')
            ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'));

        if ($dates->isEmpty()) return 0;

        $streak = 0;
        $expected = Carbon::today();

        foreach ($dates as $date) {
            if ($date === $expected->format('Y-m-d')) {
                $streak++;
                $expected->subDay();
            } elseif ($date === $expected->copy()->addDay()->format('Y-m-d')) {
                continue;
            } else {
                break;
            }
        }

        return $streak;
    }

    /**
     * Сколько кг сброшено (первый лог vs последний)
     */
    private function getWeightLost(User $user): float
    {
        $first = $user->weightLogs()->orderBy('date')->first();
        $last = $user->weightLogs()->orderByDesc('date')->first();

        if (!$first || !$last || $first->id === $last->id) return 0;

        $diff = $first->weight - $last->weight;
        return max(0, $diff);
    }

    /**
     * Достигнут ли целевой вес
     */
    private function hasReachedTargetWeight(User $user): bool
    {
        if (!$user->target_weight) return false;

        $latest = $user->weightLogs()->orderByDesc('date')->first();
        if (!$latest) return false;

        return $latest->weight <= $user->target_weight;
    }

    /**
     * Дней подписки всего
     */
    private function getSubscriptionDays(User $user): int
    {
        $sub = $user->activeSubscription;
        if (!$sub || !$sub->started_at) return 0;

        return (int) Carbon::parse($sub->started_at)->diffInDays(now());
    }

    /**
     * Были ли залогированы N приёмов пищи за один день
     */
    private function hasLoggedFullDay(User $user, int $mealsRequired): bool
    {
        return $user->foodDiaryEntries()
            ->selectRaw('date, COUNT(DISTINCT meal_type) as meals')
            ->groupBy('date')
            ->havingRaw('COUNT(DISTINCT meal_type) >= ?', [$mealsRequired])
            ->exists();
    }
}
