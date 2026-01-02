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
        Schema::create('users', static function (Blueprint $table): void {
            $table->id();

            // Twitch OAuth Authentication Fields
            $table->string('twitch_id')->unique()->nullable();
            $table->string('twitch_login')->nullable();
            $table->string('twitch_display_name')->nullable();
            $table->string('twitch_email')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->text('twitch_access_token')->nullable();
            $table->text('twitch_refresh_token')->nullable();
            $table->timestamp('twitch_token_expires_at')->nullable();
            $table->timestamp('last_twitch_sync_at')->nullable();
            $table->json('scopes')->nullable();

            // Description
            $table->text('description')->nullable();

            // User Roles and Permissions
            $table->boolean('is_viewer')->default(true);
            $table->boolean('is_cutter')->default(false);
            $table->boolean('is_streamer')->default(false);
            $table->boolean('is_moderator')->default(false);
            $table->boolean('is_admin')->default(false);

            // User Preferences
            $table->json('preferences')->nullable();
            $table->boolean('notifications_email')->default(true);
            $table->boolean('notifications_web')->default(true);
            $table->boolean('notifications_ntfy')->default(false);
            $table->string('ntfy_server_url')->nullable();
            $table->string('ntfy_topic')->nullable();
            $table->string('ntfy_auth_token')->nullable();

            // Activity Tracking
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamp('last_login_at')->nullable();

            // GDPR compliance fields
            $table->timestamp('deletion_requested_at')->nullable();
            $table->timestamp('data_exported_at')->nullable();
            $table->timestamp('anonymized_at')->nullable();
            $table->json('gdpr_consent_log')->nullable();

            // Laravel Standard Fields
            $table->rememberToken();
            $table->timestamps();

            // Indexes for Performance
            $table->index(['is_streamer', 'is_cutter', 'is_admin'], 'idx_users_roles');
            $table->index('twitch_id', 'idx_users_twitch_id');
            $table->index('created_at', 'idx_users_created_at');
            $table->index('updated_at', 'idx_users_updated_at');

            // Additional performance indexes
            $table->index('twitch_email', 'idx_users_email');
            $table->index('last_activity_at', 'idx_users_last_activity');

            // Indexes for GDPR operations
            $table->index('deletion_requested_at', 'idx_users_deletion_requested');
            $table->index('anonymized_at', 'idx_users_anonymized');
        });

        Schema::create('sessions', static function (Blueprint $table): void {
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
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('users');
    }
};
