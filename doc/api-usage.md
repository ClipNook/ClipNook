# API Usage Guide

## Overview

The Twitch API integration provides a comprehensive set of methods for interacting with Twitch's Helix API v2. This guide covers all available functionality with code examples.

## Basic Usage

### Service Injection

```php
<?php

namespace App\Http\Controllers;

use App\Services\Twitch\TwitchService;
use Illuminate\Http\Request;

class TwitchController extends Controller
{
    public function __construct(
        private TwitchService $twitchService
    ) {}

    public function user(Request $request)
    {
        // Service is automatically injected
        $user = $this->twitchService->getUserById($request->user_id);

        return response()->json($user);
    }
}
```

### Facade Usage (Alternative)

```php
<?php

use App\Services\Twitch\TwitchService;

$user = TwitchService::getUserById('123456789');
```

## User Management

### Get User by ID

```php
<?php

$user = $twitchService->getUserById('123456789');

// Returns sanitized user data:
// [
//     'id' => '123456789',
//     'login' => 'username',
//     'display_name' => 'Display Name',
//     'type' => '',
//     'broadcaster_type' => 'partner',
//     'description' => 'Streamer description',
//     'profile_image_url' => 'https://...',
//     'offline_image_url' => 'https://...',
//     'view_count' => 15000,
//     'created_at' => '2016-05-20T20:10:44Z',
// ]
```

### Get User by Login

```php
<?php

$user = $twitchService->getUserByLogin('username');
```

### Get Multiple Users

```php
<?php

$users = $twitchService->getUsersByIds(['123456789', '987654321']);
$users = $twitchService->getUsersByLogins(['user1', 'user2']);
```

### Extract User ID from URL

```php
<?php

// All of these work:
$userId = $twitchService->extractUserIdFromUrl('https://twitch.tv/username');
$userId = $twitchService->extractUserIdFromUrl('twitch.tv/username');
$userId = $twitchService->extractUserIdFromUrl('username');

// Returns: '123456789' or null if invalid
```

## Stream Management

### Get Stream Information

```php
<?php

$stream = $twitchService->getStreamByUserId('123456789');

// Returns stream data or null if offline:
// [
//     'id' => '41375541868',
//     'user_id' => '123456789',
//     'user_login' => 'username',
//     'user_name' => 'Display Name',
//     'game_id' => '33214',
//     'game_name' => 'Fortnite',
//     'type' => 'live',
//     'title' => 'Best Stream Ever',
//     'viewer_count' => 1500,
//     'started_at' => '2023-12-01T10:00:00Z',
//     'language' => 'en',
//     'thumbnail_url' => 'https://...',
//     'tag_ids' => ['6ea6bca4-4712-4ab9-a906-e3336a9d8039'],
//     'tags' => ['English', 'Fortnite'],
//     'is_mature' => false,
// ]
```

### Get Multiple Streams

```php
<?php

$streams = $twitchService->getStreamsByUserIds(['123456789', '987654321']);
```

### Get Top Streams

```php
<?php

$topStreams = $twitchService->getTopStreams(20); // Limit: 1-100
```

### Get Streams by Game

```php
<?php

$gameStreams = $twitchService->getStreamsByGameId('33214', 10);
```

## Game Management

### Get Game Information

```php
<?php

$game = $twitchService->getGameById('33214');

// Returns:
// [
//     'id' => '33214',
//     'name' => 'Fortnite',
//     'box_art_url' => 'https://...',
//     'igdb_id' => '1905',
// ]
```

### Get Multiple Games

```php
<?php

$games = $twitchService->getGamesByIds(['33214', '21779']);
$games = $twitchService->getGamesByNames(['Fortnite', 'League of Legends']);
```

### Get Top Games

```php
<?php

$topGames = $twitchService->getTopGames(10);
```

## Follow Management

### Get User Followers

```php
<?php

$followers = $twitchService->getUserFollowers('123456789', 20);

// Returns paginated results:
// [
//     'data' => [...],
//     'pagination' => ['cursor' => '...'],
//     'total' => 15000,
// ]
```

