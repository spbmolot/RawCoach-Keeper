<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Пробный план (7 дней бесплатно)
        Plan::firstOrCreate(
            ['slug' => 'trial'],
            [
                'name' => 'Пробный период',
                'type' => 'trial',
                'price' => 0.00,
                'currency' => 'RUB',
                'duration_days' => 7,
                'is_active' => true,
                'sort_order' => 0,
                'description' => '7 дней бесплатного доступа для новых пользователей',
                'features' => json_encode([
                    'Доступ к текущей неделе планов',
                    'Ограниченные рецепты',
                    'Базовые списки покупок',
                    'Один экспорт в PDF'
                ]),
                'limits' => json_encode([
                    'archive_access' => false,
                    'early_access' => false,
                    'personal_plans' => false,
                    'max_exports_per_month' => 1,
                    'max_recipes_per_day' => 3
                ])
            ]
        );

        // Стандарт — месячный
        Plan::firstOrCreate(
            ['slug' => 'standard-monthly'],
            [
                'name' => 'Стандарт',
                'type' => 'monthly',
                'price' => 1990.00,
                'currency' => 'RUB',
                'duration_days' => 30,
                'is_active' => true,
                'sort_order' => 1,
                'description' => 'Доступ к текущему месяцу планов питания',
                'features' => json_encode([
                    'Актуальные планы питания на месяц',
                    'Подробные рецепты с граммовкой',
                    'Калорийность 1200-1400 ккал/день',
                    'Списки покупок',
                    'Экспорт в PDF и Excel',
                    'Поддержка в чате'
                ]),
                'limits' => json_encode([
                    'archive_access' => false,
                    'early_access' => false,
                    'personal_plans' => false,
                    'max_exports_per_month' => 10
                ])
            ]
        );

        // Стандарт — годовой (скидка 25%)
        Plan::firstOrCreate(
            ['slug' => 'standard-yearly'],
            [
                'name' => 'Стандарт',
                'type' => 'yearly',
                'price' => 17910.00, // 1990 * 12 * 0.75
                'original_price' => 23880.00, // 1990 * 12
                'currency' => 'RUB',
                'duration_days' => 365,
                'is_active' => true,
                'sort_order' => 2,
                'description' => 'Годовая подписка со скидкой 25%',
                'features' => json_encode([
                    'Актуальные планы питания на месяц',
                    'Подробные рецепты с граммовкой',
                    'Калорийность 1200-1400 ккал/день',
                    'Списки покупок',
                    'Экспорт в PDF и Excel',
                    'Поддержка в чате',
                    'Доступ к архиву всех планов',
                    'Скидка 25%'
                ]),
                'limits' => json_encode([
                    'archive_access' => true,
                    'early_access' => false,
                    'personal_plans' => false,
                    'max_exports_per_month' => null
                ])
            ]
        );

        // Индивидуальный — месячный
        Plan::firstOrCreate(
            ['slug' => 'personal-monthly'],
            [
                'name' => 'Индивидуальный',
                'type' => 'monthly',
                'price' => 4990.00,
                'currency' => 'RUB',
                'duration_days' => 30,
                'is_active' => true,
                'sort_order' => 3,
                'description' => 'Персонализированные планы питания',
                'features' => json_encode([
                    'Все возможности плана Стандарт',
                    'Персональный план питания',
                    'Учет аллергий и предпочтений',
                    'Индивидуальная калорийность',
                    'Консультация нутрициолога',
                    'Корректировка плана в течение месяца'
                ]),
                'limits' => json_encode([
                    'archive_access' => true,
                    'early_access' => true,
                    'personal_plans' => true,
                    'max_exports_per_month' => null,
                    'nutritionist_consultations' => 2
                ])
            ]
        );

        // Индивидуальный — годовой (скидка 25%)
        Plan::firstOrCreate(
            ['slug' => 'personal-yearly'],
            [
                'name' => 'Индивидуальный',
                'type' => 'yearly',
                'price' => 44910.00, // 4990 * 12 * 0.75
                'original_price' => 59880.00, // 4990 * 12
                'currency' => 'RUB',
                'duration_days' => 365,
                'is_active' => true,
                'sort_order' => 4,
                'description' => 'Годовая персональная подписка со скидкой 25%',
                'features' => json_encode([
                    'Все возможности плана Стандарт',
                    'Персональный план питания',
                    'Учет аллергий и предпочтений',
                    'Индивидуальная калорийность',
                    'Консультация нутрициолога',
                    'Корректировка плана в течение месяца',
                    'Скидка 25%'
                ]),
                'limits' => json_encode([
                    'archive_access' => true,
                    'early_access' => true,
                    'personal_plans' => true,
                    'max_exports_per_month' => null,
                    'nutritionist_consultations' => 24
                ])
            ]
        );

        // Деактивируем старые планы если они существуют
        Plan::whereIn('slug', ['standard', 'premium', 'personal'])->update(['is_active' => false]);
    }
}
