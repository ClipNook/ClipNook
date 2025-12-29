# HTTP Client

Our abstraction over HTTP requests. We built this because Guzzle is heavy and we wanted something simpler for testing.

## Why not Guzzle?

Guzzle is great but:
- Heavy dependency for what we need
- Hard to mock in tests
- Too many features we don't use

## Interface

Simple and clean:

```php
interface HttpClientInterface {
    public function get(string $url, array $params = [], array $headers = []): array;
    public function post(string $url, array $data = [], array $headers = []): array;
    public function delete(string $url, array $headers = []): array;
}
```

## Implementation

We have a CurlHttpClient that:
- Uses curl_multi for concurrent requests (if needed later)
- Handles rate limiting
- Parses JSON responses
- Throws appropriate exceptions

## Rate Limiting

Built-in rate limiting using Laravel's RateLimiter:

```php
// In ClipsService
if (RateLimiter::tooManyAttempts($this->getRateLimitKey('get_clips'), 60)) {
    throw new RateLimitException('Rate limit exceeded');
}
```

## Error Handling

Maps HTTP status codes to our custom exceptions:
- 401 → AuthenticationException
- 403 → ForbiddenException
- 404 → NotFoundException
- 429 → RateLimitException
- 5xx → ServerException

## Testing

Easy to mock:

```php
$mockClient = Mockery::mock(HttpClientInterface::class);
$mockClient->shouldReceive('get')
    ->with('https://api.twitch.tv/helix/clips', Mockery::any(), Mockery::any())
    ->andReturn(['data' => [['id' => 'test-clip']]]);

$clipsService = new ClipsService($mockClient, $config);
```