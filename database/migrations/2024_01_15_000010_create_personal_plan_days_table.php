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
        Schema::create('personal_plan_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personal_plan_id')->constrained()->onDelete('cascade');
            $table->integer('day_number');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->decimal('total_calories', 8, 1)->nullable();
            $table->decimal('total_proteins', 8, 1)->nullable();
            $table->decimal('total_fats', 8, 1)->nullable();
            $table->decimal('total_carbs', 8, 1)->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->datetime('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['personal_plan_id', 'day_number']);
            $table->index(['personal_plan_id', 'is_completed']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_plan_days');
    }
};
