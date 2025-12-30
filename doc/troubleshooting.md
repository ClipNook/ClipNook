# Troubleshooting Guide

## Overview

This guide helps diagnose and resolve common issues with the Twitch API integration. Follow the systematic approach for efficient problem resolution.

## Quick Diagnostics

### System Health Check

Run this command to check overall system health:

```bash
php artisan tinker --execute="
echo '=== System Health Check ===\n';
echo 'PHP Version: ' . PHP_VERSION . '\n';
echo 'Laravel Version: ' . app()->version() . '\n';
echo 'Database: ' . (DB::connection()->getPdo() ? 'Connected' : 'Failed') . '\n';
echo 'Cache: ' . (Cache::store()->getStore() ? 'Connected' : 'Failed') . '\n';
echo 'Redis: ' . (Redis::ping() ? 'Connected' : 'Failed') . '\n';
echo 'Twitch Config: ' . (config('twitch.client_id') ? 'Configured' : 'Missing') . '\n';
"
```

### Log Analysis

Check recent errors:

```bash
# Laravel logs
tail -f storage/logs/laravel.log

# PHP errors
tail -f /var/log/php8.5-fpm.log

# Web server errors
tail -f /var/log/nginx/error.log

# Search for specific errors
grep -r "ERROR" storage/logs/
grep -r "Exception" storage/logs/
```

## Authentication Issues

### "Invalid state parameter" Error

**Symptoms:**
- OAuth callback fails with state validation error
- Users can't complete Twitch login

**Causes:**
- CSRF token mismatch
- Session issues
- Incorrect redirect URI configuration

**Solutions:**

1. **Check CSRF Configuration:**
```php
// In config/session.php
'secure' => env('SESSION_SECURE_COOKIE', false),
'same_site' => 'lax',
```

2. **Verify Redirect URI:**
```php
// In Twitch Developer Console
// Redirect URI must match: https://yourdomain.com/auth/twitch/callback

// In config/twitch.php
'oauth' => [
    'redirect_uri' => env('APP_URL') . '/auth/twitch/callback',
],
```

3. **Clear Sessions:**
```bash
php artisan cache:clear
php artisan session:clear
```

### "Invalid OAuth access token" Error

**Symptoms:**
- API calls fail with authentication errors
- Token refresh not working

**Causes:**
- Expired tokens
- Invalid token storage
- Twitch API changes

**Solutions:**

1. **Check Token Storage:**
```php
php artisan tinker --execute="
\$user = App\Models\User::find(1);
echo 'Token exists: ' . (!empty(\$user->twitch_access_token)) . '\n';
echo 'Token expires: ' . \$user->twitch_token_expires_at . '\n';
echo 'Token valid: ' . (\$user->twitch_token_expires_at->isFuture()) . '\n';
"
```

2. **Manual Token Refresh:**
```php
php artisan tinker --execute="
\$user = App\Models\User::find(1);
\$service = app(App\Services\Twitch\TwitchService::class);
\$service->refreshUserToken(\$user);
echo 'Token refreshed\n';
"
```

3. **Validate Token:**
```php
php artisan tinker --execute="
\$user = App\Models\User::find(1);
\$service = app(App\Services\Twitch\TwitchService::class);
\$valid = \$service->validateAccessToken(decrypt(\$user->twitch_access_token));
echo 'Token valid: ' . (\$valid ? 'Yes' : 'No') . '\n';
"
```

## API Request Issues

### "Rate limit exceeded" Error

**Symptoms:**
- API calls return 429 status
- Requests are being throttled

**Causes:**
- Too many requests per minute
- Not respecting rate limits
- Shared IP address issues

**Solutions:**

1. **Check Rate Limiting Configuration:**
```php
// In config/twitch.php
'rate_limiting' => [
    'enabled' => true,
    'requests' => env('TWITCH_RATE_LIMIT_REQUESTS', 100),
    'decay' => env('TWITCH_RATE_LIMIT_DECAY', 60),
],
```

