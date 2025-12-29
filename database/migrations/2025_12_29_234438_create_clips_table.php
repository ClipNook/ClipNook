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
            $table->string('twitch_clip_id')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('thumbnail_path'); // local path DSGVO
            $table->unsignedBigInteger('broadcaster_id'); // Streamer
            $table->unsignedBigInteger('submitted_by_id')->nullable(); // Wer hat eingereicht
            $table->boolean('is_public')->default(true);
            $table->integer('duration')->nullable();
            $table->string('creator_name')->nullable();
            $table->string('game_id')->nullable();
            $table->string('video_id')->nullable();
            $table->timestamp('clip_created_at')->nullable();
            $table->timestamps();

            $table->foreign('broadcaster_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('submitted_by_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
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
