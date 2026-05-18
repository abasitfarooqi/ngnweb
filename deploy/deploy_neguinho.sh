#!/usr/bin/env bash
set -euo pipefail

APP_DIR="${APP_DIR:-/var/www/ngnmotors}"
cd "$APP_DIR"

echo "== COMPOSER =="
composer install --no-dev --optimize-autoloader --no-interaction

echo "== BUILD FRONTEND =="
npm ci
npm run build

echo "== OPTIMISE LARAVEL =="
php artisan config:cache
php artisan route:cache
php artisan view:clear

php artisan migrate --force

echo "== DONE =="
