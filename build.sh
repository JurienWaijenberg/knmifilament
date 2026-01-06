#!/bin/bash
set -e

echo "Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

echo "Installing npm dependencies..."
npm ci

echo "Building assets..."
npm run build

echo "Build complete!"

