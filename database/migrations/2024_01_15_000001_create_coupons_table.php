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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->enum('type', ['percentage', 'fixed']);
            $table->decimal('value', 10, 2);
            $table->text('description')->nullable();
            $table->integer('usage_limit')->nullable();
            $table->integer('used_count')->default(0);
            $table->integer('usage_limit_per_user')->nullable();
            $table->json('applicable_plans')->nullable(); // массив ID планов
            $table->decimal('minimum_amount', 10, 2)->nullable();
            $table->decimal('maximum_discount', 10, 2)->nullable();
            $table->datetime('valid_from')->nullable();
            $table->datetime('valid_until')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('created_by_type')->nullable(); // User, Admin
            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->timestamps();

            $table->index(['code', 'is_active']);
            $table->index(['valid_from', 'valid_until']);
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
