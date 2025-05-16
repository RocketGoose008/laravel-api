<h1 align="center">
    Laravel Project Setup Guide
</h1>

<p align="center">
  ขั้นตอนการติดตั้งและรันโปรเจกต์ Laravel 
</p>

----------------------------------------------------

## ตรวจสอบและติดตั้ง

- PHP version 8.4.7
- Composer version 2.8.9
- Node.js version 18.20.4.
- NPM version 10.7.0

----------------------------------------------------

## ขั้นตอนการติดตั้ง

1. git clone

1. ติดตั้ง PHP

2. ติดตั้ง Composer 
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    php composer-setup.php
    php -r "unlink('composer-setup.php');"

3. ติดตั้ง Laravel
    composer global require laravel/installer

4. คำสั่ง Run
    php artisan serve

5. คำสั่งสำหรับ ล้างแคช Route
    php artisan route:clear
    php artisan optimize:clear
    php artisan config:clear
    php artisan cache:clear

----------------------------------------------------

## ขั้นตอนเรียก API
    หลังจาก Run Project ขึ้นมาได้แล้ว
    เข้าไปทดสอบ API ที่ > http://localhost:8000/api/documentation 

## การทำงานของ API
    API ทั้งหมดแบ่งเป็น 4 หมวด
1. Member : `สมาชิก`
    - /api/member/register : ใช้สร้างข้อมูล สมาชิก
2. Receiver : `ผู้รับเงิน`
    - /api/receiver/create : ใช้สร้างข้อมูล รายการผู้รับเงิน
    - /api/receiver/list : ใช้ดึงข้อมูล รายการผู้รับเงิน
3. Sell Orders : `รายการ ตั้งขายเหรียญคริปโต`
    - /api/sell_orders/create : ใช้สร้างข้อมูล รายการตั้งขายเหรียญคริปโต
    - /api/sell_orders/list : ใช้ดึงข้อมูล รายการตั้งขายเหรียญคริปโต
4. Transaction : `รายการ การทำธุรกรรม`
    - /api/transactions/insert : ใช้สร้างข้อมูลตัวอย่าง รายการการทำธุรกรรม ซื้อขายแลกเปลี่ยนเหรียญคริปโต
    - /api/transactions/list : ใช้ดึงข้อมูล รายการการทำธุรกรรม ซื้อขายแลกเปลี่ยนเหรียญคริปโต
        
```bash
php -v
