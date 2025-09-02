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
        Schema::create('personal_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('questionnaire_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('target_calories', 8, 1);
            $table->decimal('target_proteins', 8, 1);
            $table->decimal('target_fats', 8, 1);
            $table->decimal('target_carbs', 8, 1);
            $table->integer('duration_days');
            $table->enum('status', ['draft', 'active', 'completed', 'paused'])->default('draft');
            $table->enum('generated_by', ['ai', 'nutritionist'])->default('ai');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->datetime('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_public')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['status', 'start_date']);
            $table->index(['is_public', 'approved_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_plans');
    }
};
