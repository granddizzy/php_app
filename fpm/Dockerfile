FROM php:8.2-fpm

COPY ./zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf
COPY ./php.ini /usr/local/etc/php/conf.d/php-custom.ini

VOLUME /code

# Установка зависимостей
RUN apt-get update && apt-get install -y \
    libmemcached-dev \
    zlib1g-dev

# Установка и активация расширения memcache
RUN pecl install memcache && docker-php-ext-enable memcache

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ENV COMPOSER_ALLOW_SUPERUSER 1

RUN apt-get update && apt-get install zip unzip

RUN docker-php-ext-install pdo pdo_mysql

WORKDIR /code

CMD ["php-fpm"]