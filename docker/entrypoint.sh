#!/bin/sh
set -e

# Default PORT if not provided by Render
PORT=${PORT:-8080}
export PORT

# Configure Nginx port dynamically
sed -i "s/\${PORT}/$PORT/g" /etc/nginx/nginx.conf

# Set permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Production optimizations if APP_ENV is production
if [ "$APP_ENV" = "production" ]; then
    echo "Running in production mode..."
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

# Run database migrations and seeders if RUN_MIGRATIONS=true
if [ "$RUN_MIGRATIONS" = "true" ]; then
    echo "Running database migrations..."
    php artisan migrate --force || true
    echo "Running database seeders..."
    php artisan db:seed --force || true
fi

echo "Starting PHP-FPM..."
php-fpm -D

echo "Starting Nginx on port $PORT..."
exec nginx -g "daemon off;"
