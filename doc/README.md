# Twitch API Integration Docs

This directory contains documentation for our Twitch API services.

## Overview

We built a comprehensive Twitch integration with:
- Clean abstractions over Twitch Helix API
- Proper error handling and rate limiting
- Type-safe DTOs
- Automatic token management
- Privacy-conscious avatar handling

## Files

- [twitch-services-overview.md](twitch-services-overview.md) - High-level architecture
- [clips-service.md](clips-service.md) - Clip fetching and creation
- [oauth-service.md](oauth-service.md) - Authentication flow
- [http-client.md](http-client.md) - HTTP abstraction layer
- [dtos.md](dtos.md) - Data transfer objects
- [exceptions.md](exceptions.md) - Error handling
- [token-refresh.md](token-refresh.md) - Automatic token refresh
- [avatar-service.md](avatar-service.md) - User avatar management

## Key Design Decisions

1. **Contracts First**: All services implement interfaces for testability
2. **DTOs Everywhere**: Type safety over raw arrays
3. **Custom Exceptions**: Specific error types for different scenarios
4. **Rate Limiting**: Per-action limits to respect Twitch's quotas
5. **Privacy**: User control over avatars and data storage

## Testing

All services are designed for easy testing:

```php
$mockClient = Mockery::mock(HttpClientInterface::class);
$service = new ClipsService($mockClient, $config);
```

## Configuration

Rate limits and other settings in `config/services.php`:

```php
'twitch' => [
    'rate_limit' => [
        'actions' => [
            'get_clips' => ['max' => 60, 'decay' => 60],
            // ...
        ]
    ]
]
```

## Contributing

When adding new API endpoints:
1. Add method to the appropriate interface
2. Implement in the service class
3. Create/update DTOs as needed
4. Add rate limiting
5. Write tests
6. Update this documentation