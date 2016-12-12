FROM php:fpm

RUN apt-get update \
    && apt-get install -y git

RUN docker-php-source extract \
    && docker-php-ext-install pdo_mysql \
    && docker-php-source delete

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR "/var/www/tws"