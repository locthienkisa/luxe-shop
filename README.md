# Luxe Shop

Website thương mại điện tử PHP 8.2+ (MVC tự viết), MySQL/MariaDB, Apache. Giao diện tiếng Việt, Bootstrap 5.

Không dùng Laravel. Composer không bắt buộc (autoload PSR-4 nằm trong `public/index.php`).

## 1. Requirements

- Windows 10 Pro (hướng dẫn bên dưới) hoặc Linux
- Apache 2.4 + `mod_rewrite`
- PHP 8.2+ với: `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `json`, `gd` (khuyến nghị)
- MySQL 8 hoặc MariaDB 10.11+
- Composer: không bắt buộc

## 2. Cài Apache (Windows)

**XAMPP (khuyến nghị):** tải https://www.apachefriends.org — cài Apache + PHP + MySQL/MariaDB.

**WampServer:** https://www.wampserver.com

Sau khi cài, mở **XAMPP Control Panel**, Start **Apache** và **MySQL**.

PHP CLI: thêm `C:\xampp\php` vào PATH.

## 3. Cài PHP

XAMPP đã kèm PHP. Kiểm tra:

```bat
php -v
```

Cần ≥ 8.2. Bật extension trong `php.ini`:

```
extension=pdo_mysql
extension=mbstring
extension=openssl
extension=fileinfo
extension=gd
```

Restart Apache.

## 4. Cài MySQL/MariaDB

Dùng MySQL của XAMPP (port 3306). Mở phpMyAdmin `http://localhost/phpmyadmin` hoặc:

```bat
cd C:\xampp\mysql\bin
mysql.exe -u root
```

## 5. Composer (tùy chọn)

Chỉ cần nếu bạn muốn `composer dump-autoload`. Project chạy không cần `vendor/`.

## 6. Clone / copy project

Đặt thư mục, ví dụ:

`C:\xampp\htdocs\luxe-shop`

hoặc giữ nguyên path rồi trỏ VirtualHost.

## 7–9. Tạo database, schema, seed

```bat
cd C:\xampp\mysql\bin
mysql.exe -u root -e "CREATE DATABASE IF NOT EXISTS luxe_shop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql.exe -u root luxe_shop < C:\xampp\htdocs\luxe-shop\database\schema.sql
mysql.exe -u root luxe_shop < C:\xampp\htdocs\luxe-shop\database\seed.sql
```

Hoặc dùng **installer**: mở `http://shop.local/install.php` (sau khi VirtualHost trỏ `public/`).

Installer tạo bảng, seed (tùy chọn), ghi `.env` và tạo admin **bắt buộc đổi mật khẩu**. Không chấp nhận mật khẩu demo `Password123!`.

## 10. Cấu hình `.env`

```bat
copy .env.example .env
```

Sửa:

```
APP_URL=http://shop.local
APP_DEBUG=true
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=luxe_shop
DB_USERNAME=root
DB_PASSWORD=
APP_KEY=doi-thanh-chuoi-ngau-nhien-32-ky-tu
```

Khóa cổng thanh toán (`VNPAY_*`, `MOMO_*`, `STRIPE_*`) để trống cho đến khi tích hợp. **Không hard-code secret trong PHP.**

Production: `APP_ENV=production`, `APP_DEBUG=false`.

## 11–13. Apache, VirtualHost, rewrite

1. Mở `C:\xampp\apache\conf\httpd.conf`
2. Bỏ comment:

```
LoadModule rewrite_module modules/mod_rewrite.so
Include conf/extra/httpd-vhosts.conf
```

3. Copy nội dung `apache/shop.local.conf` vào `C:\xampp\apache\conf\extra\httpd-vhosts.conf` — sửa `DocumentRoot` đúng ổ đĩa.
4. **DocumentRoot phải là thư mục `public`**, không phải root project (tránh lộ `.env`).
5. File hosts (Notepad **Run as Administrator**):

`C:\Windows\System32\drivers\etc\hosts`

```
127.0.0.1 shop.local
```

6. `AllowOverride All` trong Directory của vhost để `.htaccess` rewrite về `index.php`.
7. Restart Apache.

