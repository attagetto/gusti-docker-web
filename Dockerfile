FROM php:8.4-apache
RUN docker-php-ext-install pdo pdo_mysql
WORKDIR /var/www/html
COPY index.php /var/www/html/index.php
EXPOSE 80
