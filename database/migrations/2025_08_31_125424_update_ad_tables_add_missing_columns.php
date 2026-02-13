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
        // Обновляем ad_placements — добавляем is_active
        Schema::table('ad_placements', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('size');
            $table->text('description')->nullable()->after('name');
        });

        // Обновляем ad_campaigns — приводим в соответствие с контроллером
        Schema::table('ad_campaigns', function (Blueprint $table) {
            // Переименовываем owner_id → advertiser_id
            $table->renameColumn('owner_id', 'advertiser_id');
            // Переименовываем start_at → starts_at, end_at → ends_at
            $table->renameColumn('start_at', 'starts_at');
            $table->renameColumn('end_at', 'ends_at');
        });

        Schema::table('ad_campaigns', function (Blueprint $table) {
            // Добавляем недостающие поля
            $table->string('name')->after('advertiser_id');
            $table->text('description')->nullable()->after('name');
            $table->decimal('daily_budget', 10, 2)->nullable()->after('budget');
            $table->decimal('spent_budget', 10, 2)->default(0)->after('daily_budget');
            $table->unsignedBigInteger('impressions')->default(0)->after('status');
            $table->unsignedBigInteger('clicks')->default(0)->after('impressions');
            $table->json('target_audience')->nullable()->after('clicks');
            $table->timestamp('paused_at')->nullable()->after('ends_at');
        });

        // Обновляем ad_creatives — добавляем недостающие поля для контроллера
        Schema::table('ad_creatives', function (Blueprint $table) {
            $table->string('name')->after('campaign_id');
            $table->text('content')->nullable()->after('type');
            $table->string('url')->nullable()->after('path');
            $table->string('alt_text')->nullable()->after('url');
            $table->string('status')->default('pending')->after('alt_text');
            // Переименовываем path → file_path
            $table->renameColumn('path', 'file_path');
        });

        // Создаём pivot-таблицу для связи кампаний с размещениями (many-to-many)
        Schema::create('ad_campaign_placement', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ad_campaign_id')->constrained('ad_campaigns')->cascadeOnDelete();
            $table->foreignId('ad_placement_id')->constrained('ad_placements')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['ad_campaign_id', 'ad_placement_id'], 'campaign_placement_unique');
        });

        // Создаём таблицу дневной статистики
        Schema::create('ad_campaign_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('ad_campaigns')->cascadeOnDelete();
            $table->date('date');
            $table->unsignedBigInteger('impressions')->default(0);
            $table->unsignedBigInteger('clicks')->default(0);
            $table->decimal('spent', 10, 2)->default(0);
            $table->decimal('ctr', 5, 2)->default(0);
            $table->timestamps();

            $table->unique(['campaign_id', 'date']);
            $table->index(['campaign_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ad_campaign_stats');
        Schema::dropIfExists('ad_campaign_placement');

        Schema::table('ad_creatives', function (Blueprint $table) {
            $table->renameColumn('file_path', 'path');
            $table->dropColumn(['name', 'content', 'url', 'alt_text', 'status']);
        });

        Schema::table('ad_campaigns', function (Blueprint $table) {
            $table->dropColumn(['name', 'description', 'daily_budget', 'spent_budget', 'impressions', 'clicks', 'target_audience', 'paused_at']);
        });

        Schema::table('ad_campaigns', function (Blueprint $table) {
            $table->renameColumn('advertiser_id', 'owner_id');
            $table->renameColumn('starts_at', 'start_at');
            $table->renameColumn('ends_at', 'end_at');
        });

        Schema::table('ad_placements', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'description']);
        });
    }
};
