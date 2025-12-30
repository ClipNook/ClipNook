# Security Features

ClipNook implements comprehensive security measures to protect against common web vulnerabilities and attacks. This document covers all security features and their configuration.

## Security Headers

### HTTP Security Headers Middleware

The `SecurityHeaders` middleware automatically applies security headers to all responses:

- **X-Content-Type-Options**: Prevents MIME type sniffing
- **X-Frame-Options**: Prevents clickjacking attacks
- **X-XSS-Protection**: Enables XSS filtering
- **X-Permitted-Cross-Domain-Policies**: Restricts cross-domain policies
- **Referrer-Policy**: Controls referrer information
- **Permissions-Policy**: Restricts browser features

### HTTP Strict Transport Security (HSTS)

```env
SECURITY_HSTS_MAX_AGE=31536000
SECURITY_HSTS_INCLUDE_SUBDOMAINS=true
SECURITY_HSTS_PRELOAD=false
```

Forces HTTPS connections and prevents protocol downgrade attacks.

### Content Security Policy (CSP)

Configurable CSP with environment-specific rules:

```env
SECURITY_CSP_REPORT_URI=
SECURITY_CSP_REPORT_ONLY=false
SECURITY_CSP_ADDITIONAL_SCRIPT_SRC=
SECURITY_CSP_ADDITIONAL_STYLE_SRC=
SECURITY_CSP_ADDITIONAL_FONT_SRC=
SECURITY_CSP_ADDITIONAL_IMG_SRC=
SECURITY_CSP_ADDITIONAL_CONNECT_SRC=
SECURITY_CSP_ADDITIONAL_FORM_ACTION_SRC=
```

**CSP Behavior:**
- **Development**: Permissive for Vite HMR and local development
- **Production**: Strict CSP with configurable additional sources
- **Auth Routes**: CSP disabled to prevent login blocking

## Rate Limiting

### Advanced Rate Limiter

Configurable rate limiting with multiple storage backends:

```env
RATE_LIMIT_WINDOW_SIZE=60
RATE_LIMIT_MAX_REQUESTS=60
RATE_LIMIT_BURST_LIMIT=10
RATE_LIMIT_STORAGE_PATH=storage/app/rate_limits
```

**Features:**
- Sliding window algorithm
- Burst protection
- Redis or file-based storage
- Automatic cleanup of expired entries

### Implementation

```php
use App\Services\Security\AdvancedRateLimiter;

$limiter = new AdvancedRateLimiter();
$limiter->checkLimit('api', $request->ip());
```

## Login Monitoring

### Suspicious Activity Detection

Monitors login attempts and detects suspicious patterns:

```env
LOGIN_LOCKOUT_TIME=3600
LOGIN_MAX_ATTEMPTS=5
LOGIN_ATTEMPT_WINDOW=3600
LOGIN_STORAGE_PATH=storage/app/login_attempts
```

**Features:**
- Failed attempt tracking
- Account lockout after threshold
- Admin notifications
- Attempt history logging

### Usage

```php
use App\Services\Security\LoginMonitor;

$monitor = new LoginMonitor();
$monitor->recordAttempt($identifier, $successful);

if ($monitor->isLocked($identifier)) {
    // Handle locked account
}
```

## Performance Monitoring

### Slow Query Detection

Monitors database queries and detects performance issues:

```env
PERFORMANCE_SLOW_QUERY_THRESHOLD=1000
PERFORMANCE_METRICS_RETENTION_HOURS=24
PERFORMANCE_MAX_ENTRIES=1000
PERFORMANCE_STORAGE_PATH=storage/logs/performance
```

**Features:**
- Configurable slow query threshold
- Metrics collection and retention
- Performance insights and alerting

### Implementation

```php
use App\Services\Monitoring\PerformanceMonitor;

$monitor = new PerformanceMonitor();
$monitor->recordQuery($query, $executionTime);
$monitor->recordMetric('response_time', $time);
```

## Data Protection

### GDPR Compliance

Built-in GDPR compliance features:

- **Data Export**: Users can export their data
- **Account Deletion**: Secure account deletion process
- **Consent Management**: Granular consent tracking
- **Data Retention**: Configurable data retention policies

### API Endpoints

```http
GET    /api/gdpr/data-export           # Export user data
POST   /api/gdpr/account/delete-request # Request deletion
DELETE /api/gdpr/account/confirm       # Confirm deletion
GET    /api/gdpr/consents              # Get consents
POST   /api/gdpr/consents              # Update consents
```

## Authentication Security

### Laravel Sanctum

API token-based authentication with:

- **Personal Access Tokens**: Secure API access
- **Token Scoping**: Limited permissions
- **Token Expiration**: Automatic cleanup

### OAuth Integration

Secure Twitch OAuth integration:

- **State Parameter**: CSRF protection
- **PKCE**: Enhanced security for public clients
- **Token Refresh**: Automatic token renewal

## Input Validation

### Request Validation

Comprehensive input validation using Laravel Form Requests:

```php
class StoreClipRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'twitch_clip_url' => 'required|url|regex:/^https:\/\/clips\.twitch\.tv\//',
            'description' => 'nullable|string|max:1000',
            'tags' => 'nullable|array|max:10',
        ];
    }
}
```

## Security Best Practices

### Environment Security

- **Environment Variables**: Sensitive data in `.env`
- **App Key**: Unique application encryption key
- **Debug Mode**: Disabled in production

### Database Security

- **Prepared Statements**: SQL injection prevention
- **Mass Assignment Protection**: Fillable attributes
- **Query Logging**: Development-only logging

### File Upload Security

- **MIME Type Validation**: File type verification
- **Size Limits**: Upload size restrictions
- **Storage Security**: Secure file permissions

## Monitoring & Alerting

### Security Events

Automatic monitoring of security-related events:

- Failed login attempts
- Rate limit violations
- Suspicious activities
- Security header violations

### Logging

Comprehensive logging for security events:

```php
// Security events are logged automatically
Log::warning('Suspicious login activity', [
    'identifier' => $identifier,
    'attempts' => $attemptCount,
    'ip' => $request->ip(),
]);
```

## Configuration Examples

### Development Environment

```env
# Permissive for development
SECURITY_CSP_ADDITIONAL_SCRIPT_SRC=http://localhost:5173
SECURITY_CSP_ADDITIONAL_CONNECT_SRC=ws://localhost:5173
RATE_LIMIT_MAX_REQUESTS=1000
LOGIN_MAX_ATTEMPTS=10
```

### Production Environment

```env
# Strict for production
APP_DEBUG=false
APP_ENV=production
SECURITY_HSTS_PRELOAD=true
RATE_LIMIT_MAX_REQUESTS=60
LOGIN_MAX_ATTEMPTS=5
```

## Troubleshooting

### Common Security Issues

1. **CSP Blocking Resources**
   - Add domains to `SECURITY_CSP_ADDITIONAL_*` variables
   - Check browser console for CSP violations

2. **Rate Limiting Too Restrictive**
   - Increase `RATE_LIMIT_MAX_REQUESTS`
   - Adjust `RATE_LIMIT_WINDOW_SIZE`

3. **Login Lockouts**
   - Check `LOGIN_MAX_ATTEMPTS` and `LOGIN_ATTEMPT_WINDOW`
   - Clear storage files if needed

4. **Performance Issues**
   - Adjust `PERFORMANCE_SLOW_QUERY_THRESHOLD`
   - Check storage paths and permissions

### Security Audits

Regular security audits should include:

- Dependency vulnerability scanning
- CSP violation monitoring
- Rate limiting effectiveness
- Authentication security testing
- Input validation verification