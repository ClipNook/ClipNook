# ClipNook

[![License: AGPL v3](https://img.shields.io/badge/License-AGPL_v3-blue.svg)](https://www.gnu.org/licenses/agpl-3.0)
[![PHP Version](https://img.shields.io/badge/PHP-8.5+-777bb4.svg)](https://www.php.net/)
[![Laravel Version](https://img.shields.io/badge/Laravel-12.x-ff2d20.svg)](https://laravel.com)
[![Livewire](https://img.shields.io/badge/Livewire-3.x-fb70a9.svg)](https://laravel-livewire.com)

A privacy-first, self-hosted platform for gamers, streamers, and editors to submit, manage, and discover gaming clips—putting content control back in creators' hands.

> **Project Status:** Actively developed (Beta). Not yet production-ready. Perfect for testing and development.

## Overview

ClipNook empowers streamers to own their highlight ecosystem while providing viewers and editors with a clean, ad-free interface for clip discovery and collaboration.

**Our Core Principles:**
- **Privacy-First:** GDPR-aware by design
- **Streamer-Centric:** Creators maintain ownership of submitted content
- **Lightweight Moderation:** Simple, effective tools for content management
- **No Ads, No Tracking:** Completely free and transparent

## Features

### For Viewers & Editors
- Submit clips via Twitch clip URL
- Browse by streamer, submitter, category, or tags
- Like clips and add comments
- Clean, intuitive user profiles with role-based access (Viewer/Streamer/Editor)

### For Streamers
- Full control over submitted clips
- Built-in moderation dashboard
- Simple report workflow
- Privacy-friendly video player

### Platform
- Modern, responsive UI built with Livewire & Tailwind CSS
- Real-time interactions
- Structured clip organization
- Powerful search and filtering

## Twitch Integration

ClipNook features seamless Twitch OAuth authentication and direct Twitch Clips API support, all designed with privacy in mind.

### Authentication
- Secure OAuth flow (Authorize, Callback, Revoke)
- GDPR-compliant consent checkbox (server-side validated)
- "Remember Me" option with persistent login cookie (user-controlled)
- Optional local avatar storage for GDPR compliance

### API Features
- Create, list, and retrieve clips via Twitch Helix API
- Encrypted token storage in database
- Configurable data retention policies

### Configuration
Add to your `.env` file:
```env
TWITCH_CLIENT_ID=your_client_id
TWITCH_CLIENT_SECRET=your_client_secret
TWITCH_REDIRECT_URI=https://your-domain.com/auth/twitch/callback
TWITCH_SCOPES=user:read:email
```

### Privacy Settings (`config/services.php`)
- `privacy.log_requests` (bool) - Log Twitch API requests
- `privacy.anonymize_ip` (bool) - Anonymize IP addresses
- `privacy.data_retention` (int) - Days to retain tokens/avatars
- `privacy.store_avatars` (bool) - Store avatars locally (GDPR-friendly)
- `remember` (bool) - Default "Remember Me" behavior

## Tech Stack

- **Backend:** PHP 8.5+, Laravel 12
- **Frontend:** Livewire 3, Alpine.js, Tailwind CSS
- **Icons:** Font Awesome Free
- **Database:** MySQL/PostgreSQL/SQLite
- **Storage:** Local or cloud (S3-compatible)

## Quick Start (Development)

**Prerequisites:** PHP 8.5+, Composer, Node.js 18+, npm/yarn

1. Clone the repository
```bash
git clone https://github.com/ClipNook/ClipNook.git
cd ClipNook
```

2. Install dependencies
```bash
composer install
npm install
```

3. Configure environment
```bash
cp .env.example .env
php artisan key:generate
# Edit .env with your database, mail, and Twitch credentials
```

4. Setup database
```bash
php artisan migrate
php artisan db:seed
```

5. Start development servers
```bash
npm run dev
php artisan serve
```

6. For production build
```bash
npm run build
```

## Useful Commands

```bash
# Run tests
vendor/bin/pest
php artisan test

# Clear caches
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Generate assets for production
npm run build
```

## Contributing

ClipNook is a community-driven project. We welcome contributions!

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

Please ensure your code follows existing style conventions and includes tests where applicable.

## Support

ClipNook is a **non-commercial, community-driven project**. We do not accept donations or offer commercial support.

If you'd like to support our mission, please consider donating to charitable organizations such as cancer research foundations or animal welfare groups instead.

## License

This project is licensed under the **GNU Affero General Public License v3.0** - see the [LICENSE](LICENSE) file for details.

The AGPL v3 requires that any modified versions of this software that are used to provide services over a network must make their source code available to users of that service.