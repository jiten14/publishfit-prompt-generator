FROM php:8.3-cli

# Safe defaults baked directly into the image - Laravel 11+'s own
# defaults are SESSION_DRIVER=database / CACHE_STORE=database, which
# crashes here since there's no real database and no sessions/cache
# table to read from. These ENV values are still fully overridden by
# anything set in Render's dashboard (or any other host's own env
# vars) - this is just a safe fallback, not a hard lock.
ENV SESSION_DRIVER=file
ENV CACHE_STORE=file
ENV QUEUE_CONNECTION=sync

# Split from the extension-install step below on purpose - if either
# step ever fails again, the build log will point at exactly one of the
# two, rather than a single combined command hiding which part broke.
RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libzip-dev \
        zlib1g-dev \
        libsqlite3-dev \
    && rm -rf /var/lib/apt/lists/*

# pdo itself is already built into the base php image - only
# pdo_sqlite genuinely needs installing. zlib1g-dev above is what zip
# actually needs to compile against; libzip-dev alone isn't always
# enough on this image.
RUN docker-php-ext-install pdo_sqlite zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction

RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 10000

ENTRYPOINT ["docker-entrypoint.sh"]