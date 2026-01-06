# syntax = docker/dockerfile:1

# Base image with PHP 8.2 CLI (not FPM, we'll use built-in server)
FROM php:8.2-cli-alpine AS base

WORKDIR /var/www/html

# Install system dependencies and PHP extensions
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    oniguruma-dev \
    postgresql-dev \
    mysql-dev \
    nodejs \
    npm \
    bash

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy composer files first for better caching
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Copy package files
COPY package.json package-lock.json ./

# Install Node dependencies and build assets
RUN npm ci && npm run build && rm -rf node_modules

# Copy the rest of the application
COPY --chown=www-data:www-data . /var/www/html

# Run post-install scripts
RUN composer dump-autoload --optimize

# Set permissions for storage and cache directories
RUN mkdir -p storage/framework/{sessions,views,cache/data} \
    && mkdir -p storage/logs \
    && mkdir -p bootstrap/cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage \
    && chmod -R 775 bootstrap/cache

# Production stage
FROM base AS production

# Switch to non-root user
USER www-data

EXPOSE 8080

# Use PHP's built-in server for Fly.io
CMD php artisan serve --host=0.0.0.0 --port=8080

