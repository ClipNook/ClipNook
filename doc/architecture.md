# Architecture Overview

## System Architecture

The Twitch integration follows a clean architecture pattern with clear separation of concerns:

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Controllers   │    │    Services     │    │   Repositories  │
│                 │    │                 │    │                 │
│ - Web Routes    │◄──►│ - TwitchService │◄──►│ - Cache         │
│ - API Routes    │    │ - TokenManager  │    │ - Database      │
│ - Middleware    │    │ - ApiClient     │    │ - External APIs │
└─────────────────┘    └─────────────────┘    └─────────────────┘
         │                       │                       │
         ▼                       ▼                       ▼
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Actions       │    │     DTOs        │    │   Exceptions    │
│                 │    │                 │    │                 │
│ - Authenticate  │    │ - TokenDTO      │    │ - ApiException  │
│ - ExchangeCode  │    │ - StreamerDTO   │    │ - AuthException │
│ - Redirect      │    │ - ClipDTO       │    │                 │
└─────────────────┘    └─────────────────┘    └─────────────────┘
```

## Core Components

### 1. Service Layer

#### TwitchService (`app/Services/Twitch/TwitchService.php`)
Main service orchestrating all Twitch API interactions.

**Key Methods:**
- `getCurrentUser()`: Get authenticated user info
- `getStreamer(string $userId)`: Get specific streamer info
- `getStreamInfo(string $userId)`: Get live stream data
- `getFollowedStreams(string $userId)`: Get followed streams
- `extractUserIdFromUrl(string $url)`: Parse Twitch URLs

#### TwitchApiClient (`app/Services/Twitch/TwitchApiClient.php`)
Handles HTTP communication with Twitch API.

**Features:**
- Automatic retry on 401 (token refresh)
- Request/response logging
- Timeout configuration
- Header management

#### TwitchTokenManager (`app/Services/Twitch/TwitchTokenManager.php`)
Manages OAuth tokens and refresh logic.

**Features:**
- Token storage and retrieval
- Automatic refresh before expiry
- Session-based persistence
- Secure encrypted storage

### 2. Data Transfer Objects (DTOs)

Located in `app/Services/Twitch/DTOs/`:

- **TokenDTO**: OAuth token information
- **StreamerDTO**: User/streamer data
- **StreamDTO**: Live stream information
- **ClipDTO**: Clip metadata
- **GameDTO**: Game information
- **VideoDTO**: Video data

### 3. Actions

Located in `app/Actions/Twitch/`:

- **AuthenticateTwitchUserAction**: Handle user authentication
- **ExchangeCodeForTokenAction**: OAuth code exchange
- **RedirectToTwitchAction**: OAuth redirect handling

### 4. Data Sanitization

#### TwitchDataSanitizer (`app/Services/Twitch/TwitchDataSanitizer.php`)
Instance-based sanitizer for API responses.

**Methods:**
- `sanitizeText(string $text)`: XSS protection
- `sanitizeUrl(string $url)`: Domain validation
- `sanitizeInt(mixed $value, int $default)`: Safe integer conversion
- `sanitizeBool(mixed $value)`: Boolean normalization

## Dependency Injection

All services are registered in `TwitchServiceProvider`:

```php
$this->app->singleton(TwitchApiClient::class, function ($app) {
    return new TwitchApiClient(/* config */);
});

$this->app->singleton(TwitchTokenManager::class, function ($app) {
    return new TwitchTokenManager(/* dependencies */);
});

$this->app->singleton(TwitchService::class, function ($app) {
    return new TwitchService(
        apiClient: $app->make(TwitchApiClient::class),
        tokenManager: $app->make(TwitchTokenManager::class),
        sanitizer: $app->make(TwitchDataSanitizer::class),
    );
});
```

## Error Handling

### Exception Hierarchy

```
TwitchException (base)
├── TwitchApiException
├── TwitchAuthException
└── TwitchValidationException
```

### Error Responses

- **400 Bad Request**: Invalid input parameters
- **401 Unauthorized**: Invalid or expired tokens
- **403 Forbidden**: Insufficient permissions
- **404 Not Found**: Resource not found
- **429 Too Many Requests**: Rate limit exceeded
- **500 Internal Server Error**: Server errors

## Caching Strategy

### Cache Keys
- `twitch_user_{user_id}`: User information (1 hour)
- `twitch_stream_{user_id}`: Stream data (5 minutes)
- `twitch_followed_{user_id}`: Followed streams (10 minutes)

### Cache Configuration
```php
'cache_ttl' => env('TWITCH_CACHE_TTL', 3600), // seconds
```

## Security Measures

1. **Input Validation**: All inputs validated and sanitized
2. **Rate Limiting**: API calls limited per user/time
3. **Token Encryption**: Sensitive data encrypted in storage
4. **Domain Whitelisting**: Only allowed domains for URLs
5. **CSRF Protection**: Laravel's built-in CSRF tokens
6. **SQL Injection Prevention**: Eloquent ORM usage

## Performance Considerations

1. **Connection Pooling**: Reuse HTTP connections
2. **Response Caching**: Reduce API calls
3. **Lazy Loading**: Load data on demand
4. **Background Jobs**: Heavy operations queued
5. **Database Indexing**: Optimized queries
6. **CDN Integration**: Static asset caching