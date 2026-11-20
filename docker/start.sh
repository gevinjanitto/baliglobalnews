#!/bin/sh
set -e
cd /app
php artisan config:clear
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link >/dev/null 2>&1 || true
php artisan config:cache
php artisan route:cache
php artisan view:cache
cd public
exec php -S 0.0.0.0:${PORT:-8080} ../vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php
