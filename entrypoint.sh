#!/bin/bash
set -euo pipefail

mkdir -p storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
find storage -type d -exec chmod 2775 {} \;
find storage -type f -exec chmod 664 {} \;
chmod 2775 bootstrap/cache
touch storage/logs/laravel.log || true
chown www-data:www-data storage/logs/laravel.log || true
chmod 664 storage/logs/laravel.log || true

run_as_wwwdata() {
    runuser -u www-data -- "$@"
}

if [[ -z "${APP_KEY:-}" ]]; then
    export APP_KEY=$(echo "base64:$(openssl rand -base64 32)")
fi

run_as_wwwdata php artisan migrate --force --no-interaction
run_as_wwwdata php artisan db:seed --force --no-interaction

if [[ "${APP_ENV:-production}" = "production" ]]; then
    run_as_wwwdata php artisan config:cache || true
    run_as_wwwdata php artisan route:cache  || true
    run_as_wwwdata php artisan view:cache   || true
    run_as_wwwdata php artisan filament:optimize || true
    run_as_wwwdata php artisan defender:auto true || true
fi

exec /usr/sbin/apache2ctl -D FOREGROUND
