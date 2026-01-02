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
        Schema::create('clips', static function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('twitch_clip_id')->unique(); // Twitch's clip ID
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('url'); // Twitch clip URL
            $table->string('thumbnail_url')->nullable();
            $table->string('local_thumbnail_path')->nullable();
            $table->integer('duration'); // Duration in seconds
            $table->integer('view_count')->default(0);
            $table->timestamp('created_at_twitch'); // When created on Twitch
            $table->string('clip_creator_name')->nullable();
            $table->foreignId('game_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('status', ['pending', 'approved', 'rejected', 'flagged'])->default('pending');
            $table->foreignId('submitter_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('submitted_at')->useCurrent();
            $table->foreignId('broadcaster_id')->constrained('users')->onDelete('cascade');
            $table->text('moderation_reason')->nullable(); // Reason for rejection/flagging
            $table->foreignId('moderated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('moderated_at')->nullable();
            $table->json('tags')->nullable(); // JSON array of tags
            $table->boolean('is_featured')->default(false);
            $table->integer('upvotes')->default(0);
            $table->integer('downvotes')->default(0);
            $table->timestamps();

            // Indexes for performance
            $table->index(['status', 'created_at']);
            $table->index('twitch_clip_id');
            $table->index('is_featured');
            $table->index(['upvotes', 'downvotes']);

            // Critical performance indexes for fresh installations
            $table->index(['broadcaster_id', 'status', 'submitted_at'], 'idx_clips_broadcaster_moderation');
            $table->index(['submitter_id', 'status', 'is_featured'], 'idx_clips_submitter_dashboard');
            $table->index(['is_featured', 'view_count', 'created_at'], 'idx_clips_featured_popular');

            // Additional performance indexes
            $table->index('broadcaster_id', 'idx_clips_broadcaster_id');
            $table->index('game_id', 'idx_clips_game_id');
            $table->index(['broadcaster_id', 'status', 'created_at'], 'idx_clips_broadcaster_status_created');
            $table->index(['status', 'is_featured', 'created_at'], 'idx_clips_status_featured_created');
            $table->index(['submitter_id', 'status', 'created_at'], 'idx_clips_submitter_status_created');
        });

        // Full-text search index for clips (MySQL)
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE clips ADD FULLTEXT idx_clips_fulltext (title, description)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clips');
    }
};
