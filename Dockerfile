# ใช้ PHP 8.2 กับ Apache
FROM php:8.2-apache

# เปิด mod_rewrite สำหรับ Laravel routing
RUN a2enmod rewrite

# ตั้ง ServerName เพื่อลด warning apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# ติดตั้ง dependencies ที่ Laravel ต้องการ
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    unzip \
    zip \
    git \
    curl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# ติดตั้ง Composer (copy มาจาก official composer image)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ตั้ง working directory
WORKDIR /var/www/html

# คัดลอกไฟล์โปรเจคทั้งหมดเข้า container
COPY . .

# รัน composer install (production mode)
RUN composer install --no-dev --optimize-autoloader

# ตั้ง permission ให้เหมาะสมสำหรับ storage และ cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# เปลี่ยน DocumentRoot ของ Apache ให้ชี้ไป public folder ของ Laravel
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# คัดลอกและตั้ง permission ให้ entrypoint script
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# expose port 80
EXPOSE 80

# ตั้ง entrypoint
ENTRYPOINT ["entrypoint.sh"]
