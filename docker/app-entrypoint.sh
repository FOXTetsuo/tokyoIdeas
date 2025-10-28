#!/bin/sh
set -e
cd /var/www/html

# Ensure .env exists and APP_KEY
if [ ! -f .env ]; then
  if [ -f .env.example ]; then
    cp .env.example .env
  else
    touch .env
  fi
fi
if ! grep -q '^APP_KEY=' .env || [ "$(grep '^APP_KEY=' .env | cut -d'=' -f2)" = "" ]; then
  php artisan key:generate --force || true
fi

# Install PHP dependencies if vendor missing
if [ ! -f vendor/autoload.php ]; then
  composer install --no-interaction --optimize-autoloader --no-dev || true
fi

# Build frontend assets if manifest missing
if [ ! -f public/build/manifest.json ]; then
  if [ -f package.json ]; then
    # Try yarn first, fallback to npm
    if command -v yarn >/dev/null 2>&1; then
      yarn install --silent || true
      yarn build --silent || true
    else
      npm install --silent || true
      npm run build --silent || true
    fi
  fi
fi

# Ensure permissions for storage and cache
chown -R www-data:www-data storage bootstrap/cache || true
chmod -R 775 storage bootstrap/cache || true

# Exec supervisord as in the Dockerfile
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
