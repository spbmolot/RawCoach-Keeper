<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_meal_swaps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('day_meal_id')->constrained('day_meals')->onDelete('cascade');
            $table->foreignId('original_recipe_id')->constrained('recipes')->onDelete('cascade');
            $table->foreignId('replacement_recipe_id')->constrained('recipes')->onDelete('cascade');
            $table->text('reason')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'day_meal_id']);
            $table->index(['user_id', 'day_meal_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_meal_swaps');
    }
};
