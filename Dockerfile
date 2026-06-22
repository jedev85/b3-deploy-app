FROM php:8.4-cli

ENV APP_ENV=prod
ENV APP_DEBUG=0
ENV DEFAULT_URI=http://localhost
ENV COMPOSER_ALLOW_SUPERUSER=1

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libpq-dev \
        libicu-dev \
        libzip-dev \
    && docker-php-ext-install \
        intl \
        opcache \
        pdo_pgsql \
        zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

WORKDIR /app

COPY composer.json composer.lock symfony.lock ./
RUN composer install --no-dev --no-progress --no-interaction --no-scripts --optimize-autoloader \
    || composer install --no-dev --prefer-source --no-progress --no-interaction --no-scripts --optimize-autoloader

COPY . .
RUN composer dump-autoload --no-dev --classmap-authoritative \
    && mkdir -p var/cache var/log public/assets

EXPOSE 8000

CMD ["sh", "-c", "php bin/console cache:clear --env=prod --no-debug && php bin/console importmap:install --env=prod && php bin/console asset-map:compile --env=prod && php -S 0.0.0.0:${PORT:-8000} -t public"]
