FROM php:8.4-fpm

WORKDIR /var/www/html

# System dependencies
RUN apt-get update && apt-get install -y \
    git unzip zip curl \
    libpq-dev libzip-dev libxslt1-dev libicu-dev \
    nginx \
    && docker-php-ext-install \
    pdo \
    pdo_pgsql \
    zip \
    xsl \
    intl

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# App code
COPY . .

# Install dependencies (IMPORTANT: scripts ON)
RUN composer install --no-dev --optimize-autoloader

# Permissions
RUN chown -R www-data:www-data /var/www/html

# Nginx config
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# Env prod safe defaults
ENV APP_ENV=prod
ENV APP_DEBUG=0

# Render port (nginx listens on 10000)
EXPOSE 10000

# IMPORTANT: start PHP-FPM + nginx
CMD php-fpm -D && nginx -g "daemon off;"

RUN php bin/console cache:warmup --env=prod