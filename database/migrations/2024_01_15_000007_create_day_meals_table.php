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
        Schema::create('day_meals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('day_id')->constrained()->onDelete('cascade');
            $table->foreignId('recipe_id')->constrained()->onDelete('cascade');
            $table->enum('meal_type', ['breakfast', 'lunch', 'dinner', 'snack']);
            $table->integer('order')->default(1);
            $table->decimal('portion_size', 8, 2)->nullable(); // размер порции
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['day_id', 'recipe_id', 'meal_type']);
            $table->index(['day_id', 'meal_type', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('day_meals');
    }
};
