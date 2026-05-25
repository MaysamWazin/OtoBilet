FROM php:8.2-fpm

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    default-mysql-client \
    libpng-dev \
    zip \
    unzip

RUN docker-php-ext-install pdo pdo_mysql