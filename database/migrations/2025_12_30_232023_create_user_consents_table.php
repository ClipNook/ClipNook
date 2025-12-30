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
        Schema::create('user_consents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('consent_type'); // 'terms', 'privacy_policy', 'data_processing'
            $table->string('consent_version');
            $table->boolean('consented')->default(false);
            $table->timestamp('consented_at')->nullable();
            $table->string('ip_address')->nullable(); // Masked IP
            $table->string('user_agent_hash')->nullable(); // Hashed UA
            $table->timestamps();

            $table->index(['user_id', 'consent_type']);
            $table->index(['consent_type', 'consent_version']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_consents');
    }
};
