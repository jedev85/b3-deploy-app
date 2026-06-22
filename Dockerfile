FROM php:8.4-cli

ENV APP_ENV=prod
ENV APP_DEBUG=0
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
RUN composer install --no-dev --prefer-dist --no-progress --no-interaction --no-scripts --optimize-autoloader

COPY . .
RUN APP_SECRET=build-time-secret composer dump-autoload --no-dev --classmap-authoritative \
    && APP_SECRET=build-time-secret composer run-script post-install-cmd \
    && APP_SECRET=build-time-secret php bin/console asset-map:compile

EXPOSE 8000

CMD ["sh", "-c", "php -S 0.0.0.0:${PORT:-8000} -t public"]
