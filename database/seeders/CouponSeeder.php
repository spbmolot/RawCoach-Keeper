<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Coupon;
use Carbon\Carbon;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Приветственная скидка для новых пользователей
        Coupon::firstOrCreate([
            'code' => 'WELCOME20',
            'description' => 'Скидка 20% для новых пользователей на первую подписку',
            'type' => 'percentage',
            'value' => 20.00,
            'minimum_amount' => 1000.00,
            'usage_limit' => 1000,
            'usage_limit_per_user' => 1,
            'is_active' => true,
            'valid_from' => Carbon::now(),
            'valid_until' => Carbon::now()->addMonths(6),
            'applicable_plans' => json_encode(['standard', 'premium'])
        ]);

        // Скидка на годовую подписку
        Coupon::firstOrCreate([
            'code' => 'YEAR30',
            'description' => 'Дополнительная скидка 30% при покупке годовой подписки',
            'type' => 'percentage',
            'value' => 30.00,
            'minimum_amount' => 15000.00,
            'usage_limit' => 500,
            'usage_limit_per_user' => 1,
            'is_active' => true,
            'valid_from' => Carbon::now(),
            'valid_until' => Carbon::now()->addMonths(12),
            'applicable_plans' => json_encode(['premium'])
        ]);

        // Фиксированная скидка на индивидуальный план
        Coupon::firstOrCreate([
            'code' => 'PERSONAL500',
            'description' => 'Фиксированная скидка 500 рублей на персональный план питания',
            'type' => 'fixed',
            'value' => 500.00,
            'minimum_amount' => 4000.00,
            'usage_limit' => 200,
            'usage_limit_per_user' => 2,
            'is_active' => true,
            'valid_from' => Carbon::now(),
            'valid_until' => Carbon::now()->addMonths(3),
            'applicable_plans' => json_encode(['personal'])
        ]);

        // Сезонная акция
        Coupon::firstOrCreate([
            'code' => 'SPRING2024',
            'description' => 'Весенняя скидка 15% на все планы',
            'type' => 'percentage',
            'value' => 15.00,
            'minimum_amount' => 500.00,
            'usage_limit' => 2000,
            'usage_limit_per_user' => 1,
            'is_active' => false, // активируется по сезону
            'valid_from' => Carbon::create(2024, 3, 1),
            'valid_until' => Carbon::create(2024, 5, 31),
            'applicable_plans' => json_encode(['standard', 'premium', 'personal'])
        ]);

        // Реферальный купон
        Coupon::firstOrCreate([
            'code' => 'FRIEND10',
            'description' => 'Скидка 10% за приглашение друга',
            'type' => 'percentage',
            'value' => 10.00,
            'minimum_amount' => 1000.00,
            'usage_limit' => null, // безлимитный
            'usage_limit_per_user' => 5, // до 5 друзей
            'is_active' => true,
            'valid_from' => Carbon::now(),
            'valid_until' => null, // бессрочный
            'applicable_plans' => json_encode(['standard', 'premium'])
        ]);
    }
}
