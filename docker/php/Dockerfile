FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
    icu-dev \
    oniguruma-dev \
    libzip-dev \
    mysql-client \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev

RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg \
 && docker-php-ext-install \
    pdo_mysql \
    mbstring \
    intl \
    zip \
    pcntl \
    gd

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/bin \
    --filename=composer

WORKDIR /var/www/html

# Copy app source
COPY . .

# Install prod deps only
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction

# Laravel optimizations
RUN php artisan config:clear \
 && php artisan config:cache \
 && php artisan route:cache \
 && php artisan view:cache

RUN chmod -R 755 storage bootstrap/cache