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
        Schema::create('clips', function (Blueprint $table) {
            $table->id();
            $table->string('twitch_clip_id')->unique(); // Twitch's clip ID
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('url'); // Twitch clip URL
            $table->string('thumbnail_url')->nullable();
            $table->integer('duration'); // Duration in seconds
            $table->integer('view_count')->default(0);
            $table->timestamp('created_at_twitch'); // When created on Twitch
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
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clips');
    }
};
