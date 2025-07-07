FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    zip unzip curl libpng-dev libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

RUN test -f artisan || composer create-project laravel/laravel:^12.0 .

RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html
