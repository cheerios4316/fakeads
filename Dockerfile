FROM php:8.5-apache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN docker-php-ext-install pdo pdo_sqlite \
    && a2enmod rewrite

WORKDIR /var/www/html