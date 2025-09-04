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
        Schema::table('plans', function (Blueprint $table) {
            // Добавляем поле limits для хранения ограничений плана в JSON
            if (!Schema::hasColumn('plans', 'limits')) {
                $table->json('limits')->nullable()->after('features');
            }
            
            // Переименовываем period_days в duration_days если существует
            if (Schema::hasColumn('plans', 'period_days')) {
                $table->renameColumn('period_days', 'duration_days');
            } else if (!Schema::hasColumn('plans', 'duration_days')) {
                // Если period_days не существует и duration_days тоже не существует, добавляем duration_days
                $table->integer('duration_days')->nullable()->after('type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            // Удаляем добавленные поля
            if (Schema::hasColumn('plans', 'limits')) {
                $table->dropColumn('limits');
            }
            
            // Переименовываем обратно duration_days в period_days если нужно
            if (Schema::hasColumn('plans', 'duration_days') && !Schema::hasColumn('plans', 'period_days')) {
                $table->renameColumn('duration_days', 'period_days');
            }
        });
    }
};
