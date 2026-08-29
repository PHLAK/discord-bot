# --------- BASE ----------

FROM php:8.5-apache AS base
LABEL maintainer="Chris Kankiewicz <Chris@Kankiewicz.com>"

EXPOSE 80
RUN a2enmod rewrite

ENV HOME="/tmp"
ENV COMPOSER_HOME="${HOME}/.config/composer"
ENV XDG_CONFIG_HOME="${HOME}/.config"

COPY --from=composer:2.9 /usr/bin/composer /usr/bin/composer

COPY .docker/apache2/config/000-default.dev.conf /etc/apache2/sites-available/000-default.conf
COPY .docker/php/config/php.dev.ini /usr/local/etc/php/php.ini

RUN apt-get update && apt-get install --assume-yes --no-install-recommends \
    git libicu-dev libmemcached-dev libssl-dev libzip-dev make zip zlib1g-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install intl pcntl pdo_mysql zip

RUN pecl install apcu \
    && pecl install memcached \
    && pecl install redis

RUN docker-php-ext-enable apcu memcached redis

# --------- BUILD ----------

FROM base AS build

COPY ./ /var/www/html
WORKDIR /var/www/html

RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

RUN make production

# --------- PROD ----------

FROM base AS prod

COPY .docker/apache2/config/000-default.prod.conf /etc/apache2/sites-available/000-default.conf
COPY .docker/php/config/php.prod.ini /usr/local/etc/php/php.ini

COPY --from=build /var/www/html /var/www/html
RUN chown --recursive www-data:www-data /var/www/html

# --------- DEV ----------

FROM prod AS dev

COPY .docker/apache2/config/000-default.dev.conf /etc/apache2/sites-available/000-default.conf
COPY .docker/php/config/php.dev.ini /usr/local/etc/php/php.ini

RUN pecl install xdebug
RUN docker-php-ext-enable xdebug