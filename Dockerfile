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

# App code
COPY . .

# Install dependencies (prod safe)
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Permissions FIX (important)
RUN mkdir -p var/cache var/log var/sessions \
    && chown -R www-data:www-data /var/www/html

# Nginx config
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# Env
ENV APP_ENV=prod
ENV APP_DEBUG=0
ENV PORT=10000

EXPOSE 10000

# Switch user BEFORE runtime
USER www-data

CMD sh -c "php-fpm -D && nginx -g 'daemon off;'"