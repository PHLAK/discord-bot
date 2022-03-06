FROM php:8.1-apache
LABEL maintainer="Chris Kankiewicz <Chris@ChrisKankiewicz.com>"

COPY --from=composer:2.1 /usr/bin/composer /usr/bin/composer
COPY --from=node:16.13 /usr/local/bin/node /usr/local/bin/node
COPY --from=node:16.13 /usr/local/lib/node_modules /usr/local/lib/node_modules

RUN ln --symbolic ../lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm
RUN ln --symbolic ../lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx

ENV HOME="/tmp"
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_HOME="/tmp"
ENV GNUPGHOME="/tmp"
ENV XDG_CONFIG_HOME="/tmp/.config"

RUN a2enmod rewrite

RUN apt-get update && apt-get --assume-yes install libicu-dev libpng-dev libzip-dev \
    mariadb-client sqlite3 tzdata && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure intl \
    && docker-php-ext-install bcmath gd intl pcntl pdo_mysql zip \
    && pecl install redis xdebug && docker-php-ext-enable redis xdebug

COPY ./.docker/php/config/php.ini /usr/local/etc/php/php.ini
COPY ./.docker/apache2/config/000-default.conf /etc/apache2/sites-available/000-default.conf
