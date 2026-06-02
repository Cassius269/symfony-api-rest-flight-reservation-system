# Image officielle PHP 8.4 avec PHP-FPM sur Alpine Linux
FROM php:8.4-fpm-alpine

# Installation des dépendances système nécessaires aux extensions PHP
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
    libxml2-dev

# Téléchargement de l'outil d'installation des extensions PHP
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

# Rendre le script exécutable
RUN chmod +x /usr/local/bin/install-php-extensions

# Installation des extensions PHP requises
RUN install-php-extensions \
    opcache \
    iconv \
    soap \
    zip \
    intl \
    fileinfo \
    pdo_mysql \
    mysqli \
    gd \
    redis

# Installation de Composer
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/bin/ \
    --filename=composer

# Répertoire de travail de l'application
WORKDIR /var/www