### Get Users Followed by User

```php
<?php

$following = $twitchService->getUsersFollowedByUser('123456789', 20);
```

### Check Follow Relationship

```php
<?php

$isFollowing = $twitchService->isUserFollowing('follower_id', 'streamer_id');

// Returns boolean
```

## Channel Management

### Get Channel Information

```php
<?php

$channel = $twitchService->getChannelInformation('123456789');

// Returns:
// [
//     'broadcaster_id' => '123456789',
//     'broadcaster_login' => 'username',
//     'broadcaster_name' => 'Display Name',
//     'broadcaster_language' => 'en',
//     'game_id' => '33214',
//     'game_name' => 'Fortnite',
//     'title' => 'Best Stream Ever',
//     'delay' => 0,
//     'tags' => ['English', 'Fortnite'],
//     'content_classification_labels' => [],
//     'is_branded_content' => false,
// ]
```

### Update Channel Information

```php
<?php

$updated = $twitchService->updateChannelInformation('123456789', [
    'game_id' => '21779',
    'title' => 'New Stream Title',
    'tags' => ['English', 'New Game'],
]);
```

## Authentication & Tokens

### OAuth Flow

```php
<?php

namespace App\Actions\Twitch;

use App\Services\Twitch\TwitchService;
use Illuminate\Http\Request;

class AuthenticateTwitchUserAction
{
    public function __construct(
        private TwitchService $twitchService
    ) {}

    public function execute(Request $request): array
    {
        $code = $request->get('code');

        // Exchange code for tokens
        $tokenData = $this->twitchService->exchangeCodeForTokens($code);

        // Get user information
        $userData = $this->twitchService->getAuthenticatedUser($tokenData['access_token']);

        return [
            'user' => $userData,
            'tokens' => $tokenData,
        ];
    }
}
```

### Token Refresh

```php
<?php

// Automatic refresh (handled by service)
$user = $twitchService->getAuthenticatedUser($accessToken);

// Manual refresh
$newTokens = $twitchService->refreshAccessToken($refreshToken);
```

### Validate Token

```php
<?php

$isValid = $twitchService->validateAccessToken($accessToken);

// Returns boolean
```

## Error Handling

### Try-Catch Blocks

```php
<?php

use App\Services\Twitch\Exceptions\TwitchApiException;
use App\Services\Twitch\Exceptions\TwitchAuthenticationException;

try {
    $user = $twitchService->getUserById('123456789');
} catch (TwitchAuthenticationException $e) {
    // Handle authentication errors (invalid/expired tokens)
    return response()->json(['error' => 'Authentication failed'], 401);
} catch (TwitchApiException $e) {
    // Handle API errors (rate limits, server errors, etc.)
    return response()->json(['error' => 'API request failed'], 500);
} catch (\Exception $e) {
    // Handle unexpected errors
    return response()->json(['error' => 'Unexpected error'], 500);
}
```

### Custom Exception Handling

```php
<?php

// In your exception handler
public function render($request, Throwable $exception)
{
    if ($exception instanceof TwitchApiException) {
        return response()->json([
            'error' => 'Twitch API Error',
            'message' => $exception->getMessage(),
            'status_code' => $exception->getCode(),
        ], 500);
    }

    return parent::render($request, $exception);
}
```

## Caching

### Automatic Caching

The service automatically caches API responses:

```php
<?php

// First call - fetches from API and caches
$user = $twitchService->getUserById('123456789');

// Second call within TTL - returns cached data
$user = $twitchService->getUserById('123456789');
```

### Cache Configuration

```php
<?php

// In config/twitch.php
'cache_ttl' => env('TWITCH_CACHE_TTL', 3600), // 1 hour default
```

### Manual Cache Management

```php
<?php

use Illuminate\Support\Facades\Cache;

// Clear specific cache
Cache::forget('twitch:user:123456789');

// Clear all Twitch caches
Cache::tags(['twitch'])->flush();
```

