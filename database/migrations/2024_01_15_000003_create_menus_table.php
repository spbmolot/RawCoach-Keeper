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
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->integer('month');
            $table->integer('year');
            $table->decimal('total_calories', 8, 1)->nullable();
            $table->decimal('total_proteins', 8, 1)->nullable();
            $table->decimal('total_fats', 8, 1)->nullable();
            $table->decimal('total_carbs', 8, 1)->nullable();
            $table->string('cover_image')->nullable();
            $table->string('pdf_file')->nullable();
            $table->string('excel_file')->nullable();
            $table->boolean('is_published')->default(false);
            $table->datetime('published_at')->nullable();
            $table->json('features')->nullable(); // массив особенностей меню
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['month', 'year']);
            $table->index(['is_published', 'published_at']);
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
