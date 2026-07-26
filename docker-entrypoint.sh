#!/bin/sh
set -e

# config:cache must run here (container start), not during the Docker
# build step - Render only provides your real environment variables
# (OPENAI_API_KEY, APP_KEY, etc.) at runtime, not at build time. Caching
# too early would bake in blank values.
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Render assigns a port dynamically via $PORT and expects the container
# to listen on exactly that port - 10000 below is only a local-testing
# fallback if $PORT isn't set.
php artisan serve --host=0.0.0.0 --port="${PORT:-10000}"