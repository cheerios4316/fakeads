FROM dunglas/frankenphp:php8.5-alpine

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN apk add --no-cache sqlite \
 && docker-php-ext-install pdo pdo_sqlite

WORKDIR /app