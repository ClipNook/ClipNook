<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Twitch\AuthenticateTwitchUserAction;
use App\Actions\Twitch\ExchangeCodeForTokenAction;
use App\Actions\Twitch\RedirectToTwitchAction;
use App\Http\Requests\TwitchLoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use function __;
use function redirect;
use function session;

final class TwitchOAuthController extends Controller
{
    public function redirectToTwitch(TwitchLoginRequest $request, RedirectToTwitchAction $action)
    {
        $validated = $request->validatedWithDefaults();

        // Store user preferences in session
        session([
            'login_preferences' => [
                'scopes' => $validated['scopes'],
            ],
        ]);

        return $action->execute($validated['scopes']);
    }

    public function handleCallback(Request $request, ExchangeCodeForTokenAction $action, AuthenticateTwitchUserAction $authAction)
    {
        $validated = $request->validate([
            'code'  => 'required|string',
            'state' => ['required', 'string', new \App\Rules\ValidOAuthState()],
        ]);

        $code = $validated['code'];

        $token = $action->execute($code);

        if (! $token) {
            return redirect('/')->with('error', __('twitch.oauth_failed_exchange_token'));
        }

        $user = $authAction->execute($token);

        if (! $user) {
            return redirect('/')->with('error', __('twitch.oauth_failed_get_user'));
        }

        return redirect('/')->with('success', __('twitch.oauth_success_login'));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Successfully logged out!');
    }
}
