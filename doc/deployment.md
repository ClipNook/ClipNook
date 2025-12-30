# Deployment Guide

## Overview

This guide covers deploying the Twitch API integration to various environments including development, staging, and production.

## Environment Setup

### Required Environment Variables

Create a `.env` file in your project root:

```env
# Application
APP_NAME="Twitch Integration"
APP_ENV=production
APP_KEY=base64:your_app_key_here
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=twitch_app
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

# Cache & Sessions
CACHE_DRIVER=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Twitch API
TWITCH_CLIENT_ID=your_twitch_client_id
TWITCH_CLIENT_SECRET=your_twitch_client_secret

# Queue (Optional)
QUEUE_CONNECTION=redis

# Mail (Optional)
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

# Logging
LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error
```

### Generate Application Key

```bash
php artisan key:generate
```

## Server Requirements

### Minimum Requirements

- **PHP**: 8.5 or higher
- **Database**: MySQL 8.0+, PostgreSQL 13.0+, SQLite 3.8.8+
- **Web Server**: Apache 2.4+ or Nginx 1.20+
- **Memory**: 256MB RAM minimum, 512MB recommended
- **Storage**: 200MB free space

### Recommended Production Setup

- **PHP**: 8.5 with OPcache and JIT enabled
- **Database**: MySQL 8.0+ or PostgreSQL 15.0+
- **Cache**: Redis 6.0+
- **Web Server**: Nginx with PHP-FPM
- **SSL**: Let's Encrypt or commercial SSL certificate
- **Memory**: 1GB+ RAM
- **CPU**: 1+ core

## Web Server Configuration

### Nginx Configuration

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name yourdomain.com;

    # SSL Configuration
    ssl_certificate /path/to/ssl/certificate.crt;
    ssl_certificate_key /path/to/ssl/private.key;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES128-GCM-SHA256:ECDHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers off;

    # Root directory
    root /var/www/twitch-app/public;
    index index.php index.html;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css text/xml text/javascript application/javascript application/xml+rss application/json;

    # PHP handling
    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_pass unix:/var/run/php/php8.5-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Static files
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        try_files $uri =404;
    }

    # Deny access to sensitive files
    location ~ /\.(?!well-known).* {
        deny all;
    }

    location ~ ^/\. {
        deny all;
    }

    # Laravel specific
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
}
```

### Apache Configuration

```apache
<VirtualHost *:80>
    ServerName yourdomain.com
    Redirect permanent / https://yourdomain.com/
</VirtualHost>

<VirtualHost *:443>
    ServerName yourdomain.com

    SSLEngine on
    SSLCertificateFile /path/to/ssl/certificate.crt
    SSLCertificateKeyFile /path/to/ssl/private.key
    SSLProtocol TLSv1.2 TLSv1.3
    SSLCipherSuite ECDHE-RSA-AES128-GCM-SHA256:ECDHE-RSA-AES256-GCM-SHA384

    DocumentRoot /var/www/twitch-app/public

    <Directory /var/www/twitch-app/public>
        AllowOverride All
        Require all granted

        # Security headers
        Header always set X-Frame-Options SAMEORIGIN
        Header always set X-XSS-Protection "1; mode=block"
        Header always set X-Content-Type-Options nosniff
        Header always set Referrer-Policy "no-referrer-when-downgrade"
        Header always set Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'"

        # Enable compression
        SetOutputFilter DEFLATE
        SetEnvIfNoCase Request_URI \.(?:gif|jpe?g|png|ico|svg|woff|woff2|ttf|eot)$ no-gzip dont-vary
    </Directory>

    # Deny access to sensitive files
    <FilesMatch "\.(htaccess|htpasswd|ini|log|sh|sql|conf)$">
        Require all denied
    </FilesMatch>

    ErrorLog ${APACHE_LOG_DIR}/twitch-app-error.log
    CustomLog ${APACHE_LOG_DIR}/twitch-app-access.log combined
</VirtualHost>
```

## Database Setup

### MySQL Setup

```bash
# Create database
mysql -u root -p
CREATE DATABASE twitch_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'twitch_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON twitch_app.* TO 'twitch_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Run migrations
php artisan migrate
```

### PostgreSQL Setup

```bash
# Create database and user
sudo -u postgres psql
CREATE DATABASE twitch_app;
CREATE USER twitch_user WITH ENCRYPTED PASSWORD 'secure_password';
GRANT ALL PRIVILEGES ON DATABASE twitch_app TO twitch_user;
\q

# Run migrations
php artisan migrate
```

## Redis Setup

### Install Redis

```bash
# Ubuntu/Debian
sudo apt update
sudo apt install redis-server

