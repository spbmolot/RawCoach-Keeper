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
        Schema::table('coupons', function (Blueprint $table) {
            // Добавляем поле maximum_discount
            $table->decimal('maximum_discount', 10, 2)->nullable()->after('minimum_amount');
            
            // Переименовываем поля в соответствии с миграцией
            if (Schema::hasColumn('coupons', 'usage_count')) {
                $table->renameColumn('usage_count', 'used_count');
            } else {
                $table->integer('used_count')->default(0)->after('usage_limit');
            }
            
            if (Schema::hasColumn('coupons', 'user_usage_limit')) {
                $table->renameColumn('user_usage_limit', 'usage_limit_per_user');
            } else {
                $table->integer('usage_limit_per_user')->nullable()->after('used_count');
            }
            
            if (Schema::hasColumn('coupons', 'starts_at')) {
                $table->renameColumn('starts_at', 'valid_from');
            } else {
                $table->timestamp('valid_from')->nullable()->after('usage_limit_per_user');
            }
            
            if (Schema::hasColumn('coupons', 'expires_at')) {
                $table->renameColumn('expires_at', 'valid_until');
            } else {
                $table->timestamp('valid_until')->nullable()->after('valid_from');
            }
            
            // Добавляем поля created_by
            if (!Schema::hasColumn('coupons', 'created_by_type')) {
                $table->string('created_by_type')->nullable()->after('applicable_plans');
            }
            if (!Schema::hasColumn('coupons', 'created_by_id')) {
                $table->unsignedBigInteger('created_by_id')->nullable()->after('created_by_type');
            }
            
            // Удаляем поля которых нет в миграции
            if (Schema::hasColumn('coupons', 'name')) {
                $table->dropColumn('name');
            }
            if (Schema::hasColumn('coupons', 'currency')) {
                $table->dropColumn('currency');
            }
            if (Schema::hasColumn('coupons', 'first_time_only')) {
                $table->dropColumn('first_time_only');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            // Возвращаем удаленные поля
            $table->string('name')->nullable();
            $table->string('currency', 3)->default('RUB');
            $table->boolean('first_time_only')->default(false);
            
            // Удаляем добавленные поля
            $table->dropColumn(['maximum_discount', 'created_by_type', 'created_by_id']);
            
            // Переименовываем поля обратно
            if (Schema::hasColumn('coupons', 'used_count')) {
                $table->renameColumn('used_count', 'usage_count');
            }
            if (Schema::hasColumn('coupons', 'usage_limit_per_user')) {
                $table->renameColumn('usage_limit_per_user', 'user_usage_limit');
            }
            if (Schema::hasColumn('coupons', 'valid_from')) {
                $table->renameColumn('valid_from', 'starts_at');
            }
            if (Schema::hasColumn('coupons', 'valid_until')) {
                $table->renameColumn('valid_until', 'expires_at');
            }
        });
    }
};
