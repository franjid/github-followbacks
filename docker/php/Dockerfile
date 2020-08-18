FROM composer as backend

WORKDIR /code/app

COPY ./code/app/composer.json ./code/app/composer.lock /code/app/
RUN composer install

FROM php:7.4-fpm

WORKDIR /code/app

COPY --from=backend /code/app /code/app/
COPY ./docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini
