FROM php:8.4-fpm-alpine

# =========================
# Dépendances système
# =========================
RUN apk add --no-cache \
    bash \
    curl \
    git \
    unzip \
    icu-dev \
    libzip-dev \
    oniguruma-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libxml2-dev \
    mariadb-dev \
    $PHPIZE_DEPS

# =========================
# Extensions PHP (CRITIQUES)
# =========================
RUN docker-php-ext-install \
    pdo_mysql \
    mysqli \
    intl \
    zip \
    opcache

RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd

# =========================
# Composer
# =========================
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin \
    --filename=composer

# =========================
# Nettoyage (optionnel mais propre)
# =========================
RUN rm -rf /tmp/* /var/cache/apk/*

# =========================
# Workdir
# =========================
WORKDIR /var/www/html

# =========================
# Vérification (debug build)
# =========================
RUN php -m | grep pdo_mysql