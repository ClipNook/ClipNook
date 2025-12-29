# Token Refresh Service

Handles automatic token refresh. Twitch tokens expire every 4 hours, so we need to refresh them before they expire.

## Why a separate service?

Token refresh logic was getting complex in the OAuth service, so we extracted it.

## How it works

1. Check if token is expired or will expire soon (within 5 minutes)
2. If yes, use refresh token to get new access token
3. Update user record with new tokens
4. Return valid access token

## Usage

```php
$tokenService = app(TokenRefreshService::class);
$validToken = $tokenService->getValidToken($user);

// Now safe to use $validToken for API calls
```

## Edge Cases

- Refresh token expired? Throw exception, user needs to re-auth
- Concurrent requests? We use database transactions to prevent race conditions
- Network errors? Retry with exponential backoff

## Database Updates

When refreshing, we update both tokens atomically:

```php
DB::transaction(function () use ($user, $newTokens) {
    $user->update([
        'twitch_access_token' => $newTokens->accessToken,
        'twitch_refresh_token' => $newTokens->refreshToken,
        'twitch_token_expires_at' => $newTokens->expiresAt,
    ]);
});
```

## Fallback to App Token

For some endpoints (like getting public user info), we can use the app's client credentials token instead of user tokens.

```php
$token = $tokenService->getValidToken($user) ?? $this->getAppToken();
```

This is useful when user tokens are invalid but we still need to make some API calls.