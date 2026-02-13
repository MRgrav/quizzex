#!/bin/sh
set -e

# Default settings
RUN_MIGRATIONS=${RUN_MIGRATIONS:-true}
OCTANE_SERVER=${OCTANE_SERVER:-swoole}
HOST=${HOST:-0.0.0.0}
PORT=${PORT:-8000}

# Run migrations if enabled
if [ "$RUN_MIGRATIONS" = "true" ]; then
    echo "Running database migrations..."
    php artisan migrate --force
else
    echo "Skipping database migrations (RUN_MIGRATIONS set to false)..."
fi

# Clear caches
echo "Clearing caches..."
php artisan optimize:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimize
echo "Optimizing application..."
php artisan optimize
php artisan config:cache
php artisan route:cache

# Start Octane with configured server
echo "Starting Octane with server: $OCTANE_SERVER..."

exec php artisan octane:start --server="$OCTANE_SERVER" --host="$HOST" --port="$PORT"
