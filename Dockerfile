# =====================
# 1️⃣ Base image
# =====================
FROM php:8.2-fpm

# =====================
# 2️⃣ System dependencies
# =====================
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libonig-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libxml2-dev \
    curl \
    && docker-php-ext-install pdo_mysql mbstring zip exif pcntl gd xml \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# =====================
# 3️⃣ Set working directory
# =====================
WORKDIR /var/www/html

# =====================
# 4️⃣ Copy composer files first (cache optimization)
# =====================
COPY composer.json composer.lock ./

# =====================
# 5️⃣ Install PHP dependencies
# =====================
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');" \
    && composer install --no-dev --optimize-autoloader

# =====================
# 6️⃣ Copy all project files
# =====================
COPY . .

# =====================
# 7️⃣ Set permissions for storage & cache
# =====================
RUN mkdir -p storage/framework/{sessions,views,cache,testing} storage/logs bootstrap/cache \
    && chmod -R 777 storage bootstrap/cache

# =====================
# 8️⃣ Set environment variables (Render will override .env anyway)
# =====================
ENV APP_ENV=production
ENV APP_DEBUG=false

# =====================
# 9️⃣ Expose port (PHP-FPM)
# =====================
EXPOSE 3000

# =====================
# 🔟 Start command
# =====================
# Render يحتاج listener على 0.0.0.0
CMD ["php", "-d", "variables_order=EGPCS", "server.php", "0.0.0.0:3000"]
