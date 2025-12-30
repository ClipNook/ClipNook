<?php

namespace App\Actions\Twitch;

use Illuminate\Http\RedirectResponse;

class RedirectToTwitchAction
{
    public function execute(?array $customScopes = null): RedirectResponse
    {
        $clientId    = config('twitch.client_id');
        $redirectUri = config('twitch.redirect_uri');
        $scopes      = $customScopes ? implode(' ', $customScopes) : implode(' ', config('twitch.scopes'));

        $url = config('twitch.auth_url').'/authorize?'.http_build_query([
            'client_id'     => $clientId,
            'redirect_uri'  => $redirectUri,
            'response_type' => 'code',
            'scope'         => $scopes,
            'state'         => csrf_token(), // For CSRF protection
        ]);

        return redirect($url);
    }
}
