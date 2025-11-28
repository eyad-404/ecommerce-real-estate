# =====================
# Base image
# =====================
FROM php:8.2-fpm

# =====================
# Set working directory
# =====================
WORKDIR /var/www/html

# =====================
# Install system dependencies
# =====================
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    curl \
    && docker-php-ext-install pdo_mysql mbstring zip exif pcntl gd \
    && rm -rf /var/lib/apt/lists/*

# =====================
# Install Composer
# =====================
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# =====================
# Copy only composer files first (for caching)
# =====================
COPY composer.json composer.lock ./

# =====================
# Install PHP dependencies
# =====================
RUN composer install --no-dev --optimize-autoloader

# =====================
# Copy the rest of the application
# =====================
COPY . .

# =====================
# Permissions
# =====================
RUN mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache \
    && chmod -R a+rw storage bootstrap/cache

# =====================
# Expose port 10000 (Render listens on this)
# =====================
EXPOSE 10000

# =====================
# Start command
# =====================
CMD ["php", "-S", "0.0.0.0:10000", "-t", "public"]
