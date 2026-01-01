<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('twitch_game_id')->unique();
            $table->string('name');
            $table->string('box_art_url');
            $table->string('local_box_art_path')->nullable();
            $table->string('igdb_id')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            // Additional performance indexes
            $table->index('twitch_game_id', 'idx_games_twitch_id');
        });

        // Full-text search for game names (MySQL)
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE games ADD FULLTEXT idx_games_name_fulltext (name)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
