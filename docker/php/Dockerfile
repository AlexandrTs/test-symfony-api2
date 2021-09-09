FROM php:8.0.5-alpine

RUN apk update \
  && apk add ca-certificates wget \
  && update-ca-certificates

RUN docker-php-ext-install mysqli \
  && docker-php-ext-install pdo_mysql

