FROM php:8.4-fpm-alpine

# Dépendances système
RUN apk --no-cache add \
    curl \
    git \
    wget \
    bash \
    dpkg \
    unzip \
    icu-dev \
    libzip-dev \
    oniguruma-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libxml2-dev \
    mariadb-dev

# Installer extensions PHP helper
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions

# Extensions PHP
RUN install-php-extensions \
    pdo_mysql \
    mysqli \
    opcache \
    intl \
    zip \
    gd \
    redis

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/bin/ \
    --filename=composer

WORKDIR /var/www