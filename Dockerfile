FROM php:8.0.12-apache

RUN apt-get update && apt-get upgrade -y && apt-get install -y \
      libzip-dev \
    && docker-php-ext-configure pdo_mysql --with-pdo-mysql=mysqlnd \
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

COPY ./docker/php.ini /usr/local/etc/php/php.ini
COPY ./docker/apache.conf /etc/apache2/sites-enabled/000-default.conf
COPY --chown=www-data:www-data . /var/www

WORKDIR /var/www

RUN mkdir -p var/cache/prod var/cache/dev var/cache/test var/log \
   && chown -R www-data:www-data var/ \
   && chmod -R ug+rwX var/

CMD ["apache2-foreground"]