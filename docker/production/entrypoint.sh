#!/bin/bash
set -e

echo "Syncing public assets to volume..."
rm -rf /var/www/html/public/build
cp -rp /var/www/html/public_fresh/. /var/www/html/public/

echo "Running Laravel setup tasks..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo "Running database migrations..."
# Run migrations, force is needed for production
php artisan migrate --force

echo "Linking storage..."
php artisan storage:link --force

echo "Starting PHP-FPM..."
exec "$@"
