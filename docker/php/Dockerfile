FROM php:8.3-cli-alpine

# Install system dependencies
RUN apk add --no-cache \
    bash \
    curl \
    libpng-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    build-base \
    autoconf \
    openssl-dev \
    supervisor

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql gd zip opcache pcntl sockets

# Install Swoole extension for Laravel Octane
RUN pecl install swoole && docker-php-ext-enable swoole

# Get Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Expose Octane port
EXPOSE 8000

# Set entry point script
COPY ./docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]
