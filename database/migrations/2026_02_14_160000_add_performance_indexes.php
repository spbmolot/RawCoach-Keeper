<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // menus: часто ищем по month+year+is_published+type
        Schema::table('menus', function (Blueprint $table) {
            $table->index(['month', 'year', 'is_published', 'type'], 'menus_month_year_published_type_idx');
        });

        // days: часто ищем по menu_id+day_number
        Schema::table('days', function (Blueprint $table) {
            $table->index(['menu_id', 'day_number'], 'days_menu_day_number_idx');
        });

        // day_meals: часто ищем по day_id+meal_type
        Schema::table('day_meals', function (Blueprint $table) {
            $table->index(['day_id', 'meal_type'], 'day_meals_day_meal_type_idx');
        });

        // user_subscriptions: часто ищем active по user_id
        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->index(['user_id', 'status'], 'user_subs_user_status_idx');
        });

        // payments: часто фильтруем по status и created_at
        Schema::table('payments', function (Blueprint $table) {
            $table->index(['status', 'created_at'], 'payments_status_created_idx');
            $table->index(['user_id', 'status'], 'payments_user_status_idx');
        });

        // recipes: часто фильтруем по is_published+category
        Schema::table('recipes', function (Blueprint $table) {
            $table->index(['is_published', 'category'], 'recipes_published_category_idx');
        });
    }

    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->dropIndex('menus_month_year_published_type_idx');
        });
        Schema::table('days', function (Blueprint $table) {
            $table->dropIndex('days_menu_day_number_idx');
        });
        Schema::table('day_meals', function (Blueprint $table) {
            $table->dropIndex('day_meals_day_meal_type_idx');
        });
        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->dropIndex('user_subs_user_status_idx');
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('payments_status_created_idx');
            $table->dropIndex('payments_user_status_idx');
        });
        Schema::table('recipes', function (Blueprint $table) {
            $table->dropIndex('recipes_published_category_idx');
        });
    }
};
