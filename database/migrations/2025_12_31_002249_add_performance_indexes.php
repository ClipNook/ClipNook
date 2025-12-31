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
        Schema::table('clips', function (Blueprint $table) {
            // Composite indexes for common queries
            $table->index(['status', 'is_featured', 'created_at'], 'idx_clips_status_featured_created');
            $table->index(['submitter_id', 'status', 'created_at'], 'idx_clips_submitter_status_created');

            // Full-text search index (MySQL)
            if (DB::connection()->getDriverName() === 'mysql') {
                DB::statement('ALTER TABLE clips ADD FULLTEXT idx_clips_fulltext (title, description)');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('twitch_email', 'idx_users_email');
            $table->index('last_activity_at', 'idx_users_last_activity');
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->index(['user_id', 'performed_at'], 'idx_activity_user_performed');
            $table->index(['action', 'performed_at'], 'idx_activity_action_performed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clips', function (Blueprint $table) {
            $table->dropIndex('idx_clips_status_featured_created');
            $table->dropIndex('idx_clips_submitter_status_created');

            if (DB::connection()->getDriverName() === 'mysql') {
                DB::statement('ALTER TABLE clips DROP INDEX idx_clips_fulltext');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_email');
            $table->dropIndex('idx_users_last_activity');
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropIndex('idx_activity_user_performed');
            $table->dropIndex('idx_activity_action_performed');
        });
    }
};