## Rate Limiting

### Automatic Rate Limiting

The service includes automatic rate limiting:

```php
<?php

// Rate limited to 100 requests per minute by default
for ($i = 0; $i < 150; $i++) {
    $user = $twitchService->getUserById('123456789');
    // Automatic delays when approaching limits
}
```

### Custom Rate Limiting

```php
<?php

// In routes/api.php
Route::middleware(['throttle:twitch-api'])->group(function () {
    Route::get('/twitch/user/{id}', [TwitchController::class, 'user']);
});
```

## Data Sanitization

### Automatic Sanitization

All data is automatically sanitized:

```php
<?php

$user = $twitchService->getUserById('123456789');
// Description field is automatically sanitized for XSS protection
echo $user['description']; // Safe to output
```

### Manual Sanitization

```php
<?php

use App\Services\Twitch\TwitchDataSanitizer;

$sanitizer = app(TwitchDataSanitizer::class);

$cleanText = $sanitizer->sanitizeText('<script>alert("xss")</script>Hello World');
$cleanUrl = $sanitizer->sanitizeUrl('javascript:alert("xss")');
```

## Testing

### Unit Testing

```php
<?php

use App\Services\Twitch\TwitchService;
use Tests\TestCase;

class TwitchServiceTest extends TestCase
{
    public function test_extract_user_id_from_url()
    {
        $service = app(TwitchService::class);

        $this->assertEquals('123456789', $service->extractUserIdFromUrl('https://twitch.tv/username'));
        $this->assertNull($service->extractUserIdFromUrl('invalid-url'));
    }
}
```

### Feature Testing

```php
<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TwitchOAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_twitch_oauth_callback()
    {
        // Mock Twitch API responses
        Http::fake([
            'https://id.twitch.tv/oauth2/token' => Http::response([
                'access_token' => 'test_token',
                'refresh_token' => 'refresh_token',
                'expires_in' => 3600,
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

        $response = $this->get('/auth/twitch/callback?code=test_code');

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('users', [
            'twitch_id' => '123456789',
            'twitch_login' => 'testuser',
        ]);
    }
}
```

## Advanced Usage

### Batch Operations

```php
<?php

// Get multiple users and streams in one go
$userIds = ['123', '456', '789'];

$users = $twitchService->getUsersByIds($userIds);
$streams = $twitchService->getStreamsByUserIds($userIds);

// Process data
foreach ($users as $user) {
    $stream = collect($streams)->firstWhere('user_id', $user['id']);
    // Do something with user and stream data
}
```

### Webhook Integration

```php
<?php

// Handle stream online/offline events
Route::post('/webhooks/twitch/stream', function (Request $request) {
    $event = $request->input('event');

    if ($event['type'] === 'stream.online') {
        // Stream went online
        $streamData = $event['data'];
        // Process stream online event
    }

    return response()->json(['status' => 'ok']);
})->middleware('twitch-webhook');
```

### Real-time Updates

```php
<?php

// Using Laravel Echo with Twitch events
Echo.channel('twitch.stream.123456789')
    .listen('.stream.online', (e) => {
        console.log('Stream went online:', e.stream);
    })
    .listen('.stream.offline', (e) => {
        console.log('Stream went offline');
    });
```

## Performance Tips

1. **Use Caching**: API responses are cached automatically
2. **Batch Requests**: Get multiple users/streams in single API calls
3. **Rate Limiting**: Respect API limits to avoid throttling
4. **Background Processing**: Use queues for heavy operations
5. **Database Indexing**: Index frequently queried fields

## Security Best Practices

1. **Validate Input**: Always validate user input
2. **Sanitize Output**: Data is automatically sanitized
3. **Secure Tokens**: Store tokens encrypted
4. **HTTPS Only**: Use HTTPS for all requests
5. **Rate Limiting**: Implement rate limiting on routes
6. **CSRF Protection**: Use CSRF tokens for forms