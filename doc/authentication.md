# Authentication Guide

## Overview

The Twitch integration uses OAuth 2.0 for user authentication. This guide covers the complete authentication flow, token management, and security best practices.

## OAuth 2.0 Flow

### 1. Authorization Request

Redirect users to Twitch for authorization:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TwitchAuthController extends Controller
{
    public function redirectToTwitch()
    {
        $clientId = config('twitch.client_id');
        $redirectUri = config('twitch.oauth.redirect_uri');
        $scopes = implode(' ', config('twitch.oauth.scopes'));

        $url = 'https://id.twitch.tv/oauth2/authorize?' . http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => $scopes,
            'state' => csrf_token(), // CSRF protection
        ]);

        return redirect($url);
    }
}
```

### 2. Authorization Callback

Handle the OAuth callback:

```php
<?php

namespace App\Actions\Twitch;

use App\Models\User;
use App\Services\Twitch\TwitchService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthenticateTwitchUserAction
{
    public function __construct(
        private TwitchService $twitchService
    ) {}

    public function execute(string $code, string $state): User
    {
        // Verify CSRF token
        if (!hash_equals(session('_token'), $state)) {
            throw new \Exception('Invalid state parameter');
        }

        // Exchange code for tokens
        $tokenData = $this->twitchService->exchangeCodeForTokens($code);

        // Get user information
        $userData = $this->twitchService->getAuthenticatedUser($tokenData['access_token']);

        // Create or update user
        return DB::transaction(function () use ($userData, $tokenData) {
            $user = User::updateOrCreate(
                ['twitch_id' => $userData['id']],
                [
                    'twitch_login' => $userData['login'],
                    'twitch_display_name' => $userData['display_name'],
                    'twitch_email' => $userData['email'] ?? null,
                    'twitch_access_token' => encrypt($tokenData['access_token']),
                    'twitch_refresh_token' => encrypt($tokenData['refresh_token']),
                    'twitch_token_expires_at' => now()->addSeconds($tokenData['expires_in']),
                    'scopes' => $tokenData['scope'] ?? [],
                    'description' => $userData['description'] ?? null,
                    'twitch_avatar' => $userData['profile_image_url'] ?? null,
                    'avatar_source' => 'twitch',
                    'last_login_at' => now(),
                ]
            );

            return $user;
        });
    }
}
```

### 3. Controller Implementation

```php
<?php

namespace App\Http\Controllers;

use App\Actions\Twitch\AuthenticateTwitchUserAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TwitchAuthController extends Controller
{
    public function callback(Request $request, AuthenticateTwitchUserAction $action)
    {
        try {
            $request->validate([
                'code' => 'required|string',
                'state' => 'required|string',
            ]);

            $user = $action->execute($request->code, $request->state);

            Auth::login($user);

            return redirect('/dashboard')->with('success', 'Successfully authenticated with Twitch!');
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Authentication failed: ' . $e->getMessage());
        }
    }
}
```

## Token Management

### Token Storage

Tokens are encrypted and stored in the database:

```php
<?php

// In User model
protected $casts = [
    'twitch_token_expires_at' => 'datetime',
    'scopes' => 'array',
    'preferences' => 'array',
];

protected $hidden = [
    'twitch_access_token',
    'twitch_refresh_token',
];
```

### Token Refresh

Automatic token refresh when expired:

```php
<?php

namespace App\Services\Twitch;

class TwitchTokenManager
{
    public function getValidAccessToken(User $user): string
    {
        if ($this->isTokenExpired($user)) {
            $this->refreshUserToken($user);
        }

        return decrypt($user->twitch_access_token);
    }

    private function isTokenExpired(User $user): bool
    {
        $buffer = config('twitch.token_refresh_buffer', 300); // 5 minutes
        return $user->twitch_token_expires_at->subSeconds($buffer)->isPast();
    }

    private function refreshUserToken(User $user): void
    {
        $refreshToken = decrypt($user->twitch_refresh_token);

        $response = Http::asForm()->post('https://id.twitch.tv/oauth2/token', [
            'client_id' => config('twitch.client_id'),
            'client_secret' => config('twitch.client_secret'),
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
        ]);

        if ($response->successful()) {
            $data = $response->json();

            $user->update([
                'twitch_access_token' => encrypt($data['access_token']),
                'twitch_refresh_token' => encrypt($data['refresh_token'] ?? $refreshToken),
                'twitch_token_expires_at' => now()->addSeconds($data['expires_in']),
                'scopes' => $data['scope'] ?? $user->scopes,
            ]);
        } else {
            throw new TwitchAuthenticationException('Failed to refresh token');
        }
    }
}
```

### Token Validation

```php
<?php

