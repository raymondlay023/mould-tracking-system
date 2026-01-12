FROM php:8.3-cli-alpine

# Install system + build dependencies
RUN apk update && apk add --no-cache \
    $PHPIZE_DEPS \
    bash \
    git \
    unzip \
    icu-dev \
    oniguruma-dev \
    libzip-dev \
    mysql-client \
    curl

# PHP extensions
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    mbstring \
    intl \
    zip \
    pcntl

# Install Composer (safe method)
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/bin \
    --filename=composer

WORKDIR /var/www/html

EXPOSE 8000