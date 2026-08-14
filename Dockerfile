# Stage 1: Build Frontend Assets
FROM node:22-alpine AS frontend
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# Stage 2: Production FrankenPHP with PHP 8.4
FROM dunglas/frankenphp:1-php8.4-bookworm

# Install required PHP extensions for Laravel + Filament (intl, zip, gd, sqlite, etc.)
RUN install-php-extensions \
    pdo \
    pdo_sqlite \
    pdo_mysql \
    intl \
    zip \
    gd \
    bcmath \
    exif \
    pcntl \
    opcache

WORKDIR /app

# Copy Composer binary
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application source code
COPY . .

# Copy compiled assets from frontend stage
COPY --from=frontend /app/public/build ./public/build

# Install PHP dependencies without dev packages
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Prepare directories, sqlite DB, and permissions
RUN mkdir -p database storage bootstrap/cache \
    && touch database/database.sqlite \
    && chown -R www-data:www-data /app \
    && chmod -R 775 storage bootstrap/cache database

# Entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

ENV SERVER_NAME=":80"
ENV APP_ENV=production

EXPOSE 80

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