public function validateAccessToken(string $token): bool
{
    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $token,
    ])->get('https://id.twitch.tv/oauth2/validate');

    return $response->successful();
}
```

## User Sessions

### Login/Logout

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function logout(Request $request)
    {
        // Revoke Twitch token (optional)
        if ($user = Auth::user()) {
            $this->revokeTwitchToken($user);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    private function revokeTwitchToken($user)
    {
        if ($user->twitch_access_token) {
            Http::post('https://id.twitch.tv/oauth2/revoke', [
                'client_id' => config('twitch.client_id'),
                'token' => decrypt($user->twitch_access_token),
            ]);
        }
    }
}
```

### Session Management

```php
<?php

// In User model
protected static function booted()
{
    static::updating(function ($user) {
        if ($user->isDirty('last_activity_at')) {
            // Update last activity
        }
    });
}

// Update activity on each request
class UpdateUserActivity
{
    public function handle($request, $next)
    {
        if (Auth::check()) {
            Auth::user()->update(['last_activity_at' => now()]);
        }

        return $next($request);
    }
}
```

## Security Features

### CSRF Protection

```php
<?php

// In OAuth redirect
'state' => csrf_token(),

// In callback
if (!hash_equals(session('_token'), $request->state)) {
    throw new \Exception('Invalid state parameter');
}
```

### HTTPS Enforcement

```php
<?php

// In config/twitch.php
'security' => [
    'require_https' => true,
],

// In middleware
class RequireHttps
{
    public function handle($request, $next)
    {
        if (config('twitch.security.require_https') && !$request->secure()) {
            abort(403, 'HTTPS required for Twitch authentication');
        }

        return $next($request);
    }
}
```

### Scope Management

```php
<?php

// Define required scopes
'scopes' => [
    'user:read:email',
    'user:read:follows',
    'channel:read:stream_key',
],

// Check user permissions
if (!in_array('channel:read:stream_key', $user->scopes)) {
    abort(403, 'Insufficient permissions');
}
```

### Rate Limiting

```php
<?php

// In routes/web.php
Route::middleware(['auth', 'throttle:twitch-api'])->group(function () {
    Route::get('/twitch/stream-key', [TwitchController::class, 'getStreamKey']);
});
```

## User Permissions

### Role-Based Access

```php
<?php

// In User model
public function hasRole(string $role): bool
{
    return $this->{$role} ?? false;
}

public function isStreamer(): bool
{
    return $this->is_streamer;
}

public function isModerator(): bool
{
    return $this->is_moderator;
}

// In controllers
public function someProtectedAction()
{
    $this->authorize('manage-stream', $this->user);

    if (!$this->user->isStreamer()) {
        abort(403, 'Only streamers can access this');
    }
}
```

### Permission Gates

```php
<?php

// In AuthServiceProvider
Gate::define('manage-twitch-stream', function ($user) {
    return $user->is_streamer || $user->is_moderator;
});

Gate::define('view-twitch-analytics', function ($user) {
    return $user->is_streamer || $user->is_admin;
});
```

## Error Handling

### Authentication Errors

```php
<?php

namespace App\Exceptions;

use Exception;

class TwitchAuthenticationException extends Exception
{
    protected $errors = [
        400 => 'Invalid request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        429 => 'Too many requests',
    ];

    public function getErrorMessage(): string
    {
        return $this->errors[$this->getCode()] ?? 'Authentication failed';
    }
}
```

### Exception Handler

```php
<?php

// In app/Exceptions/Handler.php
public function render($request, Throwable $exception)
{
    if ($exception instanceof TwitchAuthenticationException) {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'authentication_failed',
                'message' => $exception->getErrorMessage(),
            ], 401);
        }

        return redirect('/login')->with('error', $exception->getErrorMessage());
    }

    return parent::render($request, $exception);
}
```

## Testing Authentication

### Unit Tests

