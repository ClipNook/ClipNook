<?php

declare(strict_types=1);

namespace App\Actions\Twitch;

use App\Models\User;
use App\Services\Twitch\DTOs\TokenDTO;
use App\Services\Twitch\TwitchService;
use Illuminate\Support\Facades\Auth;

class AuthenticateTwitchUserAction
{
    public function execute(TokenDTO $token, TwitchService $twitchService): ?User
    {
        $twitchUser = $twitchService->getCurrentUser();

        if (! $twitchUser) {
            return null;
        }

        // Only save email if user:read:email scope was granted
        $email         = null;
        $grantedScopes = explode(' ', $token->scope ?? '');
        if (in_array('user:read:email', $grantedScopes)) {
            $email = $twitchUser->email;
        }

        $user = User::updateOrCreate(
            ['twitch_id' => $twitchUser->id],
            [
                'twitch_login'            => $twitchUser->login,
                'twitch_display_name'     => $twitchUser->displayName,
                'twitch_email'            => $email,
                'twitch_access_token'     => $token->accessToken,
                'twitch_refresh_token'    => $token->refreshToken,
                'twitch_token_expires_at' => now()->addSeconds($token->expiresIn),
                'scopes'                  => $grantedScopes,
                'last_login_at'           => now(),
            ]
        );

        // Always update last_login_at on login
        $user->update(['last_login_at' => now()]);

        Auth::login($user);

        // Clear session preferences
        session()->forget('login_preferences');

        return $user;
    }
}
