# Testing Guide

## Overview

The Twitch integration includes comprehensive testing coverage using Pest and PHPUnit. This guide covers unit tests, feature tests, and testing best practices.

## Test Structure

```
tests/
├── Feature/
│   ├── TwitchOAuthTest.php
│   └── TwitchApiTest.php
├── Unit/
│   ├── Services/
│   │   └── TwitchServiceTest.php
│   ├── Actions/
│   │   └── AuthenticateTwitchUserActionTest.php
│   └── TwitchDataSanitizerTest.php
└── Pest.php
```

## Unit Tests

### TwitchServiceTest

```php
<?php

use App\Services\Twitch\TwitchService;
use Tests\TestCase;

class TwitchServiceTest extends TestCase
{
    private TwitchService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(TwitchService::class);
    }

    public function test_extract_user_id_from_valid_twitch_url()
    {
        $url = 'https://twitch.tv/username';
        $result = $this->service->extractUserIdFromUrl($url);

        expect($result)->toBeString();
    }

    public function test_extract_user_id_from_invalid_url_returns_null()
    {
        $url = 'https://invalid-url.com';
        $result = $this->service->extractUserIdFromUrl($url);

        expect($result)->toBeNull();
    }

    public function test_extract_user_id_from_username_only()
    {
        $username = 'testuser';
        $result = $this->service->extractUserIdFromUrl($username);

        expect($result)->toBeString();
    }

    public function test_extract_user_id_handles_edge_cases()
    {
        $testCases = [
            'https://twitch.tv/user-name_123' => true,
            'twitch.tv/user' => true,
            'invalid-url' => false,
            '' => false,
            null => false,
        ];

        foreach ($testCases as $input => $shouldWork) {
            $result = $this->service->extractUserIdFromUrl($input);

            if ($shouldWork) {
                expect($result)->toBeString();
            } else {
                expect($result)->toBeNull();
            }
        }
    }
}
```

### TwitchDataSanitizerTest

```php
<?php

use App\Services\Twitch\TwitchDataSanitizer;
use Tests\TestCase;

class TwitchDataSanitizerTest extends TestCase
{
    private TwitchDataSanitizer $sanitizer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sanitizer = app(TwitchDataSanitizer::class);
    }

    public function test_sanitizes_xss_in_text()
    {
        $malicious = '<script>alert("xss")</script>Hello World';
        $sanitized = $this->sanitizer->sanitizeText($malicious);

        expect($sanitized)->not->toContain('<script>');
        expect($sanitized)->toContain('Hello World');
    }

    public function test_sanitizes_malicious_urls()
    {
        $maliciousUrls = [
            'javascript:alert("xss")',
            'data:text/html,<script>alert("xss")</script>',
            'vbscript:msgbox("xss")',
        ];

        foreach ($maliciousUrls as $url) {
            $sanitized = $this->sanitizer->sanitizeUrl($url);
            expect($sanitized)->toBe('');
        }
    }

    public function test_allows_safe_urls()
    {
        $safeUrls = [
            'https://twitch.tv/username',
            'https://static-cdn.jtvnw.net/previews-ttv/live_user_username-1920x1080.jpg',
        ];

        foreach ($safeUrls as $url) {
            $sanitized = $this->sanitizer->sanitizeUrl($url);
            expect($sanitized)->toBe($url);
        }
    }

    public function test_sanitizes_array_data()
    {
        $data = [
            'description' => '<script>alert("xss")</script>Safe text',
            'url' => 'javascript:alert("xss")',
            'safe_field' => 'This is safe',
        ];

        $sanitized = $this->sanitizer->sanitizeData($data);

        expect($sanitized['description'])->not->toContain('<script>');
        expect($sanitized['url'])->toBe('');
        expect($sanitized['safe_field'])->toBe('This is safe');
    }
}
```

### AuthenticateTwitchUserActionTest

