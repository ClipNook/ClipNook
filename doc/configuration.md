# Configuration

## Environment Variables

Add these to your `.env` file:

```env
# Twitch API Configuration
TWITCH_CLIENT_ID=your_twitch_client_id
TWITCH_CLIENT_SECRET=your_twitch_client_secret

# Optional: Custom Configuration
TWITCH_CACHE_TTL=3600
TWITCH_TIMEOUT=30
TWITCH_TOKEN_REFRESH_BUFFER=300
TWITCH_RATE_LIMIT_REQUESTS=100
TWITCH_RATE_LIMIT_DECAY=60
```

## Configuration Files

### config/twitch.php

```php
<?php

return [
    // Twitch API Credentials
    'client_id' => env('TWITCH_CLIENT_ID'),
    'client_secret' => env('TWITCH_CLIENT_SECRET'),

    // API Configuration
    'base_url' => 'https://api.twitch.tv/helix',
    'auth_url' => 'https://id.twitch.tv/oauth2',
    'timeout' => env('TWITCH_TIMEOUT', 30),

    // Token Management
    'token_refresh_buffer' => env('TWITCH_TOKEN_REFRESH_BUFFER', 300),

    // Caching
    'cache_ttl' => env('TWITCH_CACHE_TTL', 3600),

    // Rate Limiting
    'rate_limiting' => [
        'enabled' => true,
        'requests' => env('TWITCH_RATE_LIMIT_REQUESTS', 100),
        'decay' => env('TWITCH_RATE_LIMIT_DECAY', 60),
    ],

    // Security
    'security' => [
        'require_https' => true,
        'allowed_domains' => ['twitch.tv', 'static-cdn.jtvnw.net'],
        'max_text_length' => 1000,
    ],

    // OAuth Configuration
    'oauth' => [
        'redirect_uri' => env('APP_URL') . '/auth/twitch/callback',
        'scopes' => [
            'user:read:email',
            'user:read:follows',
            'channel:read:stream_key',
        ],
    ],
];
```

## Service Provider Registration

The `TwitchServiceProvider` is automatically registered in `bootstrap/providers.php`:

```php
<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\TwitchServiceProvider::class, // Add this line
];
```

## Database Configuration

### Migration: create_users_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // Twitch OAuth Authentication Fields
            $table->string('twitch_id')->unique()->nullable();
            $table->string('twitch_login')->nullable();
            $table->string('twitch_display_name')->nullable();
            $table->string('twitch_email')->nullable();
            $table->text('twitch_access_token')->nullable();
            $table->text('twitch_refresh_token')->nullable();
            $table->timestamp('twitch_token_expires_at')->nullable();
            $table->json('scopes')->nullable();

            // Description
            $table->text('description')->nullable();

            // Avatar Management
            $table->string('twitch_avatar')->nullable();
            $table->string('custom_avatar_path')->nullable();
            $table->string('avatar_source')->nullable();
            $table->boolean('avatar_disabled')->default(false);
            $table->timestamp('avatar_disabled_at')->nullable();

            // User Roles and Permissions
            $table->boolean('is_viewer')->default(true);
            $table->boolean('is_cutter')->default(false);
            $table->boolean('is_streamer')->default(false);
            $table->boolean('is_moderator')->default(false);
            $table->boolean('is_admin')->default(false);

            // User Preferences
            $table->json('preferences')->nullable();

            // Activity Tracking
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamp('last_login_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
```

## Cache Configuration

Ensure your cache configuration supports the required driver:

```php
// config/cache.php
'default' => env('CACHE_DRIVER', 'file'),

// For production, use Redis:
'default' => env('CACHE_DRIVER', 'redis'),
```

## Session Configuration

```php
// config/session.php
'driver' => env('SESSION_DRIVER', 'file'),
'lifetime' => env('SESSION_LIFETIME', 120),
'encrypt' => true, // Important for token security
```

## Queue Configuration (Optional)

For background processing of heavy operations:

```php
// config/queue.php
'default' => env('QUEUE_CONNECTION', 'sync'),

// For production:
'default' => env('QUEUE_CONNECTION', 'redis'),
```

## Logging Configuration

```php
// config/logging.php
'channels' => [
    'twitch' => [
        'driver' => 'single',
        'path' => storage_path('logs/twitch.log'),
        'level' => env('LOG_LEVEL', 'debug'),
    ],
],
```

## Validation Rules

Custom validation rules for Twitch data:

```php
// In a service provider
Validator::extend('twitch_username', function ($attribute, $value, $parameters, $validator) {
    return preg_match('/^[a-zA-Z0-9_]{4,25}$/', $value);
});

Validator::extend('twitch_url', function ($attribute, $value, $parameters, $validator) {
    return filter_var($value, FILTER_VALIDATE_URL) &&
           str_contains(parse_url($value, PHP_URL_HOST), 'twitch.tv');
});
```

## Middleware Configuration

Add rate limiting middleware to routes:

```php
// In routes/web.php or routes/api.php
Route::middleware(['throttle:twitch-api'])->group(function () {
    Route::get('/twitch/user', [TwitchController::class, 'user']);
    Route::get('/twitch/streams', [TwitchController::class, 'streams']);
});
```

## Testing Configuration

For testing, create a separate configuration:

```php
// config/twitch.php (add to existing config)
'testing' => [
    'fake_api_responses' => env('TWITCH_FAKE_API', false),
    'mock_token' => env('TWITCH_MOCK_TOKEN', 'test_token'),
],
```

## Environment-Specific Settings

### Development
```env
TWITCH_CACHE_TTL=60
LOG_LEVEL=debug
```

### Production
```env
TWITCH_CACHE_TTL=3600
LOG_LEVEL=error
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

### Testing
```env
TWITCH_FAKE_API=true
CACHE_DRIVER=array
SESSION_DRIVER=array
```