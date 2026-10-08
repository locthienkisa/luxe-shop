# Security checklist (local)

Chạy trên localhost. Không tấn công hệ thống người khác.

- [ ] SQL injection: nhập `' OR 1=1 --` vào email / tìm kiếm — không lộ dữ liệu, PDO bind
- [ ] XSS: tên sản phẩm chứa `<script>alert(1)</script>` — phải escape, không chạy script
- [ ] CSRF: POST `/gio-hang/them` không `_csrf` — bị chặn
- [ ] IDOR: user A mở `/don-hang/{id}` của user B — 404/403
- [ ] Privilege: customer vào `/admin` — 403
- [ ] User không tự đổi `role_id` qua form hồ sơ
- [ ] Upload `.php` / `.exe` vào avatar — bị từ chối
- [ ] Path traversal tên file `../../etc/passwd` — tên file random, không dùng tên gốc
- [ ] Brute force: >5 login sai — khóa tạm
- [ ] Session: sau login session id đổi (fixation)
- [ ] Production `APP_DEBUG=false` không in stack / SQL / path
- [ ] `.env` không nằm trong DocumentRoot
- [ ] Password cột `password_hash` không plaintext
- [ ] API cart/checkout/orders/profile không token — 401

# Functional checklist

- [ ] Đăng ký / đăng nhập / đăng xuất / quên mật khẩu
- [ ] Đổi mật khẩu, cập nhật hồ sơ, avatar, địa chỉ
- [ ] Catalog: search, filter giá/brand/rating/stock, sort, pagination
- [ ] Chi tiết SP, biến thể, tồn kho chặn vượt SL
- [ ] Giỏ: add/update/remove/clear, coupon, ship
- [ ] Checkout transaction: order, items, trừ kho, payment, xóa cart, history, notification
- [ ] Hủy đơn pending/confirmed — hoàn kho
- [ ] Review chỉ buyer khi VERIFIED_PURCHASE_ONLY
- [ ] Admin dashboard charts, CRUD SP/DM/đơn/user/voucher/review/banner/kho/settings
- [ ] Staff không đổi role (cần admin)
- [ ] Sitemap / robots / slug URL