```php
<?php

use App\Actions\Twitch\AuthenticateTwitchUserAction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AuthenticateTwitchUserActionTest extends TestCase
{
    use RefreshDatabase;

    private AuthenticateTwitchUserAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = app(AuthenticateTwitchUserAction::class);
    }

    public function test_creates_new_user_on_successful_authentication()
    {
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
                        'description' => 'Test description',
                        'profile_image_url' => 'https://example.com/avatar.jpg',
                    ]
                ]
            ]),
        ]);

        $user = $this->action->execute('test_code', csrf_token());

        expect($user)->toBeInstanceOf(User::class);
        expect($user->twitch_id)->toBe('123456789');
        expect($user->twitch_login)->toBe('testuser');
        expect($user->twitch_display_name)->toBe('Test User');
        expect($user->twitch_email)->toBe('test@example.com');
        expect($user->description)->toBe('Test description');
        expect($user->twitch_avatar)->toBe('https://example.com/avatar.jpg');
        expect($user->avatar_source)->toBe('twitch');
    }

    public function test_updates_existing_user()
    {
        User::factory()->create([
            'twitch_id' => '123456789',
            'twitch_display_name' => 'Old Name',
        ]);

        Http::fake([
            'https://id.twitch.tv/oauth2/token' => Http::response([
                'access_token' => 'test_access_token',
                'refresh_token' => 'test_refresh_token',
                'expires_in' => 3600,
            ]),
            'https://api.twitch.tv/helix/users' => Http::response([
                'data' => [
                    [
                        'id' => '123456789',
                        'login' => 'testuser',
                        'display_name' => 'Updated Name',
                        'email' => 'updated@example.com',
                    ]
                ]
            ]),
        ]);

        $user = $this->action->execute('test_code', csrf_token());

        expect($user->twitch_display_name)->toBe('Updated Name');
        expect($user->twitch_email)->toBe('updated@example.com');
        $this->assertDatabaseCount('users', 1);
    }

    public function test_throws_exception_on_invalid_state()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid state parameter');

        $this->action->execute('test_code', 'invalid_state');
    }

    public function test_handles_api_errors_gracefully()
    {
        Http::fake([
            'https://id.twitch.tv/oauth2/token' => Http::response([], 400),
        ]);

        $this->expectException(\Exception::class);
        $this->action->execute('invalid_code', csrf_token());
    }
}
```

## Feature Tests

### TwitchOAuthTest

