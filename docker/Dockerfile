FROM php:8.2-apache

# benötigte PHP Extensions
RUN docker-php-ext-install pdo_mysql mysqli

# Apache: mod_rewrite (für .htaccess)
RUN a2enmod rewrite

# Session 12 Stunden gültig, damit am Abend niemand neu einloggen muss
COPY kfc-php.ini /usr/local/etc/php/conf.d/kfc-php.ini

WORKDIR /var/www/html
