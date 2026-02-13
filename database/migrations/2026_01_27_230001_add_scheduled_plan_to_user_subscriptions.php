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
        Schema::table('user_subscriptions', function (Blueprint $table) {
            // Запланированный план для даунгрейда/смены при следующем продлении
            $table->foreignId('scheduled_plan_id')->nullable()->after('plan_id')->constrained('plans')->nullOnDelete();
            $table->timestamp('scheduled_at')->nullable()->after('scheduled_plan_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->dropForeign(['scheduled_plan_id']);
            $table->dropColumn(['scheduled_plan_id', 'scheduled_at']);
        });
    }
};
