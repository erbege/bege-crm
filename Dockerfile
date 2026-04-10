# ---------- Stage 1: PHP dependencies ----------
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-scripts --optimize-autoloader

# ---------- Stage 2: Node assets ----------
FROM node:20-alpine AS assets
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

# ---------- Stage 3: Runtime ----------
FROM php:8.3-apache

RUN apt-get update && apt-get install -y \
    libzip-dev libpng-dev libonig-dev libxml2-dev unzip git curl \
    && docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html
COPY . /var/www/html
COPY --from=vendor /app/vendor /var/www/html/vendor
COPY --from=assets /app/public/build /var/www/html/public/build

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/000-default.conf /etc/apache2/apache2.conf \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80
CMD ["apache2-foreground"]
