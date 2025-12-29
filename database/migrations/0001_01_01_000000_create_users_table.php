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

            // Twitch OAuth fields (primary auth)
            $table->string('twitch_id')->unique()->nullable();
            $table->string('twitch_login')->nullable();
            $table->string('twitch_display_name')->nullable();
            $table->string('twitch_email')->nullable();
            $table->text('twitch_access_token')->nullable();
            $table->text('twitch_refresh_token')->nullable();
            $table->timestamp('twitch_token_expires_at')->nullable();

            // Avatar fields
            $table->string('twitch_avatar')->nullable();
            $table->string('custom_avatar_path')->nullable();
            $table->string('custom_avatar_thumbnail_path')->nullable();
            $table->string('avatar_source')->nullable();
            $table->boolean('avatar_disabled')->default(false);
            $table->timestamp('avatar_disabled_at')->nullable();

            // Role flags
            $table->boolean('is_viewer')->default(true);
            $table->boolean('is_cutter')->default(false);
            $table->boolean('is_streamer')->default(false);
            $table->boolean('is_moderator')->default(false);
            $table->boolean('is_admin')->default(false);

            // Profile fields
            $table->text('intro')->nullable();
            $table->boolean('available_for_jobs')->default(false);
            $table->boolean('allow_clip_sharing')->default(false);

            // Preferences
            $table->json('preferences')->default('{"accent_color": "purple"}');
            $table->string('accent_color')->nullable();
            $table->string('theme_preference')->default('system');
            $table->string('locale')->default('de');

            // Standard Laravel fields
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('streamer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('intro')->nullable();
            $table->string('stream_schedule')->nullable();
            $table->string('preferred_games')->nullable();
            $table->string('stream_quality')->default('720p');
            $table->boolean('has_overlay')->default(false);
            $table->timestamps();
        });

        Schema::create('cutter_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('hourly_rate', 8, 2)->nullable();
            $table->string('response_time')->default('24');
            $table->json('skills')->nullable();
            $table->boolean('is_available')->default(false);
            $table->string('portfolio_url')->nullable();
            $table->integer('experience_years')->nullable();
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
