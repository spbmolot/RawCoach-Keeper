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
        Schema::create('personal_plan_day_recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personal_plan_day_id')->constrained()->onDelete('cascade');
            $table->foreignId('recipe_id')->constrained()->onDelete('cascade');
            $table->enum('meal_type', ['breakfast', 'lunch', 'dinner', 'snack']);
            $table->integer('order')->default(1);
            $table->decimal('portion_size', 8, 2)->nullable();
            $table->timestamps();

            $table->unique(['personal_plan_day_id', 'recipe_id', 'meal_type'], 'personal_plan_day_recipe_unique');
            $table->index(['personal_plan_day_id', 'meal_type', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_plan_day_recipes');
    }
};