```php
<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TwitchOAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_twitch_oauth_redirect()
    {
        $response = $this->get('/auth/twitch');

        $response->assertRedirect();
        $response->assertRedirectContains('https://id.twitch.tv/oauth2/authorize');
        $response->assertRedirectContains('client_id=' . config('twitch.client_id'));
        $response->assertRedirectContains('redirect_uri=' . urlencode(config('twitch.oauth.redirect_uri')));
        $response->assertRedirectContains('scope=' . urlencode(implode(' ', config('twitch.oauth.scopes'))));
        $response->assertRedirectContains('response_type=code');
        $response->assertRedirectContains('state=' . csrf_token());
    }

    public function test_successful_oauth_callback()
    {
        Http::fake([
            'https://id.twitch.tv/oauth2/token' => Http::response([
                'access_token' => 'test_access_token',
                'refresh_token' => 'test_refresh_token',
                'expires_in' => 3600,
                'scope' => ['user:read:email'],
            ]),
            'https://api.twitch.tv/oauth2/validate' => Http::response([
                'client_id' => config('twitch.client_id'),
                'login' => 'testuser',
                'scopes' => ['user:read:email'],
                'user_id' => '123456789',
            ]),
            'https://api.twitch.tv/helix/users' => Http::response([
                'data' => [
                    [
                        'id' => '123456789',
                        'login' => 'testuser',
                        'display_name' => 'Test User',
                        'type' => '',
                        'broadcaster_type' => 'partner',
                        'description' => 'Test streamer description',
                        'profile_image_url' => 'https://static-cdn.jtvnw.net/jtv_user_pictures/test-profile_image-300x300.png',
                        'offline_image_url' => 'https://static-cdn.jtvnw.net/jtv_user_pictures/test-offline_image-1920x1080.png',
                        'view_count' => 15000,
                        'created_at' => '2016-05-20T20:10:44Z',
                    ]
                ]
            ]),
        ]);

        $response = $this->get('/auth/twitch/callback?code=test_code&state=' . csrf_token());

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'twitch_id' => '123456789',
            'twitch_login' => 'testuser',
            'twitch_display_name' => 'Test User',
            'twitch_email' => null, // Not provided in mock
            'description' => 'Test streamer description',
            'twitch_avatar' => 'https://static-cdn.jtvnw.net/jtv_user_pictures/test-profile_image-300x300.png',
            'avatar_source' => 'twitch',
        ]);

        $this->assertAuthenticated();
    }

    public function test_oauth_callback_with_invalid_state()
    {
        $response = $this->get('/auth/twitch/callback?code=test_code&state=invalid_state');

        $response->assertRedirect('/login');
        $response->assertSessionHas('error', 'Authentication failed: Invalid state parameter');
        $this->assertGuest();
    }

    public function test_oauth_callback_with_missing_code()
    {
        $response = $this->get('/auth/twitch/callback?state=' . csrf_token());

        $response->assertRedirect('/login');
        $response->assertSessionHas('error');
        $this->assertGuest();
    }

    public function test_oauth_callback_with_api_error()
    {
        Http::fake([
            'https://id.twitch.tv/oauth2/token' => Http::response(['message' => 'Invalid code'], 400),
        ]);

        $response = $this->get('/auth/twitch/callback?code=invalid_code&state=' . csrf_token());

        $response->assertRedirect('/login');
        $response->assertSessionHas('error');
        $this->assertGuest();
    }

    public function test_oauth_callback_updates_existing_user()
    {
        $existingUser = User::factory()->create([
            'twitch_id' => '123456789',
            'twitch_display_name' => 'Old Name',
            'last_login_at' => now()->subDays(1),
        ]);

        Http::fake([
            'https://id.twitch.tv/oauth2/token' => Http::response([
                'access_token' => 'test_access_token',
                'refresh_token' => 'test_refresh_token',
                'expires_in' => 3600,
            ]),
            'https://api.twitch.tv/helix/users' => Http::response([
                'data' => [
                    [
                        'id' => '123456789',
                        'login' => 'testuser',
                        'display_name' => 'Updated Name',
                        'email' => 'updated@example.com',
                    ]
                ]
            ]),
        ]);

        $response = $this->get('/auth/twitch/callback?code=test_code&state=' . csrf_token());

        $response->assertRedirect('/dashboard');

        $existingUser->refresh();
        expect($existingUser->twitch_display_name)->toBe('Updated Name');
        expect($existingUser->twitch_email)->toBe('updated@example.com');
        expect($existingUser->last_login_at)->toBeInstanceOf(\Carbon\Carbon::class);
        expect($existingUser->last_login_at->isToday())->toBeTrue();

        $this->assertDatabaseCount('users', 1);
    }
}
```

### TwitchApiTest

