FROM php:8.2-apache

# Install system deps + PHP extensions
RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev libonig-dev \
    && docker-php-ext-install zip mbstring \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Create Laravel project
WORKDIR /var/www
RUN composer create-project laravel/laravel html "11.*" --prefer-dist --no-interaction

WORKDIR /var/www/html

# Apache: point to /public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && echo '<Directory /var/www/html/public>\n    AllowOverride All\n</Directory>' > /etc/apache2/conf-available/laravel.conf \
    && a2enconf laravel

# Copy our custom app files on top of fresh Laravel
COPY src/routes/web.php ./routes/web.php
COPY src/app/Http/Controllers/ ./app/Http/Controllers/
COPY src/app/Http/Middleware/ ./app/Http/Middleware/
COPY src/app/Services/ ./app/Services/
COPY src/resources/views/ ./resources/views/
COPY src/bootstrap/app.php ./bootstrap/app.php

# Generate app key and set permissions
RUN php artisan key:generate
RUN mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache/data
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html
RUN chmod -R 775 storage bootstrap/cache

EXPOSE 80
