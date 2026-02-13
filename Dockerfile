# --- Stage 1: Build Backend (Composer) ---
FROM composer:2 AS composer

WORKDIR /app
COPY composer.json composer.lock ./

# Install base dependencies first
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --ignore-platform-reqs \
    --no-autoloader \
    --no-scripts

# Add Octane (Swoole is a PHP extension, not a composer package)
RUN composer require laravel/octane \
    --no-interaction \
    --ignore-platform-reqs \
    --no-scripts

# Generate optimized autoloader
RUN composer dump-autoload --optimize --no-scripts

# --- Stage 2: Build Frontend (Node/Vite) ---
FROM node:20-alpine AS frontend

WORKDIR /app

# Copy Composer dependencies (needed for vendor resources like Livewire/Flux)
COPY --from=composer /app/vendor/ ./vendor/

COPY package.json package-lock.json ./
RUN npm ci

COPY vite.config.js tailwind.config.js postcss.config.js ./
COPY resources ./resources
COPY public ./public
# Copy app folder for tailwind scanning
COPY app ./app

RUN npm run build

# --- Stage 3: Final Production Image ---
FROM php:8.4-cli-alpine

# Use the highly optimized extension installer (fetches pre-built binaries)
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# Install all extensions including Swoole (no compilation needed with this installer)
RUN install-php-extensions \
    swoole \
    pdo_mysql \
    pdo_pgsql \
    pgsql \
    pdo_sqlite \
    redis \
    pcntl \
    bcmath \
    intl \
    zip \
    opcache \
    gd \
    exif

WORKDIR /app

# Copy entrypoint
COPY docker/entrypoint.sh /usr/local/bin/entrypoint

# Copy Built Assets & Code
COPY --from=frontend /app/public/build ./public/build
COPY --from=composer /app/vendor ./vendor
COPY . .

# Setup Directories & Permissions
RUN mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache \
    && chmod +x /usr/local/bin/entrypoint

# Dynamic Production Settings
ENV APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    OCTANE_SERVER=swoole

EXPOSE 8000

ENTRYPOINT ["entrypoint"]
