# ใช้ PHP 8.2 + Apache
FROM php:8.2-apache

# เปิด mod_rewrite สำหรับ Laravel routing
RUN a2enmod rewrite

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# ติดตั้ง PHP extensions ที่ Laravel ใช้
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    unzip \
    zip \
    git \
    curl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# ติดตั้ง Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ตั้ง working directory
WORKDIR /var/www/html
COPY . /var/www/html

# Ensure Apache serves from public/
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# คัดลอกไฟล์ Laravel เข้า container (อันนี้ซ้ำกับข้างบน ควรลบอันนี้ออก)
# COPY . .

# ติดตั้ง dependencies
RUN composer install --no-dev --optimize-autoloader

# ตั้ง permission
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# รับพอร์ตจาก environment variable PORT (default เป็น 80)
ENV PORT 80

# แก้ Apache config ให้ฟังพอร์ตตาม PORT env
RUN sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf && \
    sed -i "s/:80/:${PORT}/" /etc/apache2/sites-available/000-default.conf

# expose port จาก env
EXPOSE ${PORT}

# ใช้คำสั่ง start Apache แบบ foreground ตามปกติ
CMD ["apache2-foreground"]
