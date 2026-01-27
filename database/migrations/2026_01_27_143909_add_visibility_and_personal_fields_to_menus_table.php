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
        Schema::table('menus', function (Blueprint $table) {
            // Дата, с которой меню становится видимым пользователям
            $table->date('visible_from')->nullable()->after('published_at');
            
            // Для персональных меню - привязка к конкретному пользователю
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete();
            
            // Флаг персонального меню
            $table->boolean('is_personal')->default(false)->after('is_published');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['visible_from', 'user_id', 'is_personal']);
        });
    }
};
