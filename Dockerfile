ARG PHP_VERSION=${PHP_VERSION:-8.2}
FROM php:${PHP_VERSION}-fpm-alpine AS php-system-setup

# Install system dependencies
RUN apk update && apk add dcron busybox-suid curl libcap zip unzip git php-pcntl

# Install PHP extensions
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/
RUN install-php-extensions zip pdo_pgsql pcntl
RUN install-php-extensions gd

# Install composer
COPY --from=composer/composer:2 /usr/bin/composer /usr/local/bin/composer

FROM php-system-setup AS app-setup

# Set working directory
ENV LARAVEL_PATH=/var/www/html
WORKDIR $LARAVEL_PATH

# Switch to non-root 'app' user & install app dependencies
COPY composer.* ./

# Copy app
COPY . $LARAVEL_PATH/

EXPOSE 80
