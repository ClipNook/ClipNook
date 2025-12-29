<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Twitch\Contracts\OAuthInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang as LangFacade;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class TwitchController extends Controller
{
    private readonly OAuthInterface $oauth;

    public function __construct(OAuthInterface $oauth)
    {
        $this->oauth = $oauth;
    }

    public function redirect(Request $request)
    {
        // Require explicit consent
        if (! $request->has('consent')) {
            return Redirect::route('login')->withErrors(['twitch' => LangFacade::get('twitch.privacy.consent_required')]);
        }

        // Persist the "remember" preference in session so it survives the OAuth round-trip
        $request->session()->put('twitch.remember', $request->boolean('remember'));

        $state = bin2hex(random_bytes(16));
        $request->session()->put('twitch_oauth_state', $state);

        try {
            $url = $this->oauth->getAuthorizationUrl($state);
            // Do not log URL or state to avoid leaking sensitive data
            Log::info('Twitch OAuth redirect initiated');

            return Redirect::to($url);
        } catch (\Throwable $e) {
            // Log a minimal error message (no traces or sensitive data)
            Log::error('Twitch OAuth redirect failed', ['message' => $e->getMessage()]);

            return Redirect::route('login')->withErrors(['twitch' => LangFacade::get('twitch.oauth.authorization_failed')]);
        }
    }

    public function callback(Request $request)
    {
        // Log event (do not include query parameters)
        Log::info('Twitch OAuth callback received');

        $error = $request->query('error');

        if ($error) {
            // Avoid logging error_description as it may contain user-provided text
            Log::warning('Twitch OAuth returned an error');

            return Redirect::route('login')->withErrors(['twitch' => LangFacade::get('twitch.oauth.authorization_failed')]);
        }

        $state        = $request->query('state');
        $sessionState = $request->session()->pull('twitch_oauth_state');

        if (empty($state) || $state !== $sessionState) {
            // Do not log raw state values
            Log::warning('Invalid Twitch OAuth state');

            return Redirect::route('login')->withErrors(['twitch' => LangFacade::get('twitch.oauth.authorization_failed')]);
        }

        $code = $request->query('code');

        try {
            $token = $this->oauth->getAccessToken($code);

            // Store token in session (short-lived) - kept for possible later use
            $request->session()->put('twitch_token', $token->toArray());

            // Validate token and fetch user details (from Helix /users)
            $twitchUser = $this->oauth->getUser($token->accessToken);
            // Minimal log event only
            Log::info('Twitch user retrieved');

            // Map or create a local user record and log them in
            try {
                $localUser = \App\Models\User::where('twitch_id', $twitchUser->id)
                    ->orWhere('email', $twitchUser->email)
                    ->first();

                if (! $localUser) {
                    $localUser = \App\Models\User::create([
                        'name'                => $twitchUser->displayName ?? $twitchUser->login ?? null,
                        'email'               => $twitchUser->email ?? null,
                        'twitch_id'           => $twitchUser->id ?? null,
                        'twitch_login'        => $twitchUser->login ?? null,
                        'twitch_display_name' => $twitchUser->displayName ?? null,
                        'twitch_email'        => $twitchUser->email ?? null,
                        'twitch_avatar'       => $twitchUser->profileImageUrl ?? null,
                    ]);
                    // Minimal logging: avoid writing user identifiers to logs
                    Log::info('Created local user from Twitch data');
                } else {
                    // Update twitch-specific fields
                    $localUser->update([
                        'twitch_id'           => $twitchUser->id ?? $localUser->twitch_id,
                        'twitch_login'        => $twitchUser->login ?? $localUser->twitch_login,
                        'twitch_display_name' => $twitchUser->displayName ?? $localUser->twitch_display_name,
                        'twitch_email'        => $twitchUser->email ?? $localUser->twitch_email,
                        'twitch_avatar'       => $localUser->avatar_disabled ? $localUser->twitch_avatar : ($twitchUser->profileImageUrl ?? $localUser->twitch_avatar),
                    ]);
                    // Minimal logging: avoid writing user identifiers to logs
                    Log::info('Updated local user with Twitch data');
                }

                // Persist tokens and expiry (Eloquent will encrypt via casts)
                $localUser->twitch_access_token     = $token->accessToken ?? null;
                $localUser->twitch_refresh_token    = $token->refreshToken ?? null;
                $localUser->twitch_token_expires_at = property_exists($token, 'expiresAt') && $token->expiresAt ? \Carbon\Carbon::parse($token->expiresAt) : null;

                // Optionally download and store avatar locally for GDPR compliance
                try {
                    $twitchConfig = config('services.twitch', []);
                    $storeAvatars = $twitchConfig['privacy']['store_avatars'] ?? true;

                    if ($storeAvatars && ! empty($twitchUser->profileImageUrl) && ! $localUser->avatar_disabled) {
                        try {
                            $resp = \Illuminate\Support\Facades\Http::timeout(10)->get($twitchUser->profileImageUrl);

                            if ($resp->ok()) {
                                $contentType = $resp->header('Content-Type') ?? '';
                                $ext         = 'jpg';
                                if (str_contains($contentType, '/')) {
                                    [$type,$sub] = explode('/', $contentType, 2);
                                    $ext         = $sub ?: $ext;
                                } else {
                                    $pathInfo = pathinfo(parse_url($twitchUser->profileImageUrl, PHP_URL_PATH));
                                    if (! empty($pathInfo['extension'])) {
                                        $ext = $pathInfo['extension'];
                                    }
                                }

                                $filename = 'avatars/'.($twitchUser->id ?? uniqid()).'.'.$ext;
                                \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $resp->body());

                                // Store local path
                                $localUser->twitch_avatar = $filename;
                            }
                        } catch (\Throwable $e) {
                            // Do not fail login on avatar fetch; log minimal warning
                            Log::warning('Avatar fetch failed');
                        }
                    }
                } catch (\Throwable $e) {
                    // Ignore any issues here
                }

                $localUser->save();

                // Log the local user in (respect 'remember' setting stored in session)
                $remember = (bool) $request->session()->pull('twitch.remember', (bool) (config('services.twitch.remember') ?? true));
                \Illuminate\Support\Facades\Auth::login($localUser, $remember);

                return Redirect::route('home')->with('status', LangFacade::get('twitch.oauth.login_success'));
            } catch (\Exception $e) {
                // Log minimal error for debugging without exposing sensitive data
                Log::error('Failed to map/login local user from Twitch', ['message' => $e->getMessage()]);

                return Redirect::route('login')->withErrors(['twitch' => LangFacade::get('twitch.oauth.authorization_failed')]);
            }
        } catch (\Exception $e) {
            // Log minimal error for debugging without exposing stack traces or sensitive data
            Log::error('Twitch OAuth callback failed', ['message' => $e->getMessage()]);

            return Redirect::route('login')->withErrors(['twitch' => LangFacade::get('twitch.oauth.authorization_failed')]);
        }
    }

    public function revoke(Request $request)
    {
        // Prefer logged-in user's stored token
        $user  = $request->user();
        $token = null;

        if ($user && ! empty($user->twitch_access_token)) {
            $token = $user->twitch_access_token;
        }

        // Fallback to session token
        $sessionToken = $request->session()->pull('twitch_token.access_token');
        if (! $token && $sessionToken) {
            $token = $sessionToken;
        }

        if ($token) {
            try {
                $this->oauth->revokeToken($token);
            } catch (\Exception $e) {
                Log::warning('Failed to revoke Twitch token', ['message' => $e->getMessage()]);
            }
        }

        return Redirect::route('home')->with('status', LangFacade::get('twitch.oauth.logout_success'));
    }

    /**
     * Log out the current user and revoke Twitch access if present.
     */
    public function logout(Request $request)
    {
        $user = $request->user();

        // Revoke token if we have one (prefer user, fallback to session)
        $token = null;
        if ($user && ! empty($user->twitch_access_token)) {
            $token = $user->twitch_access_token;
        } elseif ($request->session()->has('twitch_token.access_token')) {
            $token = $request->session()->pull('twitch_token.access_token');
        }

        if ($token) {
            try {
                $this->oauth->revokeToken($token);
            } catch (\Exception $e) {
                Log::warning('Failed to revoke Twitch token during logout', ['message' => $e->getMessage()]);
            }
        }

        // Remove stored tokens from user record (if any)
        if ($user) {
            $user->fill([
                'twitch_access_token'     => null,
                'twitch_refresh_token'    => null,
                'twitch_token_expires_at' => null,
            ]);
            $user->save();
        }

        // Destroy session and logout
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::route('home')->with('status', LangFacade::get('twitch.oauth.logout_success'));
    }

    /**
     * Show the login page with Twitch & privacy info.
     */
    public function login(Request $request)
    {
        // If the user is already authenticated, send them home
        if ($request->user()) {
            return Redirect::route('home');
        }

        $twitch  = config('services.twitch', []);
        $privacy = $twitch['privacy'] ?? [];

        $missingClientId     = empty($twitch['client_id']);
        $missingClientSecret = empty($twitch['client_secret']);
        $missing             = $missingClientId || $missingClientSecret;

        return view('auth.login', [
            'twitch_configured'            => ! $missing,
            'twitch_missing'               => $missing,
            'twitch_missing_client_id'     => $missingClientId,
            'twitch_missing_client_secret' => $missingClientSecret,
            'privacy'                      => $privacy,
            'twitch_scopes'                => $twitch['scopes'] ?? '',
        ]);
    }
}
