FROM php:8.3-fpm

# Install dependencies sistem + Node.js
RUN apt-get update && apt-get install -y \
    git \
    zip \
    intl \
    unzip \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy source code
COPY . .

# Install dependency PHP
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Install dependency Node.js
RUN npm install

# Build frontend production
RUN npm run build

# Set permission storage dan cache
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Expose port
EXPOSE 9000

CMD php artisan serve --host=0.0.0.0 --port=8002