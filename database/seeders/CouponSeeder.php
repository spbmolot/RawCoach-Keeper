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
            'name' => 'Приветственная скидка 20%',
            'description' => 'Скидка 20% для новых пользователей на первую подписку',
            'type' => 'percentage',
            'value' => 20.00,
            'min_amount' => 1000.00,
            'usage_limit' => 1000,
            'usage_limit_per_user' => 1,
            'is_active' => true,
            'starts_at' => Carbon::now(),
            'expires_at' => Carbon::now()->addMonths(6),
            'applicable_plans' => ['standard', 'premium'],
            'conditions' => [
                'new_users_only' => true,
                'first_purchase_only' => true
            ]
        ]);

        // Скидка на годовую подписку
        Coupon::firstOrCreate([
            'code' => 'YEAR30',
            'name' => 'Дополнительная скидка 30% на годовую подписку',
            'description' => 'Дополнительная скидка 30% при покупке годовой подписки',
            'type' => 'percentage',
            'value' => 30.00,
            'min_amount' => 15000.00,
            'usage_limit' => 500,
            'usage_limit_per_user' => 1,
            'is_active' => true,
            'starts_at' => Carbon::now(),
            'expires_at' => Carbon::now()->addMonths(12),
            'applicable_plans' => ['premium'],
            'conditions' => [
                'plan_type' => 'yearly'
            ]
        ]);

        // Фиксированная скидка на индивидуальный план
        Coupon::firstOrCreate([
            'code' => 'PERSONAL500',
            'name' => 'Скидка 500 рублей на индивидуальный план',
            'description' => 'Фиксированная скидка 500 рублей на персональный план питания',
            'type' => 'fixed',
            'value' => 500.00,
            'min_amount' => 4000.00,
            'usage_limit' => 200,
            'usage_limit_per_user' => 2,
            'is_active' => true,
            'starts_at' => Carbon::now(),
            'expires_at' => Carbon::now()->addMonths(3),
            'applicable_plans' => ['personal'],
            'conditions' => []
        ]);

        // Сезонная акция
        Coupon::firstOrCreate([
            'code' => 'SPRING2024',
            'name' => 'Весенняя акция 15%',
            'description' => 'Весенняя скидка 15% на все планы',
            'type' => 'percentage',
            'value' => 15.00,
            'min_amount' => 500.00,
            'usage_limit' => 2000,
            'usage_limit_per_user' => 1,
            'is_active' => false, // активируется по сезону
            'starts_at' => Carbon::create(2024, 3, 1),
            'expires_at' => Carbon::create(2024, 5, 31),
            'applicable_plans' => ['standard', 'premium', 'personal'],
            'conditions' => []
        ]);

        // Реферальный купон
        Coupon::firstOrCreate([
            'code' => 'FRIEND10',
            'name' => 'Скидка за друга 10%',
            'description' => 'Скидка 10% за приглашение друга',
            'type' => 'percentage',
            'value' => 10.00,
            'min_amount' => 1000.00,
            'usage_limit' => null, // безлимитный
            'usage_limit_per_user' => 5, // до 5 друзей
            'is_active' => true,
            'starts_at' => Carbon::now(),
            'expires_at' => null, // бессрочный
            'applicable_plans' => ['standard', 'premium'],
            'conditions' => [
                'referral_only' => true
            ]
        ]);
    }
}
