FROM php:8.4-apache

RUN docker-php-ext-install mysqli

COPY . /var/www/html/

EXPOSE 80