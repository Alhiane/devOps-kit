# syntax=docker/dockerfile:1

################################################################################
# Stage 1: Install Composer dependencies
FROM composer:lts as deps

WORKDIR /app

RUN --mount=type=bind,source=composer.json,target=composer.json \
    --mount=type=bind,source=composer.lock,target=composer.lock \
    --mount=type=cache,target=/tmp/cache \
    composer install --no-dev --no-interaction --no-scripts

################################################################################
# Stage 2: Runtime image
FROM php:8.3-apache as final

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y libpq-dev \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install pdo pdo_pgsql

# Enable mod_rewrite for Laravel routing
RUN a2enmod rewrite

# Point DocumentRoot to Laravel's public/ directory
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' \
    /etc/apache2/sites-available/000-default.conf

# Allow .htaccess overrides inside public/
COPY <<EOF /etc/apache2/conf-available/laravel.conf
<Directory /var/www/html/public>
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>
EOF
RUN a2enconf laravel

# Use the production PHP configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Copy the full Laravel project
COPY . /var/www/html

# Copy vendor from the deps stage (overrides any local vendor/)
COPY --from=deps /app/vendor/ /var/www/html/vendor

# Regenerate the package discovery cache based on installed (non-dev) packages
RUN php /var/www/html/artisan package:discover --ansi

# Fix permissions for writable directories
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Run as non-privileged user
USER www-data
