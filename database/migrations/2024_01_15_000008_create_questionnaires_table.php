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
        Schema::create('questionnaires', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('age');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->decimal('height', 5, 2); // см
            $table->decimal('current_weight', 5, 2); // кг
            $table->decimal('target_weight', 5, 2)->nullable(); // кг
            $table->enum('activity_level', ['sedentary', 'lightly_active', 'moderately_active', 'very_active', 'extremely_active']);
            $table->enum('goal', ['lose_weight', 'maintain_weight', 'gain_weight', 'gain_muscle', 'improve_health']);
            $table->json('dietary_restrictions')->nullable(); // вегетарианство, веганство и т.д.
            $table->json('allergies')->nullable(); // аллергии
            $table->json('disliked_foods')->nullable(); // нелюбимые продукты
            $table->json('preferred_cuisines')->nullable(); // предпочитаемые кухни
            $table->enum('cooking_skill', ['beginner', 'intermediate', 'advanced']);
            $table->integer('cooking_time_limit')->nullable(); // максимальное время готовки в минутах
            $table->integer('meals_per_day')->default(3);
            $table->boolean('include_snacks')->default(true);
            $table->json('health_conditions')->nullable(); // заболевания
            $table->json('medications')->nullable(); // принимаемые лекарства
            $table->integer('water_intake_goal')->nullable(); // цель по воде в мл
            $table->enum('budget_range', ['low', 'medium', 'high'])->nullable();
            $table->text('additional_notes')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->datetime('completed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'is_completed']);
            $table->index('completed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questionnaires');
    }
};
