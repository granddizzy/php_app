FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    nano \
    && docker-php-ext-install pdo pdo_mysql

VOLUME /code

WORKDIR /code

