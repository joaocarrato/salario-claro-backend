#!/usr/bin/env sh
set -e

if [ ! -f .env ]; then
    cp .env.example .env
fi

if [ ! -d vendor ]; then
    composer install --no-interaction --prefer-dist --optimize-autoloader
fi

if ! grep -q '^APP_KEY=base64:' .env; then
    php artisan key:generate --force --no-interaction
fi

php artisan config:clear --no-interaction >/dev/null 2>&1 || true

exec "$@"
