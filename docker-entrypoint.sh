#!/bin/sh
set -e

# Clear and optimize config/routes/views
php artisan optimize:clear
php artisan package:discover --ansi || true

# Run database migrations for SQLite automatically
php artisan migrate --force --graceful || true

# Create storage symlink
php artisan storage:link || true

# Ensure permissions
chown -R www-data:www-data /app/storage /app/bootstrap/cache /app/database

exec "$@"
