FROM jitesoft/composer:8.1 as composer

ADD . /build
WORKDIR /build

ENV APP_ENV prod
RUN composer install --no-dev -o

FROM php:8.1.0-apache

RUN apt-get update && apt-get upgrade -y && apt-get install -y \
      libzip-dev \
    && docker-php-ext-configure pdo_mysql --with-pdo-mysql=mysqlnd \
    && docker-php-ext-configure opcache --enable-opcache \
    && docker-php-ext-install \
      pdo_mysql \
      sockets \
      opcache \
      zip \
    && rm -rf /tmp/* \
    && rm -rf /var/list/apt/* \
    && rm -rf /var/lib/apt/lists/* \
    && apt-get clean

RUN a2enmod rewrite
RUN a2enmod headers

COPY ./docker/php.ini /usr/local/etc/php/php.ini
COPY ./docker/apache.conf /etc/apache2/sites-enabled/000-default.conf
COPY --chown=www-data:www-data . /var/www

COPY --from=composer --chown=www-data:www-data /build /var/www

WORKDIR /var/www

RUN rm -rf ./docker ./.ddev ./rector.php

RUN mkdir -p var/cache/prod var/cache/dev var/cache/test var/log \
   && chown -R www-data:www-data var/ \
   && chmod -R ug+rwX var/

CMD ["apache2-foreground"]
