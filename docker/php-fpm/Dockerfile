FROM php:8.4.5-fpm

RUN docker-php-ext-install pdo_mysql \
    # Do not enable xdebug by default due to performance impact, enable on demand via ./docker/manager
    && pecl install xdebug

COPY ./php-fpm/php.ini /usr/local/etc/php/conf.d/php.ini
