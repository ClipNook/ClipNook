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
        Schema::create('ip_pseudonymization_salts', function (Blueprint $table) {
            $table->uuid('id')->primary(); // UUID for better security (non-predictable)
            $table->string('salt', 64)->unique(); // Cryptographically secure salt
            $table->timestamp('valid_from');
            $table->timestamp('valid_until')->nullable(); // Null means currently active
            $table->boolean('is_active')->default(false);
            $table->timestamps();

            $table->index(['is_active', 'valid_until']);
            $table->index('valid_from');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ip_pseudonymization_salts');
    }
};
