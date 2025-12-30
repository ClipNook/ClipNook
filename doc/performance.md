# Performance Monitoring & Optimization

ClipNook includes comprehensive performance monitoring and optimization features to ensure optimal application performance and user experience.

## Performance Monitoring

### Slow Query Detection

Automatically detects and logs slow database queries:

```env
PERFORMANCE_SLOW_QUERY_THRESHOLD=1000  # milliseconds
PERFORMANCE_METRICS_RETENTION_HOURS=24
PERFORMANCE_MAX_ENTRIES=1000
PERFORMANCE_STORAGE_PATH=storage/logs/performance
```

**Features:**
- Configurable slow query threshold
- Automatic query execution time tracking
- Metrics aggregation and reporting
- Storage cleanup to prevent bloat

### Usage

```php
use App\Services\Monitoring\PerformanceMonitor;

$monitor = new PerformanceMonitor();

// Record query execution
$monitor->recordQuery($query, $executionTime);

// Record custom metrics
$monitor->recordMetric('response_time', $responseTime);
$monitor->recordMetric('memory_usage', memory_get_peak_usage(true));

// Get performance insights
$insights = $monitor->getInsights();
```

## Response Caching

### API Response Caching

Configurable caching for API responses to improve performance:

```env
RESPONSE_CACHE_DEFAULT_TTL=300  # seconds
RESPONSE_CACHE_MAX_CACHE_SIZE=100  # MB
RESPONSE_CACHE_EXCLUDE_PATTERNS=admin,api/auth
```

**Features:**
- Automatic response caching
- Configurable TTL per endpoint
- Cache invalidation on data changes
- Size limits and cleanup

### CacheResponse Middleware

```php
// In routes/api.php
Route::middleware(['auth:sanctum', 'cache.response'])->group(function () {
    Route::get('/clips', [ClipController::class, 'index']);
    Route::get('/clips/{id}', [ClipController::class, 'show']);
});
```

## Database Optimization

### Query Optimization

Built-in query optimization features:

- **Eager Loading**: Prevents N+1 query problems
- **Query Caching**: Caches frequently accessed data
- **Index Optimization**: Proper database indexing

### Example: Optimized Queries

```php
// Bad: N+1 queries
$clips = Clip::all();
foreach ($clips as $clip) {
    echo $clip->user->name; // Separate query for each clip
}

// Good: Eager loading
$clips = Clip::with('user')->get();
foreach ($clips as $clip) {
    echo $clip->user->name; // No additional queries
}
```

## Caching Strategies

### Multiple Cache Layers

1. **Response Caching**: API response caching
2. **Query Result Caching**: Database query result caching
3. **Object Caching**: Model instance caching
4. **Fragment Caching**: View fragment caching

### Cache Configuration

```php
// config/cache.php
'stores' => [
    'redis' => [
        'driver' => 'redis',
        'connection' => 'cache',
    ],
    'database' => [
        'driver' => 'database',
        'table' => 'cache',
    ],
],
```

## Rate Limiting Optimization

### Efficient Rate Limiting

The advanced rate limiter is optimized for performance:

- **Redis Backend**: High-performance storage
- **Sliding Window**: Accurate rate limiting
- **Memory Efficient**: Automatic cleanup
- **Configurable Limits**: Per-route customization

### Rate Limiting Middleware

```php
// In routes/api.php
Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    Route::post('/clips', [ClipController::class, 'store']);
});
```

## Asset Optimization

### Frontend Asset Optimization

- **Vite Build System**: Fast development and optimized production builds
- **Tailwind CSS**: Utility-first CSS framework
- **Asset Versioning**: Cache busting for production

### Build Commands

```bash
# Development build with HMR
npm run dev

# Production build with optimization
npm run build

# Build analysis
npm run build -- --mode analyze
```

## Monitoring & Metrics

### Performance Metrics

Track and monitor application performance:

```php
// Record custom metrics
$monitor->recordMetric('api_response_time', $responseTime);
$monitor->recordMetric('database_connections', DB::getConnections());
$monitor->recordMetric('cache_hit_rate', $cache->getHitRate());
```

### Health Checks

Built-in health check endpoints:

```php
// routes/web.php
Route::get('/health', function () {
    return [
        'status' => 'ok',
        'timestamp' => now(),
        'services' => [
            'database' => DB::connection()->getPdo() ? 'ok' : 'error',
            'cache' => Cache::store()->getStore() ? 'ok' : 'error',
            'redis' => Redis::ping() ? 'ok' : 'error',
        ],
    ];
});
```

## Configuration Tuning

### Environment-Specific Optimization

```env
# Development
APP_DEBUG=true
CACHE_STORE=file
LOG_LEVEL=debug

# Production
APP_DEBUG=false
CACHE_STORE=redis
LOG_LEVEL=warning
```

### Database Tuning

```env
# Connection pooling
DB_CONNECTION_POOL_SIZE=10

# Query logging (development only)
DB_LOG_QUERIES=true

# Slow query logging
DB_SLOW_QUERY_THRESHOLD=1000
```

## Performance Testing

### Load Testing

Use tools like Artillery or k6 for load testing:

```javascript
// artillery.yml
config:
  target: 'http://localhost'
  phases:
    - duration: 60
      arrivalRate: 10

scenarios:
  - name: 'API Load Test'
    requests:
      - get:
          url: '/api/clips'
```

### Profiling

Use Laravel Telescope for performance profiling:

```bash
composer require laravel/telescope
php artisan telescope:install
php artisan migrate
```

## Optimization Checklist

### Database Optimization
- [ ] Add proper indexes on foreign keys
- [ ] Use eager loading to prevent N+1 queries
- [ ] Implement query result caching
- [ ] Optimize slow queries

### Caching Strategy
- [ ] Implement response caching for read-heavy endpoints
- [ ] Use Redis for session and cache storage
- [ ] Configure proper cache TTL values
- [ ] Implement cache warming for critical data

### Frontend Optimization
- [ ] Minify and compress assets
- [ ] Implement lazy loading for images
- [ ] Use CDN for static assets
- [ ] Optimize bundle splitting

### Infrastructure Optimization
- [ ] Use connection pooling
- [ ] Implement horizontal scaling
- [ ] Configure proper PHP-FPM settings
- [ ] Use HTTP/2 for better performance

## Monitoring Dashboard

### Performance Metrics Dashboard

Future feature for real-time performance monitoring:

- Response time graphs
- Error rate monitoring
- Database query performance
- Cache hit rates
- User activity metrics

### Alerting

Configure alerts for performance issues:

- Slow query alerts
- High error rate alerts
- Memory usage alerts
- Response time degradation

## Troubleshooting

### Common Performance Issues

1. **Slow API Responses**
   - Check database query performance
   - Verify caching is working
   - Monitor server resources

2. **High Memory Usage**
   - Check for memory leaks
   - Optimize large data processing
   - Implement pagination for large datasets

3. **Database Bottlenecks**
   - Add missing indexes
   - Optimize slow queries
   - Consider read replicas

4. **Cache Issues**
   - Verify cache backend connectivity
   - Check cache TTL settings
   - Monitor cache hit rates

### Performance Debugging

```php
// Enable query logging
DB::enableQueryLog();

// Log performance metrics
Log::info('Performance Debug', [
    'memory' => memory_get_peak_usage(true),
    'time' => microtime(true) - LARAVEL_START,
    'queries' => count(DB::getQueryLog()),
]);
```