2. **Implement Exponential Backoff:**
```php
// In TwitchApiClient
private function handleRateLimit(Response $response): void
{
    if ($response->status() === 429) {
        $retryAfter = $response->header('Retry-After') ?? 60;
        sleep($retryAfter);
        // Retry the request
    }
}
```

3. **Monitor API Usage:**
```php
php artisan tinker --execute="
\$service = app(App\Services\Twitch\TwitchService::class);
\$start = now()->subMinute();
\$end = now();
\$requests = DB::table('twitch_api_logs')
    ->whereBetween('created_at', [\$start, \$end])
    ->count();
echo 'Requests in last minute: ' . \$requests . '\n';
"
```

### "Invalid API response" Error

**Symptoms:**
- API calls succeed but return unexpected data
- JSON parsing errors

**Causes:**
- API response format changes
- Network issues
- Encoding problems

**Solutions:**

1. **Check API Response:**
```php
php artisan tinker --execute="
\$client = app(App\Services\Twitch\TwitchApiClient::class);
try {
    \$response = \$client->get('users', ['id' => '123456789']);
    dump(\$response);
} catch (Exception \$e) {
    echo 'Error: ' . \$e->getMessage() . '\n';
}
"
```

2. **Validate Response Structure:**
```php
// In TwitchApiClient
private function validateResponse(array $data): void
{
    if (!isset($data['data'])) {
        throw new TwitchApiException('Invalid API response structure');
    }
}
```

3. **Handle API Changes:**
```php
// Check for API version changes
php artisan tinker --execute="
\$response = Http::withHeaders([
    'Client-ID' => config('twitch.client_id'),
    'Authorization' => 'Bearer ' . config('twitch.client_secret'),
])->get('https://api.twitch.tv/helix/users?id=44322889');
dump(\$response->json());
"
```

## Database Issues

### "Column not found" Error

**Symptoms:**
- Database queries fail
- Migration issues

**Causes:**
- Missing migrations
- Schema changes
- Cache issues

**Solutions:**

1. **Check Migration Status:**
```bash
php artisan migrate:status
```

2. **Run Missing Migrations:**
```bash
php artisan migrate
```

3. **Clear Database Cache:**
```bash
php artisan config:clear
php artisan cache:clear
php artisan db:monitor
```

### "Connection refused" Error

**Symptoms:**
- Database connection fails
- Application won't start

**Causes:**
- Database server down
- Incorrect credentials
- Network issues

**Solutions:**

1. **Test Database Connection:**
```bash
php artisan tinker --execute="
try {
    DB::connection()->getPdo();
    echo 'Database connection successful\n';
} catch (Exception \$e) {
    echo 'Database connection failed: ' . \$e->getMessage() . '\n';
}
"
```

2. **Check Database Configuration:**
```php
// In config/database.php
'default' => env('DB_CONNECTION', 'mysql'),
'connections' => [
    'mysql' => [
        'host' => env('DB_HOST', '127.0.0.1'),
        'port' => env('DB_PORT', '3306'),
        'database' => env('DB_DATABASE', 'laravel'),
        'username' => env('DB_USERNAME', 'root'),
        'password' => env('DB_PASSWORD', ''),
    ],
],
```

3. **Restart Database Service:**
```bash
# MySQL
sudo systemctl restart mysql

# PostgreSQL
sudo systemctl restart postgresql
```

## Cache Issues

### "Cache connection failed" Error

**Symptoms:**
- Cache operations fail
- Performance degradation

**Causes:**
- Redis server down
- Incorrect configuration
- Memory issues

**Solutions:**

1. **Test Cache Connection:**
```bash
php artisan tinker --execute="
try {
    Cache::put('test', 'value', 10);
    \$value = Cache::get('test');
    echo 'Cache working: ' . (\$value === 'value' ? 'Yes' : 'No') . '\n';
} catch (Exception \$e) {
    echo 'Cache error: ' . \$e->getMessage() . '\n';
}
"
```

