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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            // Twitch OAuth fields
            $table->string('twitch_id')->unique()->nullable();
            $table->string('twitch_login')->nullable();
            $table->string('twitch_display_name')->nullable();
            $table->string('twitch_email')->nullable();
            $table->string('twitch_avatar')->nullable();
            $table->text('twitch_access_token')->nullable();
            $table->text('twitch_refresh_token')->nullable();
            $table->timestamp('twitch_token_expires_at')->nullable();

            // Settings
            $table->boolean('avatar_disabled')->default(false);

            // Role flags
            $table->boolean('is_viewer')->default(true);
            $table->boolean('is_cutter')->default(false);
            $table->boolean('is_streamer')->default(false);
            $table->boolean('is_moderator')->default(false);
            $table->boolean('is_admin')->default(false);

            // Profile fields
            $table->text('intro')->nullable();
            $table->boolean('available_for_jobs')->default(false);

            // Standard Laravel user fields
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('sessions');
    }
};
