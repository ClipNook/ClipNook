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
        // Additional indexes for clips table
        Schema::table('clips', function (Blueprint $table) {
            // Index for twitch_clip_id lookups (used in duplicate checking)
            $table->index('twitch_clip_id', 'idx_clips_twitch_id');

            // Index for broadcaster_id queries
            $table->index('broadcaster_id', 'idx_clips_broadcaster_id');

            // Index for game_id queries
            $table->index('game_id', 'idx_clips_game_id');

            // Composite index for clips by broadcaster and status
            $table->index(['broadcaster_id', 'status', 'created_at'], 'idx_clips_broadcaster_status_created');
        });

        // Indexes for games table
        Schema::table('games', function (Blueprint $table) {
            // Index for twitch_game_id lookups
            $table->index('twitch_game_id', 'idx_games_twitch_id');

            // Full-text search for game names
            if (DB::connection()->getDriverName() === 'mysql') {
                DB::statement('ALTER TABLE games ADD FULLTEXT idx_games_name_fulltext (name)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clips', function (Blueprint $table) {
            $table->dropIndex('idx_clips_twitch_id');
            $table->dropIndex('idx_clips_broadcaster_id');
            $table->dropIndex('idx_clips_game_id');
            $table->dropIndex('idx_clips_broadcaster_status_created');
        });

        Schema::table('games', function (Blueprint $table) {
            $table->dropIndex('idx_games_twitch_id');

            if (DB::connection()->getDriverName() === 'mysql') {
                DB::statement('ALTER TABLE games DROP INDEX idx_games_name_fulltext');
            }
        });
    }
};
