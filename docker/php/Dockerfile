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

# Node.js for asset building
RUN apk add --no-cache nodejs npm

WORKDIR /var/www/html

# Copy app source
COPY . .

# Install prod deps only
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction

# Install and build assets
RUN npm install && npm run build && npm cache clean --force

# Keep Node.js for potential asset rebuilds in production
# RUN apk del nodejs npm

# Laravel optimizations
RUN php artisan config:clear \
 && php artisan config:cache \
 && php artisan route:cache \
 && php artisan view:cache

RUN chmod -R 755 storage bootstrap/cache public