FROM php:8.4-apache

RUN a2dismod mpm_event mpm_worker && a2enmod mpm_prefork && docker-php-ext-install mysqli

COPY . /var/www/html/

EXPOSE 80
