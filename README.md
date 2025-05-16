<h1 align="center">
    Laravel Project Setup Guide
</h1>

<p align="center">
  ขั้นตอนการติดตั้งและรันโปรเจกต์ Laravel 
</p>

----------------------------------------------------
## ขั้นตอนการติดตั้ง
    
    git clone https://github.com/RocketGoose008/laravel-api.git

----------------------------------------------------
## ตรวจสอบและติดตั้ง

- PHP (8.4.7) 
    windows : https://windows.php.net/download/ 
    macOs : brew install php

- Composer (2.8.9) 
    composer install

- Laravel version 5.14.2 
    composer global require laravel/installer

- Node.js (18.20.4.) & NPM (10.7.0) 
    https://nodejs.org/en/ 

----------------------------------------------------
## คำสั่ง

- สำหรับ Run <br>
    php artisan serve

- สำหรับ ล้างแคช Route <br>
    php artisan route:clear <br>
    php artisan optimize:clear <br>
    php artisan config:clear <br>
    php artisan cache:clear

- สำหรับ Update swagger doc เมื่อมีการแก้ไข หรือเพิ่ม comment description <br>
    composer swagger:generate

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

----------------------------------------------------

## การเก็บข้อมูล
    ไม่เชื่อมต่อ Database จริง เก็บข้อมูลทั้งหมดไว้ในไฟล์ json
    ไฟล์จะถูกสร้างเองที่ storage/app/private/ เมื่อมีการยิง api create ต่างๆ 