```php
<?php

namespace Tests\Unit;

use App\Actions\Twitch\AuthenticateTwitchUserAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TwitchAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_oauth_flow()
    {
        // Mock HTTP responses
        Http::fake([
            'https://id.twitch.tv/oauth2/token' => Http::response([
                'access_token' => 'test_access_token',
                'refresh_token' => 'test_refresh_token',
                'expires_in' => 3600,
                'scope' => ['user:read:email'],
            ]),
            'https://api.twitch.tv/helix/users' => Http::response([
                'data' => [
                    [
                        'id' => '123456789',
                        'login' => 'testuser',
                        'display_name' => 'Test User',
                        'email' => 'test@example.com',
                    ]
                ]
            ]),
        ]);

        $action = app(AuthenticateTwitchUserAction::class);
        $user = $action->execute('test_code', csrf_token());

        $this->assertEquals('123456789', $user->twitch_id);
        $this->assertEquals('testuser', $user->twitch_login);
    }
}
```

### Feature Tests

```php
<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TwitchOAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_twitch_oauth_redirect()
    {
        $response = $this->get('/auth/twitch');

        $response->assertRedirect();
        $response->assertRedirectContains('https://id.twitch.tv/oauth2/authorize');
    }

    public function test_twitch_oauth_callback_success()
    {
        Http::fake([
            // ... mock responses
        ]);

        $response = $this->get('/auth/twitch/callback?code=test_code&state=' . csrf_token());

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }

    public function test_twitch_oauth_callback_invalid_state()
    {
        $response = $this->get('/auth/twitch/callback?code=test_code&state=invalid');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }
}
```

## Frontend Integration

### JavaScript SDK

```javascript
// Initialize Twitch SDK
Twitch.init({
    clientId: process.env.TWITCH_CLIENT_ID
});

// Login
function loginWithTwitch() {
    Twitch.login({
        scope: ['user:read:email', 'user:read:follows']
    });
}

// Handle auth callback
Twitch.onAuthorized((auth) => {
    // Send token to backend
    fetch('/auth/twitch/token', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            access_token: auth.token
        })
    });
});
```

### React Integration

```jsx
import React, { useEffect } from 'react';
import { useAuth } from './hooks/useAuth';

function TwitchLoginButton() {
    const { login } = useAuth();

    const handleLogin = () => {
        window.location.href = '/auth/twitch';
    };

    useEffect(() => {
        // Handle OAuth callback
        const urlParams = new URLSearchParams(window.location.search);
        const code = urlParams.get('code');

        if (code) {
            login(code);
        }
    }, [login]);

    return (
        <button onClick={handleLogin} className="twitch-login-btn">
            Login with Twitch
        </button>
    );
}
```

## Best Practices

1. **Secure Token Storage**: Always encrypt tokens in database
2. **Token Refresh**: Implement automatic token refresh
3. **CSRF Protection**: Use state parameter for CSRF protection
4. **HTTPS Only**: Require HTTPS for all auth flows
5. **Scope Minimization**: Request only necessary scopes
6. **Error Handling**: Proper error handling and user feedback
7. **Session Management**: Implement proper session handling
8. **Rate Limiting**: Protect against abuse with rate limiting
9. **Audit Logging**: Log authentication events
10. **Regular Testing**: Test auth flows regularly

## Troubleshooting

### Common Issues

1. **Invalid Client**: Check client ID and secret
2. **Redirect URI Mismatch**: Ensure redirect URI matches Twitch app settings
3. **Invalid Scope**: Verify requested scopes are valid
4. **Token Expired**: Implement proper token refresh
5. **CSRF Token Mismatch**: Ensure state parameter is handled correctly
6. **HTTPS Required**: Use HTTPS in production

### Debug Mode

```php
<?php

// In config/app.php
'debug' => env('APP_DEBUG', false),

// In config/logging.php
'channels' => [
    'twitch' => [
        'driver' => 'single',
        'path' => storage_path('logs/twitch.log'),
        'level' => env('LOG_LEVEL', 'debug'),
    ],
],
```

### Logging Authentication Events

```php
<?php

Log::channel('twitch')->info('User authenticated', [
    'user_id' => $user->id,
    'twitch_id' => $user->twitch_id,
    'ip' => request()->ip(),
    'user_agent' => request()->userAgent(),
]);
```