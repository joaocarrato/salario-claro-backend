FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
    bash \
    git \
    icu-dev \
    libpq-dev \
    oniguruma-dev \
    unzip \
    zip \
    && docker-php-ext-install \
    bcmath \
    intl \
    mbstring \
    pdo \
    pdo_pgsql \
    pgsql

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY docker/php/entrypoint.sh /usr/local/bin/app-entrypoint
RUN chmod +x /usr/local/bin/app-entrypoint

ENTRYPOINT ["app-entrypoint"]
CMD ["php-fpm"]