2. **Check Redis Status:**
```bash
redis-cli ping
redis-cli info
```

3. **Clear Cache:**
```bash
php artisan cache:clear
php artisan config:cache
```

## Performance Issues

### Slow API Responses

**Symptoms:**
- API calls take too long
- High response times

**Causes:**
- Network latency
- Database queries
- Cache misses
- Rate limiting

**Solutions:**

1. **Profile API Calls:**
```php
php artisan tinker --execute="
\$start = microtime(true);
\$service = app(App\Services\Twitch\TwitchService::class);
\$user = \$service->getUserById('44322889');
\$end = microtime(true);
echo 'API call took: ' . round((\$end - \$start) * 1000, 2) . 'ms\n';
"
```

2. **Check Database Performance:**
```sql
-- Slow query log
SHOW PROCESSLIST;
SHOW ENGINE INNODB STATUS;

-- Query optimization
EXPLAIN SELECT * FROM users WHERE twitch_id = '44322889';
```

3. **Optimize Cache Usage:**
```php
// Check cache hit rate
php artisan tinker --execute="
\$stats = Cache::store()->getStats();
dump(\$stats);
"
```

### High Memory Usage

**Symptoms:**
- Memory exhaustion errors
- Application crashes

**Causes:**
- Memory leaks
- Large data sets
- Inefficient queries

**Solutions:**

1. **Monitor Memory Usage:**
```php
php artisan tinker --execute="
echo 'Memory usage: ' . round(memory_get_usage() / 1024 / 1024, 2) . 'MB\n';
echo 'Memory peak: ' . round(memory_get_peak_usage() / 1024 / 1024, 2) . 'MB\n';
"
```

2. **Optimize Queries:**
```php
// Use chunking for large datasets
User::chunk(100, function ($users) {
    foreach ($users as $user) {
        // Process user
    }
});
```

3. **Enable OPcache:**
```ini
; In php.ini
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=7963
opcache.revalidate_freq=0
```

## Security Issues

### XSS Vulnerabilities

**Symptoms:**
- Malicious script execution
- Data sanitization issues

**Causes:**
- Unsanitized user input
- Missing output escaping

**Solutions:**

1. **Check Data Sanitization:**
```php
php artisan tinker --execute="
\$sanitizer = app(App\Services\Twitch\TwitchDataSanitizer::class);
\$malicious = '<script>alert(\"xss\")</script>';
\$sanitized = \$sanitizer->sanitizeText(\$malicious);
echo 'Original: ' . \$malicious . '\n';
echo 'Sanitized: ' . \$sanitized . '\n';
"
```

2. **Audit Blade Templates:**
```blade
{{-- Always escape output --}}
{{ $user->description }} {{-- Auto-escaped --}}
{!! $user->description !!} {{-- Dangerous, avoid --}}
```

3. **Update Content Security Policy:**
```php
// In config/twitch.php
'security' => [
    'content_security_policy' => "default-src 'self'; script-src 'self' 'unsafe-inline'",
],
```

### Token Exposure

**Symptoms:**
- Tokens leaked in logs
- Unauthorized access

**Causes:**
- Improper token storage
- Logging sensitive data

**Solutions:**

1. **Check Token Encryption:**
```php
php artisan tinker --execute="
\$user = App\Models\User::find(1);
\$token = decrypt(\$user->twitch_access_token);
echo 'Token length: ' . strlen(\$token) . '\n';
echo 'Token starts with: ' . substr(\$token, 0, 10) . '...\n';
"
```

2. **Audit Log Files:**
```bash
# Search for tokens in logs
grep -r "access_token" storage/logs/
grep -r "Bearer" storage/logs/
```

3. **Update Hidden Attributes:**
```php
// In User model
protected $hidden = [
    'twitch_access_token',
    'twitch_refresh_token',
    'password',
];
```

## Testing Issues

