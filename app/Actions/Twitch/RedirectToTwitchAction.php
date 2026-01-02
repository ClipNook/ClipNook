<?php

declare(strict_types=1);

namespace App\Actions\Twitch;

use Illuminate\Http\RedirectResponse;

use function config;
use function http_build_query;
use function implode;
use function redirect;
use function session;

final class RedirectToTwitchAction
{
    public function execute(?array $customScopes = null): RedirectResponse
    {
        $clientId    = config('twitch.client_id');
        $redirectUri = config('twitch.redirect_uri');
        $scopes      = $customScopes ? implode(' ', $customScopes) : implode(' ', config('twitch.scopes'));

        // Generate and store state for CSRF protection
        $state = \Illuminate\Support\Str::random(40);
        session(['oauth_state' => $state]);

        $url = config('twitch.auth_url').'/authorize?'.http_build_query([
            'client_id'     => $clientId,
            'redirect_uri'  => $redirectUri,
            'response_type' => 'code',
            'scope'         => $scopes,
            'state'         => $state,
        ]);

        return redirect($url);
    }
}
