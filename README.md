<h1 align="center">
    Laravel Project Setup Guide
</h1>

<p align="center">
  Demo API ระบบซื้อขายเหรียญคริปโต <br>
  พัฒนาด้วย Laravel รันผ่าน Docker และทดสอบ API ด้วย Swagger
</p>

---

## ขั้นตอนการติดตั้ง
```bash
    git clone https://github.com/RocketGoose008/laravel-api.git
```   

---

## ตรวจสอบและติดตั้ง

- PHP (8.4.7) <br>
    windows : https://windows.php.net/download/ 
    <br>
    macOs : 
    ```bash 
    brew install php 
    ```

- Composer (2.8.9) <br>
```bash
    composer install
```

- Laravel version 5.14.2 <br>
```bash
    composer global require laravel/installer 
```

- Node.js (18.20.4.) & NPM (10.7.0) <br>
    https://nodejs.org/en/  <br>

- Docker Desktop <br>
    https://docs.docker.com/get-docker/
    <br>
    หมายเหตุ: สำหรับการ Run ด้วย Docker 
    
---

## คำสั่ง

- สำหรับ Run ด้วย Docker <br>
```bash
    docker build -t laravel-app .
    docker run -p 8080:80 laravel-app
```

- สำหรับ Run (local development) <br>
```bash
    php artisan serve
```

- สำหรับ ล้างแคช ของ Laravel <br>
```bash
    php artisan route:clear 
    php artisan optimize:clear 
    php artisan config:clear 
    php artisan cache:clear
```

- สร้างหรืออัพเดต Swagger API Documentation <br>
```bash
    php artisan l5-swagger:generate
```
---

## API
หลังจาก Run Project ขึ้นมาได้แล้ว 
เข้าไปทดสอบ API ที่ > http://localhost:8080/api/documentation

### การทำงานของ API
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

---

## การเก็บข้อมูล
ไม่เชื่อมต่อ Database จริง เก็บข้อมูลทั้งหมดไว้ในไฟล์ json <br>
ไฟล์จะถูกสร้างเองที่ storage/app/private/ เมื่อมีการยิง api create ต่างๆ 
