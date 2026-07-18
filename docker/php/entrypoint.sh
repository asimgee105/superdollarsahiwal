#!/bin/bash
set -e

# Copy .env if not exists
if [ ! -f .env ]; then
    cp .env.example .env
fi

# Ensure application key is set
if ! grep -q "APP_KEY=base64" .env; then
    php artisan key:generate
fi

# Start Supervisor or start Octane directly
echo "Starting Laravel Octane on Swoole..."
exec php artisan octane:start --server=swoole --host=0.0.0.0 --port=8000 --watch
