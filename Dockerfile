FROM php:8.4-fpm

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    git unzip zip curl \
    libpq-dev libzip-dev libxslt1-dev libicu-dev \
    nginx \
    && docker-php-ext-install \
    pdo pdo_pgsql zip xsl intl

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-scripts

# 🔥 FIX IMPORTANT SYMFONY + RENDER
RUN mkdir -p var/cache var/log var/sessions \
    && mkdir -p /tmp/client_body /tmp/proxy /tmp/fastcgi \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 var

COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

ENV APP_ENV=prod
ENV APP_DEBUG=0
ENV PORT=10000

EXPOSE 10000

CMD sh -c "php-fpm -D && nginx -g 'daemon off;'"