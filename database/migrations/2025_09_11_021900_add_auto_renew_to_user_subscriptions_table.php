<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_subscriptions', function (Blueprint $table) {
            if (!Schema::hasColumn('user_subscriptions', 'auto_renew')) {
                $table->boolean('auto_renew')->default(true)->after('ends_at');
            }
        });

        // Переносим значения из auto_renewal -> auto_renew, если старое поле существует
        if (Schema::hasColumn('user_subscriptions', 'auto_renewal') && Schema::hasColumn('user_subscriptions', 'auto_renew')) {
            DB::table('user_subscriptions')->update([
                'auto_renew' => DB::raw('auto_renewal')
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_subscriptions', function (Blueprint $table) {
            if (Schema::hasColumn('user_subscriptions', 'auto_renew')) {
                $table->dropColumn('auto_renew');
            }
        });
    }
};
