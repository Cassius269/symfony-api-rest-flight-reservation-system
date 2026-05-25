# Image PHP 8.3 FPM basée sur Alpine (OS léger)
FROM php:8.4-fpm-alpine 

#installer les dépendances
RUN apk --no-cache add curl git wget bash dpkg

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
# Autoriser la modification du fichier des extensions
RUN chmod +x /usr/local/bin/install-php-extensions

# Installer les extensions PHP
RUN install-php-extensions opcache iconv soap
RUN install-php-extensions zip intl fileinfo
RUN install-php-extensions pdo redis mysqli pdo_mysql
RUN install-php-extensions gd

# Installer composer (gestionnaire de bundles sur Symfony)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

# Définir le dossier de travail où se seront executées les commandes de ce Dockerfile
WORKDIR /var/www