FROM php:8.4-fpm

WORKDIR /var/www/html

# Install system dependencies
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

# Copy app
COPY . .

# Install PHP dependencies (NO scripts here → évite cache:clear)
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Permissions
RUN chown -R www-data:www-data /var/www/html

# Nginx config
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# ⚠️ IMPORTANT: ne pas exécuter cache:clear au build sans env runtime
# (DATABASE_URL n'existe pas encore ici)

ENV APP_ENV=prod
ENV APP_DEBUG=0

# Render expects a PORT
ENV PORT=10000

EXPOSE 10000

# Start both services correctly
CMD sh -c "php-fpm -D && nginx -g 'daemon off;'"
