#!/bin/sh
set -e

# Ensure SQLite database exists
mkdir -p /app/database
touch /app/database/database.sqlite
chown -R www-data:www-data /app/storage /app/bootstrap/cache /app/database
chmod -R 775 /app/storage /app/bootstrap/cache /app/database

# 1. Run migrations first so cache/sessions/tables exist
php artisan migrate --force --graceful || true

# 2. Link storage
php artisan storage:link || true

# 3. Cache configuration & routes for production speed
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

exec "$@"
