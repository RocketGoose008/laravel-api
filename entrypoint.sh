#!/bin/sh
set -e

echo "Running Laravel migrations..."
php artisan migrate --force

echo "Clearing and caching config & routes..."
php artisan config:cache
php artisan route:cache

echo "Starting Apache in foreground..."
apache2-foreground
