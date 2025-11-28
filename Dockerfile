# 1️⃣ Base image
FROM php:8.2-fpm

WORKDIR /var/www/html

# 2️⃣ System dependencies
RUN apt-get update && apt-get install -y git unzip libzip-dev libonig-dev libpng-dev libjpeg-dev libfreetype6-dev libxml2-dev curl \
    && docker-php-ext-install pdo_mysql mbstring zip exif pcntl gd xml \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 3️⃣ Copy all project files first
COPY . .

# 4️⃣ Install Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');"

# 5️⃣ Install PHP dependencies (now artisan موجود)
RUN composer install --no-dev --optimize-autoloader

# 6️⃣ Permissions
RUN mkdir -p storage/framework/{sessions,views,cache,testing} storage/logs bootstrap/cache \
    && chmod -R 777 storage bootstrap/cache

# 7️⃣ Expose port
EXPOSE 3000

# 8️⃣ Start command
CMD ["php", "-d", "variables_order=EGPCS", "server.php", "0.0.0.0:3000"]