```php
<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TwitchApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'twitch_id' => '123456789',
            'twitch_access_token' => encrypt('test_access_token'),
            'twitch_token_expires_at' => now()->addHour(),
        ]);
    }

    public function test_get_user_profile()
    {
        $this->actingAs($this->user);

        Http::fake([
            'https://api.twitch.tv/helix/users?id=123456789' => Http::response([
                'data' => [
                    [
                        'id' => '123456789',
                        'login' => 'testuser',
                        'display_name' => 'Test User',
                        'type' => '',
                        'broadcaster_type' => 'partner',
                        'description' => 'Test description',
                        'profile_image_url' => 'https://example.com/avatar.jpg',
                        'offline_image_url' => 'https://example.com/offline.jpg',
                        'view_count' => 15000,
                        'created_at' => '2016-05-20T20:10:44Z',
                    ]
                ]
            ]),
        ]);

        $response = $this->get('/api/twitch/user');

        $response->assertOk();
        $response->assertJsonStructure([
            'id',
            'login',
            'display_name',
            'description',
            'profile_image_url',
            'view_count',
        ]);
    }

    public function test_get_user_stream()
    {
        $this->actingAs($this->user);

        Http::fake([
            'https://api.twitch.tv/helix/streams?user_id=123456789' => Http::response([
                'data' => [
                    [
                        'id' => '41375541868',
                        'user_id' => '123456789',
                        'user_login' => 'testuser',
                        'user_name' => 'Test User',
                        'game_id' => '33214',
                        'game_name' => 'Fortnite',
                        'type' => 'live',
                        'title' => 'Best Stream Ever!',
                        'viewer_count' => 1500,
                        'started_at' => '2023-12-01T10:00:00Z',
                        'language' => 'en',
                        'thumbnail_url' => 'https://static-cdn.jtvnw.net/previews-ttv/live_user_testuser-{width}x{height}.jpg',
                        'tag_ids' => ['6ea6bca4-4712-4ab9-a906-e3336a9d8039'],
                        'tags' => ['English', 'Fortnite'],
                        'is_mature' => false,
                    ]
                ]
            ]),
        ]);

        $response = $this->get('/api/twitch/stream');

        $response->assertOk();
        $response->assertJson([
            'id' => '41375541868',
            'user_id' => '123456789',
            'user_name' => 'Test User',
            'game_name' => 'Fortnite',
            'type' => 'live',
            'title' => 'Best Stream Ever!',
            'viewer_count' => 1500,
        ]);
    }

    public function test_get_user_followers()
    {
        $this->actingAs($this->user);

        Http::fake([
            'https://api.twitch.tv/helix/users/follows?to_id=123456789' => Http::response([
                'total' => 15000,
                'data' => [
                    [
                        'from_id' => '987654321',
                        'from_login' => 'follower1',
                        'from_name' => 'Follower One',
                        'to_id' => '123456789',
                        'to_login' => 'testuser',
                        'to_name' => 'Test User',
                        'followed_at' => '2023-01-01T00:00:00Z',
                    ]
                ],
                'pagination' => [
                    'cursor' => 'eyJiIjpudWxsLCJhIjp7Ik9mZnNldCI6MX19',
                ]
            ]),
        ]);

        $response = $this->get('/api/twitch/followers');

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'from_id',
                    'from_login',
                    'from_name',
                    'followed_at',
                ]
            ],
            'total',
            'pagination',
        ]);
    }

    public function test_api_returns_401_when_token_expired()
    {
        $this->user->update([
            'twitch_token_expires_at' => now()->subHour(),
        ]);

        $this->actingAs($this->user);

        $response = $this->get('/api/twitch/user');

        $response->assertUnauthorized();
    }

    public function test_api_handles_rate_limiting()
    {
        $this->actingAs($this->user);

        Http::fake([
            'https://api.twitch.tv/helix/users?id=123456789' => Http::response([], 429),
        ]);

        $response = $this->get('/api/twitch/user');

        $response->assertStatus(429);
    }
}
```

## Test Configuration

### Pest.php

```php
<?php

uses(Tests\TestCase::class)->in('Feature', 'Unit');

expect()->extend('toBeValidTwitchUserId', function () {
    return $this->toMatch('/^\d+$/');
});

expect()->extend('toBeValidTwitchUsername', function () {
    return $this->toMatch('/^[a-zA-Z0-9_]{4,25}$/');
});

function mockTwitchApiResponse(array $response = [], int $status = 200)
{
    return \Illuminate\Support\Facades\Http::response($response, $status);
}

function createAuthenticatedUser(array $attributes = []): \App\Models\User
{
    return \App\Models\User::factory()->create(array_merge([
        'twitch_id' => '123456789',
        'twitch_access_token' => encrypt('test_token'),
        'twitch_token_expires_at' => now()->addHour(),
    ], $attributes));
}
```

### Test Database

```php
<?php

// In phpunit.xml or pest.php
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

## Running Tests

### All Tests

```bash
# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage

# Run specific test file
php artisan test tests/Unit/TwitchServiceTest.php

# Run specific test method
php artisan test --filter=test_extract_user_id_from_valid_twitch_url
```

### Pest Commands

```bash
# Run tests in parallel
php artisan test --parallel

# Run tests with different output
php artisan test --verbose

# Generate test coverage report
php artisan test --coverage-html=reports/coverage
```

## Mocking Strategy

### HTTP Client Mocking

```php
<?php

Http::fake([
    'https://api.twitch.tv/helix/users*' => Http::response([
        'data' => [
            [
                'id' => '123456789',
                'login' => 'testuser',
                'display_name' => 'Test User',
            ]
        ]
    ]),
    'https://id.twitch.tv/oauth2/token' => Http::response([
        'access_token' => 'test_token',
        'refresh_token' => 'refresh_token',
        'expires_in' => 3600,
    ]),
]);
```

### Service Mocking

```php
<?php

