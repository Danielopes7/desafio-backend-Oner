FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    zip unzip curl libpng-dev libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN apt-get -y update \
&& apt-get install -y libicu-dev \
&& docker-php-ext-configure intl \
&& docker-php-ext-install intl \
&& docker-php-ext-install zip

# Install PHP extensions
RUN docker-php-ext-install exif pcntl bcmath gd sockets

WORKDIR /var/www/html

RUN test -f artisan || composer create-project laravel/laravel:^12.0 .

RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html
