<?php

declare(strict_types=1);

namespace App\Actions\Twitch;

use App\Models\User;
use App\Services\Twitch\Api\StreamerApiService;
use App\Services\Twitch\Auth\TwitchTokenManager;
use App\Services\Twitch\DTOs\TokenDTO;
use Illuminate\Support\Facades\Auth;

use function explode;
use function in_array;
use function now;
use function session;

final class AuthenticateTwitchUserAction
{
    public function __construct(
        private readonly StreamerApiService $streamerApiService,
        private readonly TwitchTokenManager $tokenManager,
    ) {}

    public function execute(TokenDTO $token): ?User
    {
        $twitchUser = $this->streamerApiService->getCurrentUser($token->accessToken);

        if (! $twitchUser) {
            return null;
        }

        // Only save email if user:read:email scope was granted
        $email         = null;
        $grantedScopes = explode(' ', $token->scope ?? '');
        if (in_array('user:read:email', $grantedScopes, true)) {
            $email = $twitchUser->email;
        }

        $user = User::updateOrCreate(
            ['twitch_id' => $twitchUser->id],
            [
                'twitch_login'            => $twitchUser->login,
                'twitch_display_name'     => $twitchUser->displayName,
                'twitch_email'            => $email,
                'scopes'                  => $grantedScopes,
                'last_login_at'           => now(),
            ]
        );

        // Store tokens using the token manager
        $this->tokenManager->updateUserTokens($user, $token);

        // Always update last_login_at on login
        $user->update(['last_login_at' => now()]);

        Auth::login($user);

        // Clear session preferences
        session()->forget('login_preferences');

        return $user;
    }
}
