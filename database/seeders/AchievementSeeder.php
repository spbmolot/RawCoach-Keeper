<?php

namespace Database\Seeders;

use App\Models\Achievement;
use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    public function run(): void
    {
        $achievements = [
            // Питание
            ['code' => 'first_entry', 'name' => 'Первый шаг', 'description' => 'Сделайте первую запись в дневнике питания', 'icon' => 'pencil', 'category' => 'nutrition', 'condition_type' => 'diary_entries', 'condition_value' => 1, 'sort_order' => 1],
            ['code' => 'diary_10', 'name' => 'Привычка', 'description' => 'Запишите 10 приёмов пищи', 'icon' => 'notebook-pen', 'category' => 'nutrition', 'condition_type' => 'diary_entries', 'condition_value' => 10, 'sort_order' => 2],
            ['code' => 'diary_50', 'name' => 'Дневник мастера', 'description' => 'Запишите 50 приёмов пищи', 'icon' => 'book-open-check', 'category' => 'nutrition', 'condition_type' => 'diary_entries', 'condition_value' => 50, 'sort_order' => 3],
            ['code' => 'diary_100', 'name' => 'Летописец', 'description' => 'Запишите 100 приёмов пищи', 'icon' => 'scroll-text', 'category' => 'nutrition', 'condition_type' => 'diary_entries', 'condition_value' => 100, 'sort_order' => 4],
            ['code' => 'full_day', 'name' => 'Полный день', 'description' => 'Запишите завтрак, обед, ужин и перекус за один день', 'icon' => 'utensils', 'category' => 'nutrition', 'condition_type' => 'meals_per_day', 'condition_value' => 4, 'sort_order' => 5],

            // Серии
            ['code' => 'streak_3', 'name' => 'Три дня подряд', 'description' => 'Ведите дневник 3 дня подряд', 'icon' => 'flame', 'category' => 'streak', 'condition_type' => 'diary_streak', 'condition_value' => 3, 'sort_order' => 10],
            ['code' => 'streak_7', 'name' => 'Неделя силы', 'description' => 'Ведите дневник 7 дней подряд', 'icon' => 'flame', 'category' => 'streak', 'condition_type' => 'diary_streak', 'condition_value' => 7, 'sort_order' => 11],
            ['code' => 'streak_14', 'name' => 'Две недели', 'description' => 'Ведите дневник 14 дней подряд', 'icon' => 'zap', 'category' => 'streak', 'condition_type' => 'diary_streak', 'condition_value' => 14, 'sort_order' => 12],
            ['code' => 'streak_30', 'name' => 'Месяц дисциплины', 'description' => 'Ведите дневник 30 дней подряд', 'icon' => 'crown', 'category' => 'streak', 'condition_type' => 'diary_streak', 'condition_value' => 30, 'sort_order' => 13],

            // Вес
            ['code' => 'first_weigh', 'name' => 'На весы!', 'description' => 'Сделайте первый замер веса', 'icon' => 'scale', 'category' => 'weight', 'condition_type' => 'weight_logs', 'condition_value' => 1, 'sort_order' => 20],
            ['code' => 'weight_10', 'name' => 'Регулярность', 'description' => 'Запишите вес 10 раз', 'icon' => 'scale', 'category' => 'weight', 'condition_type' => 'weight_logs', 'condition_value' => 10, 'sort_order' => 21],
            ['code' => 'lost_1kg', 'name' => 'Первый килограмм', 'description' => 'Сбросьте 1 кг', 'icon' => 'trending-down', 'category' => 'weight', 'condition_type' => 'weight_lost', 'condition_value' => 1, 'sort_order' => 22],
            ['code' => 'lost_5kg', 'name' => 'Пять кило долой', 'description' => 'Сбросьте 5 кг', 'icon' => 'trending-down', 'category' => 'weight', 'condition_type' => 'weight_lost', 'condition_value' => 5, 'sort_order' => 23],
            ['code' => 'lost_10kg', 'name' => 'Десятка!', 'description' => 'Сбросьте 10 кг', 'icon' => 'medal', 'category' => 'weight', 'condition_type' => 'weight_lost', 'condition_value' => 10, 'sort_order' => 24],
            ['code' => 'target_reached', 'name' => 'Цель достигнута', 'description' => 'Достигните целевого веса', 'icon' => 'target', 'category' => 'weight', 'condition_type' => 'weight_target', 'condition_value' => 1, 'sort_order' => 25],

            // Исследование
            ['code' => 'recipes_5', 'name' => 'Гурман', 'description' => 'Попробуйте 5 разных рецептов', 'icon' => 'chef-hat', 'category' => 'exploration', 'condition_type' => 'recipes_tried', 'condition_value' => 5, 'sort_order' => 30],
            ['code' => 'recipes_20', 'name' => 'Шеф-повар', 'description' => 'Попробуйте 20 разных рецептов', 'icon' => 'chef-hat', 'category' => 'exploration', 'condition_type' => 'recipes_tried', 'condition_value' => 20, 'sort_order' => 31],
            ['code' => 'fav_5', 'name' => 'Коллекционер', 'description' => 'Добавьте 5 рецептов в избранное', 'icon' => 'heart', 'category' => 'exploration', 'condition_type' => 'recipes_favorited', 'condition_value' => 5, 'sort_order' => 32],

            // Социальное / профиль
            ['code' => 'profile_done', 'name' => 'Знакомство', 'description' => 'Заполните профиль полностью', 'icon' => 'user-check', 'category' => 'social', 'condition_type' => 'profile_complete', 'condition_value' => 1, 'sort_order' => 40],
            ['code' => 'sub_30', 'name' => 'Месяц с нами', 'description' => 'Будьте подписчиком 30 дней', 'icon' => 'calendar-check', 'category' => 'social', 'condition_type' => 'subscription_days', 'condition_value' => 30, 'sort_order' => 41],
            ['code' => 'sub_90', 'name' => 'Квартал здоровья', 'description' => 'Будьте подписчиком 90 дней', 'icon' => 'award', 'category' => 'social', 'condition_type' => 'subscription_days', 'condition_value' => 90, 'sort_order' => 42],
        ];

        foreach ($achievements as $data) {
            Achievement::updateOrCreate(
                ['code' => $data['code']],
                $data
            );
        }
    }
}
