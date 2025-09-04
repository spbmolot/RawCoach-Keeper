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
            $table->foreignId('coupon_id')->nullable()->after('plan_id')->constrained()->onDelete('set null');
            $table->decimal('original_amount', 10, 2)->nullable()->after('amount');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('original_amount');
            $table->json('webhook_payload')->nullable()->after('payload');
            $table->datetime('failed_at')->nullable()->after('webhook_payload');
            $table->datetime('refunded_at')->nullable()->after('failed_at');
            $table->decimal('refund_amount', 10, 2)->default(0)->after('refunded_at');
            $table->text('description')->nullable()->after('refund_amount');
            
            $table->index(['coupon_id', 'status']);
            $table->index(['provider', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['coupon_id']);
            $table->dropIndex(['coupon_id', 'status']);
            $table->dropIndex(['provider', 'status']);
            $table->dropColumn([
                'coupon_id',
                'original_amount',
                'discount_amount',
                'webhook_payload',
                'failed_at',
                'refunded_at',
                'refund_amount',
                'description'
            ]);
        });
    }
};
