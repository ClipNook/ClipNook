<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UserEncryptionTest extends TestCase
{
    public function test_tokens_are_encrypted_in_database()
    {
        // Ensure a minimal users table exists in the test DB
        if (! Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->text('twitch_access_token')->nullable();
                $table->text('twitch_refresh_token')->nullable();
                $table->timestamps();
            });
        }

        // Create a user using mass assignment of the relevant fields only
        $user = User::create([
            'twitch_access_token'  => 'plain_access_token_123',
            'twitch_refresh_token' => 'plain_refresh_token_456',
        ]);

        $dbAccess  = DB::table('users')->where('id', $user->id)->value('twitch_access_token');
        $dbRefresh = DB::table('users')->where('id', $user->id)->value('twitch_refresh_token');

        $this->assertNotEmpty($dbAccess);
        $this->assertNotEquals('plain_access_token_123', $dbAccess, 'Access token should be stored encrypted in the DB');
        $this->assertNotEquals('plain_refresh_token_456', $dbRefresh, 'Refresh token should be stored encrypted in the DB');

        // The model should decrypt on access using the encrypted cast
        $fresh = $user->fresh();
        $this->assertEquals('plain_access_token_123', $fresh->twitch_access_token);
        $this->assertEquals('plain_refresh_token_456', $fresh->twitch_refresh_token);
    }
}
