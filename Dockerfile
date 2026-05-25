FROM php:8.2-fpm

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    git unzip zip \
    libpq-dev libzip-dev libxslt1-dev \
    && docker-php-ext-install pdo pdo_pgsql zip xsl

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-scripts

RUN php bin/console cache:clear --env=prod --no-debug

ENV APP_ENV=prod
ENV APP_DEBUG=0