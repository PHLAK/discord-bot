FROM php:8.4-apache
LABEL maintainer="Chris Kankiewicz <Chris@ChrisKankiewicz.com>"

COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

ENV HOME="/home/dev"
ENV COMPOSER_HOME="${HOME}/.config/composer"
ENV XDG_CONFIG_HOME="${HOME}/.config"

RUN useradd --create-home --shell /bin/bash dev

RUN a2enmod rewrite

RUN apt-get update && apt-get --assume-yes install libicu-dev libpng-dev libzip-dev \
    mariadb-client sqlite3 tzdata && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure intl \
    && docker-php-ext-install bcmath gd intl pcntl pdo_mysql zip \
    && pecl install redis xdebug && docker-php-ext-enable redis xdebug

COPY ./.docker/php/config/php.ini /usr/local/etc/php/php.ini
COPY ./.docker/apache2/config/000-default.conf /etc/apache2/sites-available/000-default.conf

USER dev
