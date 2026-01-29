#!/bin/sh
set -e

echo "✅ clearing old caches"
rm -f bootstrap/cache/*.php bootstrap/cache/*.tmp || true
php artisan optimize:clear || true

echo "✅ running migrations"
php artisan migrate --force || true

echo "✅ starting server from public/"
php -S 0.0.0.0:${PORT:-8000} -t public public/router.php




# set -e

# echo "✅ Clearing caches..."
# php artisan optimize:clear || true

# echo "✅ Running migrations..."
# php artisan migrate --force

# echo "✅ Caching config/routes..."
# php artisan config:cache || true
# php artisan route:cache || true

# echo "✅ Starting server..."
# php -S 0.0.0.0:${PORT:-8000} -t public public/index.php

