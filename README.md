# ClipNook

[![License](https://img.shields.io/badge/license-AGPL--3.0-blue.svg)](LICENSE) [![PHP Version](https://img.shields.io/badge/PHP-8.5-777bb4.svg)](https://www.php.net/)

A privacy-first, self-hosted platform for viewers, streamers and video editors — submit, manage and discover gaming clips.

> ⚠️ **Status:** Actively developed — not production-ready. Intended for testing and development only.

---

## ✨ Overview

ClipNook gives streamers control over their highlights and provides viewers/editors with a clean, ad-free interface and simple moderation workflows.

**Core Principles:**
- Privacy-first (GDPR-aware)
- Streamer ownership of submitted content
- Lightweight moderation tools
- No ads, no hidden fees

---

## 🚀 Key Features

- Submit clips via Twitch clip URL
- Browse by streamer, submitter, category or tags
- Privacy-friendly video player
- Likes, comments and report workflow
- User profiles with roles (Viewer, Streamer, Editor)
- Moderation tools for streamers

---

## 🛠 Tech Stack

- Backend: PHP 8.5, Laravel
- Frontend: Livewire, Alpine.js, Tailwind CSS
- Icons: Font Awesome (Free)

---

## 📦 Quick Start (Development)

**Prerequisites:** PHP, Composer, Node.js, npm/yarn

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
# Edit .env (DB, Mail, etc.)
```
4. Migrate & seed
```bash
php artisan migrate
php artisan db:seed
```
5. Assets & dev server
```bash
npm run dev
php artisan serve
# For production: npm run build
```

---

## ✅ Useful Commands

```bash
# Tests
vendor/bin/pest
php artisan test

# Caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## 🤝 Contributing

Open issues for bugs or feature requests. For code contributions: Fork → Branch → Commit → PR. Keep commits clear and add tests where applicable.

---

## 💝 Support

ClipNook is a **non-commercial, community-driven project**. We don't accept donations. If you'd like to support our mission, please consider donating to charitable organizations such as cancer research foundations or animal welfare groups instead.

---

## ⚖️ License

This project is licensed under the **GNU AGPL v3.0** — see `LICENSE`.