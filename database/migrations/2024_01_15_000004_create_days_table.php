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
        Schema::create('days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained()->onDelete('cascade');
            $table->integer('day_number');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->decimal('total_calories', 8, 1)->nullable();
            $table->decimal('total_proteins', 8, 1)->nullable();
            $table->decimal('total_fats', 8, 1)->nullable();
            $table->decimal('total_carbs', 8, 1)->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['menu_id', 'day_number']);
            $table->index(['menu_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('days');
    }
};
