ARG PHP_VERSION=7.4

FROM php:${PHP_VERSION}-fpm-alpine

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/
COPY --from=composer:1 /usr/bin/composer /usr/bin/composer

RUN install-php-extensions apcu bz2 gd intl opcache pdo_mysql zip bcmath
RUN mv $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini

RUN apk --no-cache add bash git mysql-client unzip

ENV PATH="/srv/app/vendor/bin:/srv/app/bin:${PATH}"

WORKDIR /srv/app
