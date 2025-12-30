# ClipNook

[![License: AGPL v3](https://img.shields.io/badge/License-AGPL_v3-blue.svg)](https://www.gnu.org/licenses/agpl-3.0)
[![PHP Version](https://img.shields.io/badge/PHP-8.5+-777bb4.svg)](https://www.php.net/)
[![Laravel Version](https://img.shields.io/badge/Laravel-12.x-ff2d20.svg)](https://laravel.com)
[![Livewire](https://img.shields.io/badge/Livewire-3.x-fb70a9.svg)](https://laravel-livewire.com)
[![Pest](https://img.shields.io/badge/Pest-4.x-8A2BE2.svg)](https://pestphp.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-4.x-38B2AC.svg)](https://tailwindcss.com)

A privacy-first, self-hosted platform for gamers, streamers, and editors to submit, manage, and discover gaming clips—putting content control back in creators' hands.

> **Project Status:** Hobby project in early development. Backend API and core functionality complete. Frontend implementation in progress. Not intended for production use—best suited for testing, development, and contributions.

## 🚀 Quick Start

```bash
# Clone the repository
git clone https://github.com/ClipNook/ClipNook.git
cd ClipNook

# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install

# Copy environment file and configure
cp .env.example .env
php artisan key:generate

# Configure your database in .env
# Configure Twitch OAuth credentials in .env

# Run migrations
php artisan migrate

# Seed the database (optional)
php artisan db:seed

# Build assets
npm run build

# Start the development server
php artisan serve
```

## 📋 Requirements

- **PHP**: 8.5+
- **Laravel**: 12.x
- **Database**: MySQL 8.0+, PostgreSQL 13+, SQLite 3.35+
- **Node.js**: 18+ (for asset compilation)
- **Composer**: Latest stable
- **npm**: Latest stable

## 🏗️ Architecture

### Tech Stack

- **Backend**: Laravel 12 (PHP 8.5+)
- **Frontend**: Livewire 3 + Alpine.js
- **Styling**: Tailwind CSS 4
- **Database**: Eloquent ORM with migrations
- **Authentication**: Laravel Sanctum (API tokens)
- **Performance Monitoring**: Configurable metrics and thresholds
- **Security**: Advanced rate limiting, login monitoring, security headers
- **Testing**: Pest 4
- **Code Quality**: Laravel Pint
- **External APIs**: Twitch Helix API

### Core Components

```
app/
├── Actions/           # Service layer actions
│   ├── Clip/         # Clip-related business logic
│   └── GDPR/         # GDPR compliance actions
├── Http/Controllers/ # HTTP controllers
├── Http/Middleware/  # Custom middleware (SecurityHeaders, CacheResponse)
├── Models/           # Eloquent models
├── Notifications/    # Email/SMS notifications
├── Policies/         # Authorization policies
├── Services/         # External service integrations
│   ├── Monitoring/   # Performance monitoring services
│   └── Security/     # Security services (Rate limiting, Login monitoring)
└── Helper/           # Utility classes

config/
├── app.php          # Application configuration
├── performance.php  # Performance and security settings
└── ...             # Other Laravel configs

database/
├── factories/        # Model factories for testing
├── migrations/       # Database schema migrations
└── seeders/          # Database seeders

resources/
├── css/             # Tailwind CSS styles
├── js/              # Alpine.js components
└── views/           # Blade templates

routes/
├── api.php          # API routes (Sanctum protected)
├── web.php          # Web routes
└── console.php      # Artisan commands

storage/
├── app/             # Application storage
├── framework/       # Laravel framework files
└── logs/           # Application logs

tests/
├── Feature/         # Feature tests
├── Unit/           # Unit tests
└── Browser/        # Browser/E2E tests (future)
```

### Design Patterns

- **Service Layer Pattern**: Business logic encapsulated in Action classes
- **Repository Pattern**: Data access abstracted through Eloquent models
- **Policy-based Authorization**: Fine-grained access control
- **Event-driven Architecture**: Laravel events for decoupled components
- **Configurable Security**: Environment-based security settings
- **Performance Monitoring**: Built-in metrics and alerting

## 🛡️ Security Features

### Advanced Rate Limiting
- Configurable request limits per time window
- Burst protection for sudden traffic spikes
- Redis or file-based storage options
- Automatic cleanup of expired entries

### Login Monitoring
- Suspicious activity detection
- Configurable lockout periods
- Failed attempt tracking and alerting
- Admin notifications for security events

### Security Headers
- Content Security Policy (CSP) with environment-specific rules
- HTTP Strict Transport Security (HSTS)
- XSS and clickjacking protection
- Configurable security policies

### Performance Monitoring
- Slow query detection and logging
- Configurable performance thresholds
- Metrics retention and cleanup
- Real-time performance insights

## 🔧 Configuration

### Environment Variables

```bash
# Application
APP_NAME=ClipNook
APP_ENV=local
APP_KEY=base64:your-app-key
APP_DEBUG=true
APP_URL=http://localhost

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=clipnook
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Twitch OAuth
TWITCH_CLIENT_ID=your_twitch_client_id
TWITCH_CLIENT_SECRET=your_twitch_client_secret
TWITCH_REDIRECT_URI=http://localhost/auth/twitch/callback

# Performance Monitoring
PERFORMANCE_SLOW_QUERY_THRESHOLD=1000
PERFORMANCE_METRICS_RETENTION_HOURS=24
PERFORMANCE_MAX_ENTRIES=1000
PERFORMANCE_STORAGE_PATH=storage/logs/performance

# Rate Limiting
RATE_LIMIT_WINDOW_SIZE=60
RATE_LIMIT_MAX_REQUESTS=60
RATE_LIMIT_BURST_LIMIT=10
RATE_LIMIT_STORAGE_PATH=storage/app/rate_limits

# Login Monitoring
LOGIN_LOCKOUT_TIME=3600
LOGIN_MAX_ATTEMPTS=5
LOGIN_ATTEMPT_WINDOW=3600
LOGIN_STORAGE_PATH=storage/app/login_attempts

# Response Caching
RESPONSE_CACHE_DEFAULT_TTL=300

# Security Headers & CSP
SECURITY_HSTS_MAX_AGE=31536000
SECURITY_HSTS_INCLUDE_SUBDOMAINS=true
SECURITY_HSTS_PRELOAD=false
SECURITY_CSP_REPORT_URI=
SECURITY_CSP_REPORT_ONLY=false
SECURITY_CSP_ADDITIONAL_SCRIPT_SRC=
SECURITY_CSP_ADDITIONAL_STYLE_SRC=
SECURITY_CSP_ADDITIONAL_FONT_SRC=
SECURITY_CSP_ADDITIONAL_IMG_SRC=
SECURITY_CSP_ADDITIONAL_CONNECT_SRC=
SECURITY_CSP_ADDITIONAL_FORM_ACTION_SRC=

# Mail (optional)
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@clipnook.test"
MAIL_FROM_NAME="${APP_NAME}"

# Queue (optional)
QUEUE_CONNECTION=database
```

### Twitch OAuth Setup

1. Create a Twitch application at [Twitch Developer Console](https://dev.twitch.tv/console/apps)
2. Set redirect URI to: `http://your-domain/auth/twitch/callback`
3. Copy Client ID and Client Secret to your `.env` file

## 📚 Documentation

- **[Configuration Guide](doc/configuration.md)** - Complete configuration reference
- **[Security Features](doc/security.md)** - Security implementation details
- **[Performance Guide](doc/performance.md)** - Performance monitoring and optimization
- **[API Documentation](doc/api-usage.md)** - API endpoints and usage
- **[Authentication](doc/authentication.md)** - OAuth and authentication setup
- **[Deployment](doc/deployment.md)** - Production deployment guide
- **[Testing](doc/testing.md)** - Testing strategies and examples
- **[Troubleshooting](doc/troubleshooting.md)** - Common issues and solutions

### Authentication

All API endpoints require authentication via Laravel Sanctum personal access tokens.

```bash
# Create a token
curl -X POST http://localhost/api/tokens \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"password"}'

# Use token in requests
curl -H "Authorization: Bearer your-token-here" \
  http://localhost/api/clips
```

### Endpoints

#### Clips

```http
GET    /api/clips              # List approved clips
GET    /api/clips/{id}         # Get specific clip
POST   /api/clips              # Submit new clip
PUT    /api/clips/{id}         # Update clip (owner only)
DELETE /api/clips/{id}         # Delete clip (owner/moderator)

GET    /api/my-clips           # User's submitted clips
GET    /api/clips/pending      # Pending clips (moderators)
POST   /api/clips/{id}/approve # Approve clip (moderators)
POST   /api/clips/{id}/reject  # Reject clip (moderators)
```

#### GDPR Compliance

```http
GET    /api/gdpr/data-export           # Export user data (GDPR Art. 20)
POST   /api/gdpr/account/delete-request # Request account deletion
DELETE /api/gdpr/account/confirm       # Confirm account deletion
GET    /api/gdpr/consents              # Get user consents
POST   /api/gdpr/consents              # Update user consents
GET    /api/gdpr/retention-info        # Data retention information
```

### Request/Response Examples

#### Submit Clip
```bash
curl -X POST http://localhost/api/clips \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "twitch_clip_url": "https://clips.twitch.tv/AmazingClip123",
    "title": "Amazing gameplay moment",
    "description": "Epic clutch play",
    "tags": ["fps", "clutch", "headshot"]
  }'
```

#### Response
```json
{
  "id": 1,
  "title": "Amazing gameplay moment",
  "twitch_clip_url": "https://clips.twitch.tv/AmazingClip123",
  "status": "pending",
  "user_id": 1,
  "created_at": "2024-01-01T12:00:00Z",
  "updated_at": "2024-01-01T12:00:00Z"
}
```

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/ClipTest.php

# Run with coverage
php artisan test --coverage

# Run Pest tests with specific filter
php artisan test --filter="user can submit a clip"
```

### Test Structure

- **Unit Tests**: Test individual classes and methods
- **Feature Tests**: Test complete user workflows
- **API Tests**: Test HTTP endpoints and responses
- **Browser Tests**: E2E testing with Pest browser testing

## 🛠️ Development

### Code Quality

```bash
# Format code with Pint
vendor/bin/pint

# Check code style
vendor/bin/pint --test

# Run static analysis (if configured)
# composer run phpstan
```

### Database Management

```bash
# Create migration
php artisan make:migration create_feature_table

# Run migrations
php artisan migrate

# Rollback
php artisan migrate:rollback

# Create seeder
php artisan make:seeder FeatureSeeder

# Seed database
php artisan db:seed
```

### Asset Compilation

```bash
# Development build (watch mode)
npm run dev

# Production build
npm run build

# Build and watch
npm run watch
```

## 🚀 Deployment

### Production Checklist

- [ ] Set `APP_ENV=production` and `APP_DEBUG=false`
- [ ] Configure production database
- [ ] Set up proper mail configuration
- [ ] Configure queue worker: `php artisan queue:work`
- [ ] Set up SSL certificate
- [ ] Configure proper file permissions
- [ ] Run `php artisan config:cache` and `php artisan route:cache`
- [ ] Set up monitoring and logging

### Docker (Future)

Docker support planned for future releases.

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/amazing-feature`
3. Commit your changes: `git commit -m 'Add amazing feature'`
4. Push to the branch: `git push origin feature/amazing-feature`
5. Open a Pull Request

### Development Guidelines

- Follow PSR-12 coding standards
- Use Laravel Pint for code formatting
- Write tests for new features
- Update documentation as needed
- Use meaningful commit messages
- Keep PRs focused and atomic

### Code Review Process

- All PRs require review
- Tests must pass
- Code must be formatted with Pint
- Documentation updated if needed

## 📝 License

This project is licensed under the **GNU Affero General Public License v3.0** - see the [LICENSE](LICENSE) file for details.

The AGPL v3 requires that any modified versions of this software that are used to provide services over a network must make their source code available to users of that service.

## 🙏 Support

ClipNook is a **non-commercial, community-driven project**. We do not accept donations or offer commercial support.

If you'd like to support our mission, please consider donating to charitable organizations such as cancer research foundations or animal welfare groups instead.

### Contact

- **Issues**: [GitHub Issues](https://github.com/ClipNook/ClipNook/issues)
- **Discussions**: [GitHub Discussions](https://github.com/ClipNook/ClipNook/discussions)
- **Collaboration**: **Telegram:** [@Zurret](https://t.me/Zurret)

## 📊 Project Status

### ✅ Completed
- Backend API with full CRUD operations
- GDPR compliance (data export, deletion, consent management)
- Twitch OAuth integration
- Laravel Sanctum authentication
- Advanced security features (rate limiting, login monitoring, security headers)
- Performance monitoring and metrics
- Configurable application settings
- Comprehensive test suite
- Database schema and migrations
- Code formatting and quality tools

### 🚧 In Progress
- Frontend implementation (Livewire components)
- Admin/moderation interface
- Advanced search and filtering
- Clip embedding and playback
- User dashboard
- API documentation improvements

### 📋 Planned
- Docker containerization
- Advanced moderation tools
- Social features (comments, likes)
- Browser testing suite
- Performance optimization tools

---

**Built with ❤️ using Laravel, Livewire, and Tailwind CSS**