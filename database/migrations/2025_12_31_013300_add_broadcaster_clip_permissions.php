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
        // Broadcaster settings table
        Schema::create('broadcaster_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('broadcaster_id')->constrained('users')->onDelete('cascade');
            $table->boolean('allow_public_clip_submissions')->default(false);
            $table->timestamps();

            $table->unique('broadcaster_id');
        });

        // Specific permissions for users to submit clips for specific broadcasters
        Schema::create('broadcaster_clip_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('broadcaster_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->boolean('can_submit_clips')->default(true);
            $table->boolean('can_edit_clips')->default(false);
            $table->boolean('can_delete_clips')->default(false);
            $table->boolean('can_moderate_clips')->default(false);
            $table->timestamps();

            $table->unique(['broadcaster_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('broadcaster_clip_permissions');
        Schema::dropIfExists('broadcaster_settings');
    }
};
