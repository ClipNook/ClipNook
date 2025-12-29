# OAuth Service

Handles the OAuth dance with Twitch. This is pretty standard OAuth2 but with Twitch-specific quirks.

## Flow

1. Generate auth URL with scopes
2. User authorizes, gets redirected back with code
3. Exchange code for tokens
4. Store refresh token (encrypted in DB)
5. Use access token for API calls
6. Refresh when expired

## Scopes

We request these scopes:
- `user:read:email` - for user email
- `clips:edit` - to create clips
- `channel:read:stream` - to check if streamer is live

## Token Storage

Tokens are encrypted in the database using Laravel's encrypted casts:

```php
protected $casts = [
    'twitch_access_token' => 'encrypted',
    'twitch_refresh_token' => 'encrypted',
];
```

## Refresh Logic

The TokenRefreshService automatically refreshes tokens when they're close to expiring:

```php
$tokenService = app(TokenRefreshService::class);
$validToken = $tokenService->getValidToken($user);

// This will refresh if needed and return a valid token
```

## Validation

Always validate tokens before use:

```php
$oauth = app(OAuthInterface::class);
$userData = $oauth->validateToken($accessToken);

if ($userData->id !== $expectedUserId) {
    throw new SecurityException('Token user mismatch');
}
```

Twitch tokens expire in 4 hours, so we refresh them proactively.