$this->mock(\App\Services\Twitch\TwitchService::class, function ($mock) {
    $mock->shouldReceive('getUserById')
         ->andReturn(['id' => '123', 'login' => 'test']);
});
```

## Test Data Factories

### UserFactory

```php
<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'twitch_id' => $this->faker->unique()->numberBetween(100000000, 999999999),
            'twitch_login' => $this->faker->unique()->userName(),
            'twitch_display_name' => $this->faker->name(),
            'twitch_email' => $this->faker->unique()->safeEmail(),
            'twitch_access_token' => encrypt('test_access_token'),
            'twitch_refresh_token' => encrypt('test_refresh_token'),
            'twitch_token_expires_at' => now()->addHour(),
            'scopes' => ['user:read:email'],
            'description' => $this->faker->sentence(),
            'twitch_avatar' => $this->faker->imageUrl(300, 300, 'people'),
            'avatar_source' => 'twitch',
            'is_viewer' => true,
            'is_streamer' => $this->faker->boolean(20), // 20% chance
            'is_moderator' => false,
            'is_admin' => false,
            'last_activity_at' => now(),
            'last_login_at' => now(),
        ];
    }

    public function streamer(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_streamer' => true,
            'is_viewer' => false,
        ]);
    }

    public function moderator(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_moderator' => true,
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_admin' => true,
        ]);
    }

    public function expiredToken(): static
    {
        return $this->state(fn (array $attributes) => [
            'twitch_token_expires_at' => now()->subHour(),
        ]);
    }
}
```

## Continuous Integration

### GitHub Actions

```yaml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest

    services:
      redis:
        image: redis
        ports:
          - 6379:6379
        options: >-
          --health-cmd "redis-cli ping"
          --health-interval 10s
          --health-timeout 5s
          --health-retries 5

    steps:
      - uses: actions/checkout@v3

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.5'
          extensions: pdo, pdo_sqlite

      - name: Install dependencies
        run: composer install --no-progress --prefer-dist --optimize-autoloader

      - name: Copy environment file
        run: cp .env.ci .env

      - name: Generate application key
        run: php artisan key:generate

      - name: Run migrations
        run: php artisan migrate --force

      - name: Run tests
        run: php artisan test --coverage
```

## Test Best Practices

1. **Test Isolation**: Each test should be independent
2. **Mock External APIs**: Never call real APIs in tests
3. **Use Factories**: Create test data with factories
4. **Test Edge Cases**: Cover error conditions and edge cases
5. **Descriptive Names**: Use clear, descriptive test method names
6. **Arrange-Act-Assert**: Structure tests clearly
7. **Test Public APIs**: Focus on testing public methods
8. **Fast Tests**: Keep tests fast and reliable
9. **Continuous Testing**: Run tests on every change
10. **Code Coverage**: Aim for high coverage of critical paths

## Debugging Tests

### Verbose Output

```bash
php artisan test --verbose
```

### Debug Specific Test

```php
<?php

public function test_debugging_example()
{
    $data = ['key' => 'value'];
    dump($data); // Debug output

    expect($data)->toHaveKey('key');
}
```

### Test Database Inspection

```php
<?php

public function test_database_state()
{
    // Create test data
    $user = User::factory()->create();

    // Inspect database
    dump(User::all()->toArray());

    expect($user->twitch_id)->toBeString();
}
```

## Performance Testing

### Load Testing

```php
<?php

public function test_api_performance_under_load()
{
    $users = User::factory()->count(100)->create();

    $startTime = microtime(true);

    foreach ($users as $user) {
        $this->actingAs($user);
        $response = $this->get('/api/twitch/user');
        $response->assertOk();
    }

    $endTime = microtime(true);
    $totalTime = $endTime - $startTime;

    expect($totalTime)->toBeLessThan(10.0); // Should complete within 10 seconds
}
```

### Memory Usage Testing

```php
<?php

public function test_memory_usage()
{
    $initialMemory = memory_get_usage();

    // Perform operations
    for ($i = 0; $i < 1000; $i++) {
        $user = app(\App\Services\Twitch\TwitchService::class)->getUserById('123');
    }

    $finalMemory = memory_get_usage();
    $memoryUsed = $finalMemory - $initialMemory;

    expect($memoryUsed)->toBeLessThan(50 * 1024 * 1024); // Less than 50MB
}
```