FROM php:8.0-fpm-alpine as base

RUN docker-php-ext-install pdo_mysql

# install configure file
RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini

WORKDIR /usr/share/nginx/html


FROM base as dev

RUN apk add php8-pecl-xdebug

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer


FROM base as prod
# settings
COPY settings/php.ini /usr/local/etc/php/conf.d/php.ini

# # app sources
COPY src /usr/share/nginx/html