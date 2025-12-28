# ClipNook

[![License](https://img.shields.io/badge/license-AGPL--3.0-blue.svg)](LICENSE)
[![PHP Version](https://img.shields.io/badge/PHP-8.5-777bb4.svg)](https://www.php.net/)
[![GitHub Issues](https://img.shields.io/github/issues/ClipNook/ClipNook)](https://github.com/ClipNook/ClipNook/issues)
[![GitHub Stars](https://img.shields.io/github/stars/ClipNook/ClipNook)](https://github.com/ClipNook/ClipNook)

A privacy-focused platform for viewers, streamers, and video editors - submit, manage, and discover gaming clips.

> ⚠️ **Status:** Not production-ready - This project is currently under active development and may be unstable. It is intended for testing and development purposes only. Do not use it in production environments.

---

## ✨ Overview

ClipNook is a self-hosted, community-driven platform designed to give streamers full ownership and control over their highlights while providing viewers and editors with a clean, ad-free space to interact.

**Key Principles:**
*   **Privacy-First & GDPR-Compliant:** Built with data minimization and user privacy in mind.
*   **Streamer Ownership:** Streamers retain ultimate ownership of clips submitted by their community.
*   **Simple Moderation:** Intuitive workflows for streamers and their moderators.
*   **Ad-Free & Free:** No ads, no premium tiers, no cost.

---

## 🚀 Features

### For Viewers & Editors
*   Submit clips via Twitch Clip URL.
*   Browse clips by streamer, submitter, category, or tags.
*   View clips with a privacy-friendly player.
*   Interact via likes and comments.
*   Report inappropriate content.
*   User profiles with roles (Viewer, Streamer, Editor), follower counts, and pinboards.
*   Editors can signal their availability with an "Available for Work" label.

### For Streamers & Moderators
*   Full ownership and management of clips from your channel.
*   Appoint moderators to help manage clips and comments.
*   Edit or remove clips and comments.
*   Define content warnings and categories.

---

## 🛠️ Tech Stack

*   **Backend:** PHP 8.5, Laravel
*   **Frontend:** Livewire, Alpine.js, Tailwind CSS
*   **Icons:** Font Awesome (Free)

---

## 🔐 User & Data Deletion Policy

*   **Viewer Account Deletion:** All personal data is removed. Submitted clips remain but are anonymized (submitter info is cleared).
*   **Streamer Account Deletion:** All clips managed by the streamer will be deleted.
*   **Moderation:** Streamers can assign moderators with specific permissions.

---

## 📦 Installation & Development

### Prerequisites
*   PHP 8.5+
*   Composer
*   Node.js & npm/yarn

### Steps
1.  Clone the repository and navigate into it.
    ```bash
    git clone https://github.com/ClipNook/ClipNook.git
    cd ClipNook
    ```
2.  Install PHP and JavaScript dependencies.
    ```bash
    composer install
    npm install
    ```
3.  Configure the environment.
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
    *Edit the `.env` file to set your database credentials and other environment variables.*
4.  Run database migrations.
    ```bash
    php artisan migrate
    ```
5.  Build the frontend assets.
    ```bash
    npm run dev
    # For production: npm run build
    ```
6.  Start the local development server.
    ```bash
    php artisan serve
    ```
    The application will be available at `http://localhost:8000`.

---

## 🤝 Contributing

Contributions are welcome and appreciated! Here's how you can help:

1.  **Report Bugs & Request Features:** Please open an [Issue](https://github.com/ClipNook/ClipNook/issues) and provide a clear description.
2.  **Submit Code Changes:**
    *   Fork the repository.
    *   Create a feature branch (`git checkout -b feature/amazing-feature`).
    *   Commit your changes (`git commit -m 'Add some amazing feature'`).
    *   Push to the branch (`git push origin feature/amazing-feature`).
    *   Open a Pull Request.

Please ensure your code follows the project's style and include tests where applicable.

---

## ⚖️ Disclaimer

The developers, maintainers, and contributors of ClipNook assume **no liability** for any direct or indirect damages arising from the use of this software, its provided content, or its integration with third-party services. Use is at your own risk and without any warranty.

ClipNook is **not affiliated, associated, or in any way officially connected** with individual streamers, Twitch, or any other streaming platform. Users who submit or manage content are solely responsible for ensuring its legality, compliance with platform Terms of Service, and respect for copyright and personality rights.

---

## 📄 License

This project is licensed under the **GNU Affero General Public License v3.0 (AGPL-3.0)**. See the [LICENSE](LICENSE) file for the full text.