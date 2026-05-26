FROM php:8.4-fpm

WORKDIR /var/www/html

# System deps
RUN apt-get update && apt-get install -y \
    git unzip zip curl \
    libpq-dev libzip-dev libxslt1-dev libicu-dev \
    nginx \
    && docker-php-ext-install \
    pdo pdo_pgsql zip xsl intl

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# App
COPY . .

# Composer install (prod safe)
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Permissions Symfony
RUN mkdir -p var/cache var/log var/sessions \
    && mkdir -p /tmp/client_body /tmp/proxy /tmp/fastcgi \
    && chown -R www-data:www-data /var/www/html

# Nginx config
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# Env prod
ENV APP_ENV=prod
ENV APP_DEBUG=0
ENV PORT=10000

EXPOSE 10000

# IMPORTANT: non-root
USER www-data

CMD sh -c "php-fpm -D && nginx -g 'daemon off;'"