# CentOS/RHEL
sudo yum install redis

# macOS (with Homebrew)
brew install redis

# Start Redis
sudo systemctl start redis
sudo systemctl enable redis
```

### Redis Configuration

```redis.conf
# Bind to localhost only
bind 127.0.0.1

# Set password (optional but recommended)
requirepass your_secure_password

# Memory management
maxmemory 256mb
maxmemory-policy allkeys-lru

# Persistence
save 900 1
save 300 10
save 60 10000
```

## SSL Certificate Setup

### Let's Encrypt (Free)

```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx

# Get certificate
sudo certbot --nginx -d yourdomain.com

# Auto-renewal (runs twice daily)
sudo crontab -e
# Add: 0 12 * * * /usr/bin/certbot renew --quiet
```

### Manual SSL Setup

```bash
# Generate private key
openssl genrsa -out private.key 2048

# Generate CSR
openssl req -new -key private.key -out certificate.csr

# Submit CSR to CA and get certificate
# Place certificate.crt and private.key in /etc/ssl/certs/
```

## Deployment Process

### 1. Code Deployment

```bash
# Clone repository
git clone https://github.com/your-repo/twitch-app.git
cd twitch-app

# Install dependencies
composer install --optimize-autoloader --no-dev
npm install && npm run build

# Set permissions
sudo chown -R www-data:www-data .
sudo chmod -R 755 storage bootstrap/cache

# Generate optimized autoloader
composer dump-autoload --optimize
```

### 2. Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate app key
php artisan key:generate

# Configure environment variables
nano .env
```

### 3. Database Migration

```bash
# Run migrations
php artisan migrate --force

# Seed database (if needed)
php artisan db:seed --force
```

### 4. Cache Optimization

```bash
# Clear and optimize caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 5. Storage Link

```bash
# Create storage symlink
php artisan storage:link
```

## Queue Setup (Optional)

### Supervisor Configuration

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/twitch-app/artisan queue:work --sleep=3 --tries=3 --max-jobs=1000
directory=/var/www/twitch-app
autostart=true
autorestart=true
numprocs=2
user=www-data
redirect_stderr=true
stdout_logfile=/var/www/twitch-app/storage/logs/worker.log
```

```bash
# Reload supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*
```

## Monitoring Setup

### Laravel Telescope (Development)

```bash
composer require laravel/telescope
php artisan telescope:install
php artisan migrate
```

### Health Checks

Create a health check route:

```php
// In routes/web.php
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
        'services' => [
            'database' => DB::connection()->getPdo() ? 'ok' : 'error',
            'cache' => Cache::store()->getStore() ? 'ok' : 'error',
            'redis' => Redis::ping() ? 'ok' : 'error',
        ],
    ]);
});
```

### Log Monitoring

```bash
# Monitor Laravel logs
tail -f storage/logs/laravel.log

# Monitor PHP-FPM logs
tail -f /var/log/php8.5-fpm.log

# Monitor Nginx logs
tail -f /var/log/nginx/error.log
tail -f /var/log/nginx/access.log
```

## Backup Strategy

### Database Backup

```bash
# MySQL backup script
mysqldump -u twitch_user -p twitch_app > backup_$(date +%Y%m%d_%H%M%S).sql

# PostgreSQL backup
pg_dump -U twitch_user twitch_app > backup_$(date +%Y%m%d_%H%M%S).sql
```

### File Backup

```bash
# Backup application files
tar -czf backup_$(date +%Y%m%d_%H%M%S).tar.gz /var/www/twitch-app
```

### Automated Backups

```bash
# Add to crontab
crontab -e

# Daily database backup at 2 AM
0 2 * * * /path/to/backup-script.sh

# Weekly file backup on Sunday at 3 AM
0 3 * * 0 /path/to/file-backup-script.sh
```

## Performance Optimization

### PHP Optimization

```ini
; php.ini optimizations
opcache.enable=1
opcache.enable_cli=1
opcache.memory_consumption=256
opcache.max_accelerated_files=7963
opcache.revalidate_freq=0
opcache.jit=tracing
opcache.jit_buffer_size=100M

; JIT settings
opcache.jit=1255
opcache.jit_buffer_size=100M
```

### Database Optimization

```sql
-- Add indexes for better performance
CREATE INDEX idx_users_twitch_id ON users (twitch_id);
CREATE INDEX idx_users_twitch_login ON users (twitch_login);
CREATE INDEX idx_users_last_activity ON users (last_activity_at);

-- Optimize tables
OPTIMIZE TABLE users;
```

### Cache Optimization

