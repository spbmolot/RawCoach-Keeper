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
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->longText('instructions');
            $table->integer('prep_time')->nullable(); // минуты
            $table->integer('cook_time')->nullable(); // минуты
            $table->integer('servings')->default(1);
            $table->decimal('calories', 8, 1)->default(0);
            $table->decimal('proteins', 8, 1)->default(0);
            $table->decimal('fats', 8, 1)->default(0);
            $table->decimal('carbs', 8, 1)->default(0);
            $table->decimal('fiber', 8, 1)->default(0);
            $table->string('difficulty')->default('easy'); // easy, medium, hard
            $table->json('dietary_tags')->nullable(); // вегетарианское, веганское и т.д.
            $table->json('allergens')->nullable(); // аллергены
            $table->string('category')->nullable(); // завтрак, обед, ужин, перекус
            $table->string('cuisine')->nullable(); // кухня мира
            $table->string('main_image')->nullable();
            $table->json('gallery_images')->nullable();
            $table->boolean('is_published')->default(false);
            $table->datetime('published_at')->nullable();
            $table->integer('views_count')->default(0);
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('ratings_count')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['is_published', 'published_at']);
            $table->index('category');
            $table->index('difficulty');
            $table->index('slug');
            $table->index('rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
