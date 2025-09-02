<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Профильные поля
            $table->string('first_name')->nullable()->after('name');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('phone')->nullable()->after('email');
            $table->date('birth_date')->nullable()->after('phone');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('birth_date');
            $table->string('avatar')->nullable()->after('gender');
            
            // Физические параметры
            $table->decimal('height', 5, 2)->nullable()->after('avatar'); // см
            $table->decimal('weight', 5, 2)->nullable()->after('height'); // кг
            $table->decimal('target_weight', 5, 2)->nullable()->after('weight'); // кг
            $table->enum('activity_level', ['sedentary', 'lightly_active', 'moderately_active', 'very_active', 'extremely_active'])->nullable()->after('target_weight');
            
            // Предпочтения питания
            $table->json('dietary_preferences')->nullable()->after('activity_level'); // вегетарианство, веганство и т.д.
            $table->json('allergies')->nullable()->after('dietary_preferences'); // аллергии
            $table->json('disliked_foods')->nullable()->after('allergies'); // нелюбимые продукты
            
            // Настройки уведомлений
            $table->boolean('email_notifications')->default(true)->after('disliked_foods');
            $table->boolean('sms_notifications')->default(false)->after('email_notifications');
            $table->boolean('push_notifications')->default(true)->after('sms_notifications');
            
            // Дополнительные поля
            $table->string('timezone')->default('Europe/Moscow')->after('push_notifications');
            $table->string('language')->default('ru')->after('timezone');
            $table->text('bio')->nullable()->after('language');
            $table->json('settings')->nullable()->after('bio'); // дополнительные настройки
            
            // Статистика
            $table->datetime('last_login_at')->nullable()->after('settings');
            $table->string('last_login_ip')->nullable()->after('last_login_at');
            $table->boolean('is_active')->default(true)->after('last_login_ip');
            
            // Индексы
            $table->index(['phone']);
            $table->index(['is_active', 'email_verified_at']);
            $table->index('last_login_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['phone']);
            $table->dropIndex(['is_active', 'email_verified_at']);
            $table->dropIndex(['last_login_at']);
            
            $table->dropColumn([
                'first_name',
                'last_name',
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
                'last_login_at',
                'last_login_ip',
                'is_active'
            ]);
        });
    }
};
