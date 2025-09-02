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
        Schema::create('recipe_ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')->constrained()->onDelete('cascade');
            $table->string('ingredient_name');
            $table->decimal('amount', 8, 2);
            $table->string('unit'); // г, кг, мл, л, шт, ст.л., ч.л.
            $table->string('category')->nullable(); // овощи, мясо, крупы и т.д.
            $table->text('preparation_notes')->nullable(); // нарезать кубиками и т.д.
            $table->boolean('is_optional')->default(false);
            $table->integer('order')->default(1);
            $table->timestamps();

            $table->index(['recipe_id', 'order']);
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipe_ingredients');
    }
};
