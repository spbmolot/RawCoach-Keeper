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
        // Месячный план (Стандарт)
        Plan::firstOrCreate(
            ['slug' => 'standard'], // Проверяем только по slug
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

        // Годовой план (Премиум) со скидкой
        Plan::firstOrCreate(
            ['slug' => 'premium'], // Проверяем только по slug
            [
                'name' => 'Премиум',
                'type' => 'yearly',
                'price' => 17910.00, // 1492.50 * 12 (скидка 25%)
                'original_price' => 23880.00, // 1990 * 12
                'currency' => 'RUB',
                'duration_days' => 365,
                'is_active' => true,
                'sort_order' => 2,
                'description' => 'Годовая подписка со скидкой 25% и расширенными возможностями',
                'features' => json_encode([
                    'Все возможности плана Стандарт',
                    'Доступ к архиву всех планов',
                    'Ранний доступ к новым планам',
                    'Приоритетная поддержка',
                    'Безлимитный экспорт',
                    'Скидка 25% от месячной оплаты'
                ]),
                'limits' => json_encode([
                    'archive_access' => true,
                    'early_access' => true,
                    'personal_plans' => false,
                    'max_exports_per_month' => null // безлимит
                ])
            ]
        );

        // Индивидуальный план
        Plan::firstOrCreate(
            ['slug' => 'personal'], // Проверяем только по slug
            [
                'name' => 'Индивидуальный',
                'type' => 'personal',
                'price' => 4990.00,
                'currency' => 'RUB',
                'duration_days' => 30,
                'is_active' => true,
                'sort_order' => 3,
                'description' => 'Персонализированные планы питания на основе ваших предпочтений',
                'features' => json_encode([
                    'Все возможности плана Премиум',
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

        // Пробный план (7 дней бесплатно)
        Plan::firstOrCreate(
            ['slug' => 'trial'], // Проверяем только по slug
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
    }
}
