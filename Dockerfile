FROM php:8.4-fpm

WORKDIR /var/www/html

RUN apk add --no-cache \
    bash curl git unzip icu-dev libzip-dev \
    libpng-dev libjpeg-turbo-dev freetype-dev \
    libxml2-dev mariadb-dev

RUN docker-php-ext-install \
    pdo_mysql \
    mysqli

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copier tous les fichiers du projet dans le conteneur  
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