```php
// In config/cache.php
'stores' => [
    'redis' => [
        'driver' => 'redis',
        'connection' => 'cache',
        'lock_connection' => 'default',
    ],
],
```

## Security Hardening

### File Permissions

```bash
# Secure file permissions
find /var/www/twitch-app -type f -exec chmod 644 {} \;
find /var/www/twitch-app -type d -exec chmod 755 {} \;
chmod 600 /var/www/twitch-app/.env
chmod 600 /var/www/twitch-app/storage/logs/*.log
```

### Firewall Setup

```bash
# UFW (Ubuntu)
sudo ufw allow ssh
sudo ufw allow 'Nginx Full'
sudo ufw --force enable

# Firewalld (CentOS)
sudo firewall-cmd --permanent --add-service=http
sudo firewall-cmd --permanent --add-service=https
sudo firewall-cmd --permanent --add-service=ssh
sudo firewall-cmd --reload
```

### Fail2Ban Setup

```bash
# Install Fail2Ban
sudo apt install fail2ban

# Configure for Nginx
sudo cp /etc/fail2ban/jail.conf /etc/fail2ban/jail.local

# Edit jail.local to enable nginx rules
sudo systemctl enable fail2ban
sudo systemctl start fail2ban
```

## Scaling Considerations

### Horizontal Scaling

```nginx
# Load balancer configuration
upstream backend {
    server backend1.example.com;
    server backend2.example.com;
    server backend3.example.com;
}

server {
    listen 80;
    server_name yourdomain.com;

    location / {
        proxy_pass http://backend;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

### Database Scaling

```php
// Read/write connection splitting
'connections' => [
    'mysql' => [
        'read' => [
            'host' => ['read1.example.com', 'read2.example.com'],
        ],
        'write' => [
            'host' => 'write.example.com',
        ],
        'sticky' => true,
    ],
],
```

### Cache Clustering

```php
// Redis cluster configuration
'redis' => [
    'client' => env('REDIS_CLIENT', 'phpredis'),
    'options' => [
        'cluster' => env('REDIS_CLUSTER', 'redis'),
    ],
    'clusters' => [
        'cache' => [
            [
                'host' => env('REDIS_HOST', '127.0.0.1'),
                'password' => env('REDIS_PASSWORD'),
                'port' => env('REDIS_PORT', 6379),
                'database' => 0,
            ],
        ],
    ],
],
```

## Troubleshooting

### Common Issues

1. **500 Internal Server Error**
   - Check Laravel logs: `tail -f storage/logs/laravel.log`
   - Verify file permissions
   - Check PHP-FPM status

2. **Database Connection Error**
   - Verify database credentials in `.env`
   - Check database server status
   - Test connection manually

3. **Redis Connection Error**
   - Verify Redis is running: `redis-cli ping`
   - Check Redis configuration
   - Verify firewall settings

4. **SSL Certificate Issues**
   - Check certificate validity: `openssl x509 -in cert.crt -text`
   - Verify certificate chain
   - Check Nginx SSL configuration

5. **Performance Issues**
   - Enable OPcache and JIT
   - Optimize database queries
   - Implement caching strategies
   - Monitor resource usage

### Debug Mode

```php
// Temporarily enable debug mode
APP_DEBUG=true
APP_ENV=local

// Check configuration
php artisan config:show
php artisan route:list
php artisan tinker
```

## Maintenance Mode

```bash
# Enable maintenance mode
php artisan down

# Allow specific IPs
php artisan down --allow=192.168.1.1

# Disable maintenance mode
php artisan up
```

## Rollback Strategy

```bash
# Database rollback
php artisan migrate:rollback

# Code rollback
git reset --hard HEAD~1
git push --force

# Cache clear
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

## Monitoring & Alerting

### Application Monitoring

```bash
# Install monitoring tools
sudo apt install htop iotop nmon

# Laravel Pulse (Laravel 11+)
composer require laravel/pulse
php artisan pulse:install
php artisan migrate
```

### Log Aggregation

```bash
# Install logrotate
sudo apt install logrotate

# Configure log rotation
/var/log/nginx/*.log {
    daily
    missingok
    rotate 52
    compress
    delaycompress
    notifempty
    create 644 www-data adm
    postrotate
        systemctl reload nginx
    endscript
}
```

### Automated Monitoring

```bash
# Health check script
#!/bin/bash
STATUS=$(curl -s -o /dev/null -w "%{http_code}" https://yourdomain.com/health)

if [ $STATUS -ne 200 ]; then
    echo "Health check failed with status $STATUS"
    # Send alert (email, Slack, etc.)
fi
```

This deployment guide provides a comprehensive setup for production environments. Always test deployments in staging before production, and implement proper monitoring and backup strategies.