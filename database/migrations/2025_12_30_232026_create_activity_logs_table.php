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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('action'); // e.g., 'login', 'clip_submitted', 'data_exported'
            $table->string('ip_address')->nullable();
            $table->json('metadata')->nullable(); // Additional context data
            $table->timestamp('performed_at');
            $table->timestamps();

            $table->index(['user_id', 'action']);
            $table->index('performed_at');

            // Additional performance indexes
            $table->index(['user_id', 'performed_at'], 'idx_activity_user_performed');
            $table->index(['action', 'performed_at'], 'idx_activity_action_performed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