`public/.htaccess` gửi mọi request không phải file tĩnh vào front controller.

## 14. Start services

XAMPP: Start Apache + MySQL.

Chạy nhanh không Apache (dev):

```bat
cd C:\xampp\htdocs\luxe-shop\public
php -S localhost:8080 router.php
```

Khi đó `APP_URL=http://localhost:8080`.

## 15. Truy cập website

- Cửa hàng: http://shop.local/
- Cài đặt: http://shop.local/install.php (xóa file này trên production)

## 16. Admin

http://shop.local/admin

## 17. Tài khoản demo (sau khi import `seed.sql`)

| Vai trò | Email | Mật khẩu |
|---|---|---|
| Super Admin | admin@luxe-shop.local | Password123! |
| Staff | staff@luxe-shop.local | Password123! |
| Khách 1 | customer1@example.com | Password123! |
| Khách 2 | customer2@example.com | Password123! |
| Khách 3 | customer3@example.com | Password123! |

Chỉ dùng local. Đổi ngay trên production.

Tạo user mới: `/dang-ky`. User không tự nâng quyền.

## 18. Backup database

```bat
cd C:\xampp\mysql\bin
mysqldump.exe -u root --single-transaction --routines --triggers luxe_shop > C:\backup\luxe_shop_%date:~-4,4%%date:~-7,2%%date:~-10,2%.sql
```

PowerShell:

```powershell
mysqldump -u root luxe_shop | Out-File -Encoding utf8 C:\backup\luxe_shop.sql
```

## 19. Restore

```bat
mysql.exe -u root -e "CREATE DATABASE IF NOT EXISTS luxe_shop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql.exe -u root luxe_shop < C:\backup\luxe_shop.sql
```

## 20. Deploy production

1. `APP_DEBUG=false`, `APP_ENV=production`, HTTPS (`session.cookie_secure` tự bật khi HTTPS).
2. Xóa `public/install.php`.
3. Quyền ghi: `storage/logs`, `public/uploads`.
4. Không commit `.env`.
5. DocumentRoot = `public`.
6. Tắt directory listing.
7. Backup định kỳ (`mysqldump`).
8. Rate limit login đã có; cân nhắc fail2ban trên server thật.
9. Cổng VNPay/MoMo/Stripe: implement class trong `app/Services/Payment/` đọc key từ `.env`, thêm vào `PaymentManager::enabled()`.

## Cây thư mục

```
luxe-shop/
├── app/Controllers, Models, Services, Middleware, Core, Views
├── config/          app.php, database.php
├── public/          index.php, .htaccess, assets, uploads, install.php
├── routes/          web.php, admin.php, api.php
├── database/        schema.sql, seed.sql
├── apache/          shop.local.conf
├── storage/logs
├── tests/
├── .env.example
└── README.md
```

## REST API

| Method | Path | Auth |
|---|---|---|
| GET | `/api/products` | không |
| GET | `/api/products/{slug}` | không |
| POST | `/api/login` | JSON `{email,password}` → token |
| POST | `/api/register` | JSON |
| POST | `/api/cart` | Bearer |
| POST | `/api/checkout` | Bearer |
| GET | `/api/orders` | Bearer |
| GET | `/api/profile` | Bearer |

Header: `Authorization: Bearer <token>`.

## Git commit plan

1. chore: scaffold + schema  
2. feat: auth + session/csrf  
3. feat: catalog search  
4. feat: cart wishlist  
5. feat: checkout orders payment  
6. feat: reviews coupons  
7. feat: admin  
8. docs: readme apache tests  

Không commit `.env`, `uploads/*`, `storage/logs/*`, `vendor/`.

## Thanh toán

Hiện **COD**. Interface `PaymentGatewayInterface`. Stub: VNPay, MoMo, Stripe.

## Bảo mật (tóm tắt)

PDO prepared statements, CSRF, `password_hash`, escape output `e()`, session httponly + strict, rate limit login, upload whitelist MIME/ext + tên random, middleware role, IDOR: địa chỉ/đơn/review theo `user_id`, không mass-assign role từ form khách, debug ẩn production.

Chi tiết: `tests/SECURITY_CHECKLIST.md`.
