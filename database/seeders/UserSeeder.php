<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Создание администратора
        $admin = User::firstOrCreate([
            'email' => 'admin@rawplan.ru'
        ], [
            'name' => 'Администратор',
            'first_name' => 'Иван',
            'last_name' => 'Администраторов',
            'email' => 'admin@rawplan.ru',
            'email_verified_at' => now(),
            'password' => Hash::make('admin123'),
            'phone' => '+7 (999) 123-45-67',
            'birth_date' => '1985-05-15',
            'gender' => 'male',
            'height' => 180,
            'weight' => 75.5,
            'activity_level' => 'moderate',
            'goal' => 'maintain',
            'timezone' => 'Europe/Moscow',
            'language' => 'ru',
            'is_active' => true,
            'profile_completed_at' => now(),
        ]);
        $admin->assignRole('admin');

        // Создание редактора/нутрициолога
        $editor = User::firstOrCreate([
            'email' => 'editor@rawplan.ru'
        ], [
            'name' => 'Анна Нутрициолог',
            'first_name' => 'Анна',
            'last_name' => 'Петрова',
            'email' => 'editor@rawplan.ru',
            'email_verified_at' => now(),
            'password' => Hash::make('editor123'),
            'phone' => '+7 (999) 234-56-78',
            'birth_date' => '1990-08-20',
            'gender' => 'female',
            'height' => 165,
            'weight' => 58.0,
            'activity_level' => 'active',
            'goal' => 'maintain',
            'timezone' => 'Europe/Moscow',
            'language' => 'ru',
            'is_active' => true,
            'profile_completed_at' => now(),
            'bio' => 'Сертифицированный нутрициолог с 5-летним опытом работы',
        ]);
        $editor->assignRole('editor');

        // Создание рекламодателя
        $advertiser = User::firstOrCreate([
            'email' => 'advertiser@example.com'
        ], [
            'name' => 'Рекламное Агентство',
            'first_name' => 'Михаил',
            'last_name' => 'Рекламщиков',
            'email' => 'advertiser@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('advertiser123'),
            'phone' => '+7 (999) 345-67-89',
            'birth_date' => '1988-12-10',
            'gender' => 'male',
            'timezone' => 'Europe/Moscow',
            'language' => 'ru',
            'is_active' => true,
            'profile_completed_at' => now(),
        ]);
        $advertiser->assignRole('advertiser');

        // Создание подписчика Premium
        $premiumUser = User::firstOrCreate([
            'email' => 'premium@example.com'
        ], [
            'name' => 'Елена Премиум',
            'first_name' => 'Елена',
            'last_name' => 'Сидорова',
            'email' => 'premium@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('premium123'),
            'phone' => '+7 (999) 456-78-90',
            'birth_date' => '1992-03-25',
            'gender' => 'female',
            'height' => 170,
            'weight' => 65.0,
            'activity_level' => 'moderate',
            'goal' => 'lose_weight',
            'target_weight' => 60.0,
            'allergies' => ['глютен', 'лактоза'],
            'dislikes' => ['грибы', 'морепродукты'],
            'dietary_preferences' => ['vegetarian'],
            'timezone' => 'Europe/Moscow',
            'language' => 'ru',
            'is_active' => true,
            'profile_completed_at' => now(),
        ]);
        $premiumUser->assignRole('subscriber_premium');

        // Создание подписчика с персональным планом
        $personalUser = User::firstOrCreate([
            'email' => 'personal@example.com'
        ], [
            'name' => 'Дмитрий Персональный',
            'first_name' => 'Дмитрий',
            'last_name' => 'Иванов',
            'email' => 'personal@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('personal123'),
            'phone' => '+7 (999) 567-89-01',
            'birth_date' => '1987-07-12',
            'gender' => 'male',
            'height' => 185,
            'weight' => 90.0,
            'activity_level' => 'very_active',
            'goal' => 'gain_muscle',
            'target_weight' => 85.0,
            'allergies' => ['орехи'],
            'dislikes' => ['рыба'],
            'dietary_preferences' => ['high_protein'],
            'timezone' => 'Europe/Moscow',
            'language' => 'ru',
            'is_active' => true,
            'profile_completed_at' => now(),
        ]);
        $personalUser->assignRole('subscriber_personal');

        // Создание обычного подписчика Standard
        $standardUser = User::firstOrCreate([
            'email' => 'standard@example.com'
        ], [
            'name' => 'Ольга Стандарт',
            'first_name' => 'Ольга',
            'last_name' => 'Козлова',
            'email' => 'standard@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('standard123'),
            'phone' => '+7 (999) 678-90-12',
            'birth_date' => '1995-11-08',
            'gender' => 'female',
            'height' => 160,
            'weight' => 55.0,
            'activity_level' => 'light',
            'goal' => 'maintain',
            'timezone' => 'Europe/Moscow',
            'language' => 'ru',
            'is_active' => true,
            'profile_completed_at' => now(),
        ]);
        $standardUser->assignRole('subscriber_standard');

        // Создание обычного пользователя без подписки
        $regularUser = User::firstOrCreate([
            'email' => 'user@example.com'
        ], [
            'name' => 'Алексей Пользователь',
            'first_name' => 'Алексей',
            'last_name' => 'Смирнов',
            'email' => 'user@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('user123'),
            'phone' => '+7 (999) 789-01-23',
            'birth_date' => '1993-09-30',
            'gender' => 'male',
            'height' => 175,
            'weight' => 70.0,
            'activity_level' => 'moderate',
            'goal' => 'lose_weight',
            'target_weight' => 65.0,
            'timezone' => 'Europe/Moscow',
            'language' => 'ru',
            'is_active' => true,
        ]);
        $regularUser->assignRole('user');
    }
}
