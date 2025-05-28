#!/bin/sh
set -e

# Set Apache to listen on the port provided by Railway
if [ -n "$PORT" ]; then
  sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
  sed -i "s/:80/:${PORT}/" /etc/apache2/sites-available/000-default.conf
fi

echo "Running Laravel migrations..."
php artisan migrate --force || true

echo "Clearing and caching config & routes..."
php artisan config:cache
php artisan route:cache

echo "Starting Apache on port ${PORT:-80}..."
apache2-foreground
