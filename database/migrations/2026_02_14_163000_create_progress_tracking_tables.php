<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Дневник питания
        Schema::create('food_diary_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->string('meal_type'); // breakfast, lunch, dinner, snack
            $table->foreignId('recipe_id')->nullable()->constrained()->nullOnDelete();
            $table->string('custom_name')->nullable();
            $table->decimal('calories', 8, 1)->default(0);
            $table->decimal('proteins', 8, 1)->default(0);
            $table->decimal('fats', 8, 1)->default(0);
            $table->decimal('carbs', 8, 1)->default(0);
            $table->decimal('portion_size', 5, 2)->default(1);
            $table->text('notes')->nullable();
            $table->boolean('from_menu')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'date']);
        });

        // Лог веса
        Schema::create('weight_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->decimal('weight', 5, 1);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'date']);
        });

        // Достижения (справочник)
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('icon')->default('trophy');
            $table->string('category'); // nutrition, weight, streak, social, exploration
            $table->string('condition_type'); // diary_entries, weight_logs, streak_days, recipes_tried, etc.
            $table->integer('condition_value')->default(1);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Достижения пользователей
        Schema::create('user_achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('achievement_id')->constrained()->cascadeOnDelete();
            $table->timestamp('unlocked_at');
            $table->timestamps();

            $table->unique(['user_id', 'achievement_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_achievements');
        Schema::dropIfExists('achievements');
        Schema::dropIfExists('weight_logs');
        Schema::dropIfExists('food_diary_entries');
    }
};
