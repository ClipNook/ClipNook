# Twitch API Services

This is the core of our Twitch integration. We built a clean abstraction layer over the Twitch Helix API with proper error handling, rate limiting, and caching.

## Architecture

- **Contracts**: Interfaces for all services (ClipsInterface, OAuthInterface, HttpClientInterface)
- **Implementations**: Concrete classes that implement the contracts
- **DTOs**: Data Transfer Objects for type safety
- **Exceptions**: Custom exceptions for different error scenarios

## Key Features

- Rate limiting per action (configurable)
- Automatic token refresh
- Proper error handling with custom exceptions
- Caching for expensive API calls
- Full type safety with readonly DTOs

## Usage Example

```php
// Get clips for a streamer
$clipsService = app(ClipsInterface::class);
$clipsService->setAccessToken($userToken);

$clips = $clipsService->getClips('123456789', 20);
foreach ($clips->data as $clip) {
    echo $clip->title . ' - ' . $clip->viewCount . ' views' . PHP_EOL;
}
```

The services are designed to be testable - you can mock the HttpClientInterface for unit tests.