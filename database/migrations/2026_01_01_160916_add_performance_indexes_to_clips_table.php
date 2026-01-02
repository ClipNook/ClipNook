<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('clips', static function (Blueprint $table): void {
            // Composite index for homepage query (latest clips by status)
            $table->index(['status', 'is_featured', 'view_count'], 'idx_clips_homepage_featured');

            // Composite index for moderation queries
            $table->index(['broadcaster_id', 'status', 'submitted_at'], 'idx_clips_moderation');

            // Index for upvote/trending queries
            $table->index(['status', 'upvotes', 'created_at'], 'idx_clips_trending');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clips', static function (Blueprint $table): void {
            $table->dropIndex('idx_clips_homepage_featured');
            $table->dropIndex('idx_clips_moderation');
            $table->dropIndex('idx_clips_trending');
        });
    }
};
