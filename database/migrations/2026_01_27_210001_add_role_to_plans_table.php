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
            $table->string('role')->nullable()->after('type');
        });

        // Обновляем существующие планы
        \DB::table('plans')->where('slug', 'trial')->update(['role' => 'subscriber_trial']);
        \DB::table('plans')->where('slug', 'standard-monthly')->update(['role' => 'subscriber_standard']);
        \DB::table('plans')->where('slug', 'standard-yearly')->update(['role' => 'subscriber_premium']);
        \DB::table('plans')->where('slug', 'personal-monthly')->update(['role' => 'subscriber_personal']);
        \DB::table('plans')->where('slug', 'personal-yearly')->update(['role' => 'subscriber_personal']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
