# Deployment Documentation

## Overview
This document outlines the deployment process, server requirements, and DevOps practices for the Dolce application.

## Server Requirements

### 1. System Requirements
- PHP 8.1 or higher
- MySQL 8.0 or higher
- Nginx/Apache web server
- Composer
- Node.js and NPM
- Redis (optional, for caching)

### 2. PHP Extensions
```ini
extension=bcmath
extension=ctype
extension=fileinfo
extension=json
extension=mbstring
extension=openssl
extension=pdo_mysql
extension=tokenizer
extension=xml
```

## Deployment Process

### 1. Initial Setup
```bash
# Clone repository
git clone https://github.com/your-org/dolce.git
cd dolce

# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Install NPM dependencies
npm install
npm run production

# Set up environment
cp .env.example .env
php artisan key:generate

# Configure database
php artisan migrate
php artisan db:seed

# Set up storage
php artisan storage:link
```

### 2. Environment Configuration
```env
APP_NAME=Dolce
APP_ENV=production
APP_DEBUG=false
APP_URL=https://www.dolce.pet

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dolce
DB_USERNAME=dolce_user
DB_PASSWORD=secure_password

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

## Server Configuration

### 1. Nginx Configuration
```nginx
server {
    listen 80;
    server_name www.dolce.pet dolce.pet;
    root /var/www/dolce/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 2. Supervisor Configuration
```ini
[program:dolce-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/dolce/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=8
redirect_stderr=true
stdout_logfile=/var/www/dolce/storage/logs/worker.log
stopwaitsecs=3600
```

## Deployment Scripts

### 1. Deploy Script
```bash
#!/bin/bash

# Pull latest changes
git pull origin main

# Install dependencies
composer install --no-dev --optimize-autoloader
npm install
npm run production

# Clear caches
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
php artisan migrate --force

# Restart queue workers
supervisorctl restart dolce-worker:*
```

### 2. Rollback Script
```bash
#!/bin/bash

# Revert to previous version
git reset --hard HEAD^
git clean -fd

# Restore database
php artisan migrate:rollback

# Clear caches
php artisan optimize:clear

# Restart services
supervisorctl restart dolce-worker:*
```

## Monitoring

### 1. Health Checks
```php
// routes/api.php
Route::get('health', function () {
    return response()->json([
        'status' => 'healthy',
        'services' => [
            'database' => DB::connection()->getPdo() ? 'connected' : 'disconnected',
            'redis' => Redis::ping() ? 'connected' : 'disconnected',
            'queue' => Queue::size() < 1000 ? 'healthy' : 'overloaded'
        ]
    ]);
});
```

### 2. Logging
```php
// config/logging.php
'channels' => [
    'stack' => [
        'driver' => 'stack',
        'channels' => ['daily', 'slack'],
    ],
    'daily' => [
        'driver' => 'daily',
        'path' => storage_path('logs/laravel.log'),
        'level' => env('LOG_LEVEL', 'debug'),
        'days' => 14,
    ],
    'slack' => [
        'driver' => 'slack',
        'url' => env('LOG_SLACK_WEBHOOK_URL'),
        'username' => 'Dolce Logger',
        'emoji' => ':boom:',
        'level' => 'error',
    ],
],
```

## Backup Strategy

### 1. Database Backup
```bash
#!/bin/bash

# Daily backup
mysqldump -u dolce_user -p dolce > /backups/dolce_$(date +%Y%m%d).sql

# Compress backup
gzip /backups/dolce_$(date +%Y%m%d).sql

# Keep only last 7 days
find /backups -name "dolce_*.sql.gz" -mtime +7 -delete
```

### 2. File Backup
```bash
#!/bin/bash

# Backup storage
tar -czf /backups/storage_$(date +%Y%m%d).tar.gz /var/www/dolce/storage

# Keep only last 7 days
find /backups -name "storage_*.tar.gz" -mtime +7 -delete
```

## Security

### 1. SSL Configuration
```nginx
server {
    listen 443 ssl http2;
    server_name www.dolce.pet dolce.pet;

    ssl_certificate /etc/letsencrypt/live/dolce.pet/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/dolce.pet/privkey.pem;
    ssl_session_timeout 1d;
    ssl_session_cache shared:SSL:50m;
    ssl_session_tickets off;

    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384:ECDHE-ECDSA-CHACHA20-POLY1305:ECDHE-RSA-CHACHA20-POLY1305:DHE-RSA-AES128-GCM-SHA256:DHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers off;

    # HSTS
    add_header Strict-Transport-Security "max-age=63072000" always;
}
```

### 2. Security Headers
```php
// app/Http/Middleware/SecurityHeaders.php
public function handle($request, Closure $next)
{
    $response = $next($request);

    $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
    $response->headers->set('X-Content-Type-Options', 'nosniff');
    $response->headers->set('X-XSS-Protection', '1; mode=block');
    $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
    $response->headers->set('Content-Security-Policy', "default-src 'self'");

    return $response;
}
```

## Performance Optimization

### 1. Cache Configuration
```php
// config/cache.php
'redis' => [
    'client' => env('REDIS_CLIENT', 'phpredis'),
    'default' => [
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'password' => env('REDIS_PASSWORD', null),
        'port' => env('REDIS_PORT', 6379),
        'database' => env('REDIS_DB', 0),
    ],
    'cache' => [
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'password' => env('REDIS_PASSWORD', null),
        'port' => env('REDIS_PORT', 6379),
        'database' => env('REDIS_CACHE_DB', 1),
    ],
],
```

### 2. Queue Configuration
```php
// config/queue.php
'redis' => [
    'driver' => 'redis',
    'connection' => 'default',
    'queue' => env('REDIS_QUEUE', 'default'),
    'retry_after' => 90,
    'block_for' => null,
],
```

## Disaster Recovery

### 1. Backup Restoration
```bash
#!/bin/bash

# Restore database
gunzip -c /backups/dolce_20240101.sql.gz | mysql -u dolce_user -p dolce

# Restore files
tar -xzf /backups/storage_20240101.tar.gz -C /var/www/dolce/
```

### 2. Failover Configuration
```nginx
upstream backend {
    server 192.168.1.10:80;
    server 192.168.1.11:80 backup;
}

server {
    listen 80;
    server_name www.dolce.pet dolce.pet;

    location / {
        proxy_pass http://backend;
        proxy_next_upstream error timeout http_500;
        proxy_next_upstream_tries 3;
    }
}
```

## Monitoring and Alerts

### 1. Server Monitoring
```yaml
# prometheus.yml
scrape_configs:
  - job_name: 'dolce'
    static_configs:
      - targets: ['localhost:9100']
    metrics_path: '/metrics'
```

### 2. Alert Configuration
```yaml
# alertmanager.yml
route:
  group_by: ['alertname']
  group_wait: 30s
  group_interval: 5m
  repeat_interval: 4h
  receiver: 'slack-notifications'

receivers:
- name: 'slack-notifications'
  slack_configs:
  - api_url: 'https://hooks.slack.com/services/your-webhook-url'
    channel: '#alerts'
```

## Scaling

### 1. Horizontal Scaling
```nginx
upstream dolce {
    server 192.168.1.10:80;
    server 192.168.1.11:80;
    server 192.168.1.12:80;
}

server {
    listen 80;
    server_name www.dolce.pet dolce.pet;

    location / {
        proxy_pass http://dolce;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
    }
}
```

### 2. Database Scaling
```ini
# my.cnf
[mysqld]
innodb_buffer_pool_size = 4G
innodb_log_file_size = 512M
innodb_flush_log_at_trx_commit = 2
innodb_flush_method = O_DIRECT
```

## Maintenance

### 1. Regular Maintenance
```bash
#!/bin/bash

# Clear old logs
find /var/www/dolce/storage/logs -name "*.log" -mtime +30 -delete

# Optimize database
mysql -u dolce_user -p dolce -e "OPTIMIZE TABLE pets, bookings, users;"

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### 2. Update Process
```bash
#!/bin/bash

# Pull latest changes
git pull origin main

# Update dependencies
composer update --no-dev
npm update

# Run migrations
php artisan migrate --force

# Clear caches
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart services
supervisorctl restart dolce-worker:*
``` 