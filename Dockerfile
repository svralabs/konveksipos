FROM dunglas/frankenphp:1-php8.4-bookworm

# Install Node.js 22 and required PHP extensions for Laravel + Filament
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs git unzip \
    && install-php-extensions \
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

# 1. Install PHP dependencies first (so Filament CSS in vendor/ is present for Vite)
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# 2. Install NPM dependencies
COPY package*.json ./
RUN npm install

# 3. Copy application source code
COPY . .

# 4. Build Vite assets (now can access vendor/filament/filament CSS)
RUN npm run build

# Prepare database directory, sqlite DB, and set permissions
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
