# Custom Exceptions

We have specific exceptions for different error scenarios instead of generic ones.

## Why Custom Exceptions?

- Clear error handling in calling code
- Different recovery strategies for different errors
- Better logging and monitoring

## Exception Types

- `AuthenticationException` - Invalid/expired tokens (401)
- `ForbiddenException` - Missing permissions (403)
- `NotFoundException` - Resource doesn't exist (404)
- `RateLimitException` - Hit API limits (429)
- `ValidationException` - Bad request data (400)
- `ServerException` - Twitch API down (5xx)

## Usage

```php
try {
    $clips = $clipsService->getClips($broadcasterId);
} catch (AuthenticationException $e) {
    // Token expired, redirect to re-auth
    return redirect()->route('auth.twitch');
} catch (RateLimitException $e) {
    // Wait and retry
    sleep(60);
    return $this->getClips($broadcasterId);
} catch (ValidationException $e) {
    // Bad input, show error to user
    return back()->withErrors($e->getMessage());
}
```

## Base Exception

All inherit from `TwitchException`:

```php
abstract class TwitchException extends Exception {
    public function __construct(
        string $message,
        public readonly ?int $statusCode = null,
        public readonly ?array $context = null,
    ) {
        parent::__construct($message);
    }
}
```

The `$context` array includes API response details for debugging.