### Tests Failing

**Symptoms:**
- Unit tests fail
- Feature tests fail

**Causes:**
- Mock data issues
- Environment differences
- Dependency problems

**Solutions:**

1. **Run Tests with Debug:**
```bash
php artisan test --verbose
php artisan test --debug
```

2. **Check Test Configuration:**
```php
// In phpunit.xml
<env name="APP_ENV" value="testing"/>
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

3. **Fix Mock Data:**
```php
// Update test with correct API response structure
Http::fake([
    'https://api.twitch.tv/helix/users' => Http::response([
        'data' => [
            [
                'id' => '123456789',
                'login' => 'testuser',
                'display_name' => 'Test User',
                // Include all required fields
            ]
        ]
    ]),
]);
```

## Common Error Codes

### HTTP Status Codes

- **400 Bad Request**: Invalid request parameters
- **401 Unauthorized**: Invalid or missing authentication
- **403 Forbidden**: Insufficient permissions
- **404 Not Found**: Resource doesn't exist
- **429 Too Many Requests**: Rate limit exceeded
- **500 Internal Server Error**: Server-side error
- **502 Bad Gateway**: Upstream server error
- **503 Service Unavailable**: Service temporarily down

### Twitch API Error Codes

- **invalid_token**: Token is invalid or expired
- **missing_scope**: Required scope not granted
- **invalid_request**: Malformed request
- **server_error**: Twitch API internal error

## Debug Tools

### Laravel Debugbar

```bash
composer require barryvdh/laravel-debugbar --dev
```

### Laravel Telescope

```bash
php artisan telescope:install
php artisan migrate
```

### Custom Debug Commands

```php
// In app/Console/Commands/DebugTwitchCommand.php
<?php

namespace App\Console\Commands;

use App\Services\Twitch\TwitchService;
use Illuminate\Console\Command;

class DebugTwitchCommand extends Command
{
    protected $signature = 'twitch:debug {user_id?}';

    public function handle()
    {
        $userId = $this->argument('user_id') ?? '44322889';

        $service = app(TwitchService::class);

        try {
            $user = $service->getUserById($userId);
            $this->info('User data retrieved successfully');
            dump($user);
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }
}
```

## Emergency Procedures

### Application Down

1. **Check Services:**
```bash
sudo systemctl status nginx
sudo systemctl status php8.5-fpm
sudo systemctl status mysql
sudo systemctl status redis
```

2. **Restart Services:**
```bash
sudo systemctl restart nginx
sudo systemctl restart php8.5-fpm
sudo systemctl restart mysql
sudo systemctl restart redis
```

3. **Enable Maintenance Mode:**
```bash
php artisan down
```

4. **Clear All Caches:**
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Data Recovery

1. **Database Backup:**
```bash
mysqldump -u username -p database_name > backup.sql
```

2. **File Backup:**
```bash
tar -czf backup.tar.gz /var/www/app
```

3. **Restore from Backup:**
```bash
mysql -u username -p database_name < backup.sql
```

## Prevention

### Monitoring Setup

```bash
# Install monitoring
composer require laravel/pulse

# Health check endpoint
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'checks' => [
            'database' => DB::connection()->getPdo() ? 'ok' : 'error',
            'cache' => Cache::store()->getStore() ? 'ok' : 'error',
            'twitch_api' => $this->checkTwitchApi(),
        ],
    ]);
});
```

### Automated Testing

```bash
# Add to CI/CD pipeline
php artisan test
php artisan dusk (if using browser tests)
```

### Log Rotation

```bash
# Configure logrotate
/var/log/nginx/*.log {
    daily
    rotate 30
    compress
    missingok
    notifempty
}
```

### Regular Maintenance

```bash
# Weekly maintenance script
#!/bin/bash
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate:status
```

This troubleshooting guide should help resolve most common issues. For complex problems, check the Laravel and Twitch API documentation, and consider reaching out to the community or support channels.