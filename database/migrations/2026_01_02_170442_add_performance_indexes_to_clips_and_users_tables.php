<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('clips', static function (Blueprint $table): void {
            // Covering index for dashboard queries
            $table->index(
                ['broadcaster_id', 'status', 'submitted_at', 'is_featured'],
                'idx_clips_dashboard_optimized'
            );

            // Partial index for active clips (only approved)
            if (DB::getDriverName() === 'mysql') {
                DB::statement(
                    'CREATE INDEX idx_clips_active ON clips (view_count DESC, created_at DESC)
                     WHERE status = "approved"'
                );
            }
        });

        Schema::table('users', static function (Blueprint $table): void {
            // Index for token refresh queries
            $table->index(
                ['id', 'twitch_token_expires_at'],
                'idx_users_token_refresh'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clips', static function (Blueprint $table): void {
            $table->dropIndex('idx_clips_dashboard_optimized');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement('DROP INDEX idx_clips_active ON clips');
        }

        Schema::table('users', static function (Blueprint $table): void {
            $table->dropIndex('idx_users_token_refresh');
        });
    }
};
