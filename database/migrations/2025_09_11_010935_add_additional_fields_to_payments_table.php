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
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'payment_url')) {
                $table->string('payment_url', 2048)->nullable()->after('external_id');
            }
            if (!Schema::hasColumn('payments', 'metadata')) {
                $table->json('metadata')->nullable()->after('payload');
            }
            if (!Schema::hasColumn('payments', 'processed_at')) {
                $table->dateTime('processed_at')->nullable()->after('paid_at');
            }
            if (!Schema::hasColumn('payments', 'failure_reason')) {
                $table->string('failure_reason', 500)->nullable()->after('refund_amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'payment_url')) {
                $table->dropColumn('payment_url');
            }
            if (Schema::hasColumn('payments', 'metadata')) {
                $table->dropColumn('metadata');
            }
            if (Schema::hasColumn('payments', 'processed_at')) {
                $table->dropColumn('processed_at');
            }
            if (Schema::hasColumn('payments', 'failure_reason')) {
                $table->dropColumn('failure_reason');
            }
        });
    }
};
