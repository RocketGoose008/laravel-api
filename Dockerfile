# ใช้ PHP เวอร์ชัน 8.2 พร้อม Apache
FROM php:8.2-apache

# ติดตั้ง PHP extension ที่ Laravel ต้องใช้
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# ติดตั้ง Composer (PHP package manager)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# เปิด mod_rewrite สำหรับ Laravel Routing
RUN a2enmod rewrite

# ตั้ง working directory
WORKDIR /var/www/html

# คัดลอกไฟล์จาก project ไปยัง container
COPY . .

# ติดตั้ง dependencies
RUN composer install --no-dev --optimize-autoloader

# ตั้ง permission สำหรับ storage & cache
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# เปิด port 80
EXPOSE 80

# ใช้ Apache เป็น web server (default จาก base image แล้ว)
