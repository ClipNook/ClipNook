<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_is_twitch_connected()
    {
        $user = User::factory()->create(['twitch_id' => null]);
        $this->assertFalse($user->isTwitchConnected());

        $user->twitch_id = '12345';
        $user->save();

        $this->assertTrue($user->isTwitchConnected());
    }

    public function test_delete_avatar_clears_storage_and_attribute()
    {
        Storage::fake('public');

        $filename = 'avatars/test-avatar.jpg';
        Storage::disk('public')->put($filename, 'contents');

        $user = User::factory()->create(['twitch_avatar' => $filename]);

        $this->assertTrue(Storage::disk('public')->exists($filename));

        $result = $user->deleteAvatar();

        $this->assertTrue($result);
        $this->assertFalse(Storage::disk('public')->exists($filename));
        $this->assertNull($user->fresh()->twitch_avatar);
    }
}
