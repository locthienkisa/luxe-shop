-- Luxe Shop demo seed (passwords: Password123!)
SET NAMES utf8mb4;
USE `luxe_shop`;
SET FOREIGN_KEY_CHECKS=0;
INSERT INTO roles (id, slug, name) VALUES (1,'customer','Customer'),(2,'staff','Staff'),(3,'admin','Admin'),(4,'super_admin','Super Admin');
INSERT INTO permissions (id, slug, name) VALUES (1,'products.manage','Quản lý sản phẩm'),(2,'orders.manage','Quản lý đơn'),(3,'users.manage','Quản lý user'),(4,'coupons.manage','Quản lý voucher'),(5,'reviews.manage','Quản lý đánh giá'),(6,'settings.manage','Cài đặt');
INSERT INTO role_permissions (role_id, permission_id) SELECT 2,id FROM permissions WHERE slug IN ('products.manage','orders.manage','reviews.manage');
INSERT INTO role_permissions (role_id, permission_id) SELECT 3,id FROM permissions;
INSERT INTO role_permissions (role_id, permission_id) SELECT 4,id FROM permissions;
INSERT INTO users (id,role_id,email,password_hash,full_name,phone,is_active,email_verified_at) VALUES
(1,4,'admin@luxe-shop.local','$2y$10$dGOvpUJQAw8uKiaP2XWQe.QccC3uPI6F2ucrfAzi/DE0ItWkkLnZq','Super Admin','0900000001',1,NOW()),
(2,2,'staff@luxe-shop.local','$2y$10$dGOvpUJQAw8uKiaP2XWQe.QccC3uPI6F2ucrfAzi/DE0ItWkkLnZq','Nhân viên Minh','0900000002',1,NOW()),
(3,1,'customer1@example.com','$2y$10$dGOvpUJQAw8uKiaP2XWQe.QccC3uPI6F2ucrfAzi/DE0ItWkkLnZq','Nguyễn Văn An','0901111111',1,NOW()),
(4,1,'customer2@example.com','$2y$10$dGOvpUJQAw8uKiaP2XWQe.QccC3uPI6F2ucrfAzi/DE0ItWkkLnZq','Trần Thị Bình','0902222222',1,NOW()),
(5,1,'customer3@example.com','$2y$10$dGOvpUJQAw8uKiaP2XWQe.QccC3uPI6F2ucrfAzi/DE0ItWkkLnZq','Lê Quốc Cường','0903333333',1,NOW());
INSERT INTO addresses (user_id,label,full_name,phone,line1,ward,district,city,is_default) VALUES
(3,'Nhà','Nguyễn Văn An','0901111111','12 Lê Lợi','Bến Nghé','Quận 1','TP.HCM',1),
(4,'Nhà','Trần Thị Bình','0902222222','88 Pasteur','Võ Thị Sáu','Quận 3','TP.HCM',1),
(5,'Nhà','Lê Quốc Cường','0903333333','5 Nguyễn Huệ','Bến Nghé','Quận 1','TP.HCM',1);
INSERT INTO categories (id,name,slug,description,sort_order,is_active,meta_title) VALUES
(1,'Điện thoại','dien-thoai','Smartphone các hãng',1,1,'Điện thoại'),
(2,'Laptop','laptop','Máy tính xách tay',2,1,'Laptop'),
(3,'Âm thanh','am-thanh','Tai nghe, loa',3,1,'Âm thanh'),
(4,'Thời trang','thoi-trang','May mặc, giày dép',4,1,'Thời trang'),
(5,'Gia dụng','gia-dung','Thiết bị nhà bếp',5,1,'Gia dụng');
INSERT INTO products (id,name,slug,sku,brand,short_description,description,price,sale_price,stock,has_variants,is_featured,is_active,sold_count,rating_avg,rating_count,meta_title,created_at) VALUES
(1,'iPhone 15 Pro','iphone-15-pro','IP15P','Apple','Flagship Apple A17 Pro','Điện thoại cao cấp, camera 48MP, titanium.',28990000,26990000,40,0,1,1,1,0,0,'iPhone 15 Pro',DATE_SUB(NOW(), INTERVAL 19 DAY)),
(2,'Samsung Galaxy S24','galaxy-s24','SS-S24','Samsung','Màn hình AMOLED 120Hz','Galaxy AI, pin bền, chống nước IP68.',22990000,19990000,35,0,1,1,2,0,0,'Samsung Galaxy S24',DATE_SUB(NOW(), INTERVAL 18 DAY)),
(3,'Xiaomi 14','xiaomi-14','MI14','Xiaomi','Leica camera, sạc nhanh','Hiệu năng mạnh, giá tốt trong phân khúc flagship.',16990000,14990000,50,0,0,1,3,0,0,'Xiaomi 14',DATE_SUB(NOW(), INTERVAL 17 DAY)),
(4,'OPPO Find X7','oppo-find-x7','OP-FX7','OPPO','Sạc siêu nhanh, màn cong','Thiết kế mỏng nhẹ, camera Hasselblad.',18990000,NULL,25,0,0,1,4,0,0,'OPPO Find X7',DATE_SUB(NOW(), INTERVAL 16 DAY)),
(5,'MacBook Air M3','macbook-air-m3','MBA-M3','Apple','13 inch, chip M3','Laptop mỏng nhẹ cho công việc và sáng tạo.',32990000,30990000,20,0,1,1,5,0,0,'MacBook Air M3',DATE_SUB(NOW(), INTERVAL 15 DAY)),
(6,'Dell XPS 13','dell-xps-13','DXPS13','Dell','Màn InfinityEdge','Ultrabook doanh nhân, bàn phím êm.',28990000,NULL,15,0,1,0,6,0,0,'Dell XPS 13',DATE_SUB(NOW(), INTERVAL 14 DAY)),
(7,'ASUS Vivobook 15','asus-vivobook-15','AS-VB15','ASUS','i5, 16GB RAM','Học tập văn phòng, màn 15.6 inch.',16990000,14990000,30,0,0,1,7,0,0,'ASUS Vivobook 15',DATE_SUB(NOW(), INTERVAL 13 DAY)),
(8,'Lenovo ThinkPad E14','thinkpad-e14','LN-E14','Lenovo','Bền bỉ chuẩn doanh nghiệp','Bảo mật vân tay, bàn phím ThinkPad.',21990000,NULL,18,0,0,1,8,0,0,'Lenovo ThinkPad E14',DATE_SUB(NOW(), INTERVAL 12 DAY)),
(9,'Sony WH-1000XM5','sony-wh1000xm5','SN-XM5','Sony','Chống ồn chủ động','Tai nghe over-ear hi-end, pin 30 giờ.',8490000,7490000,60,0,1,1,9,0,0,'Sony WH-1000XM5',DATE_SUB(NOW(), INTERVAL 11 DAY)),
(10,'AirPods Pro 2','airpods-pro-2','AP-PRO2','Apple','USB-C, ANC','Tai nghe in-ear Apple, Spatial Audio.',6190000,5490000,80,0,1,1,10,0,0,'AirPods Pro 2',DATE_SUB(NOW(), INTERVAL 10 DAY)),
(11,'JBL Charge 5','jbl-charge-5','JBL-C5','JBL','Loa bluetooth chống nước','Bass mạnh, pin 20 giờ, PartyBoost.',3990000,3490000,45,0,0,1,11,0,0,'JBL Charge 5',DATE_SUB(NOW(), INTERVAL 9 DAY)),
(12,'Marshall Emberton II','marshall-emberton-ii','MS-EM2','Marshall','Loa compact vintage','Âm thanh stereo 360 độ.',4590000,NULL,22,0,0,1,12,0,0,'Marshall Emberton II',DATE_SUB(NOW(), INTERVAL 8 DAY)),
(13,'Áo sơ mi Oxford','ao-so-mi-oxford','TS-OXF','LuxeWear','Cotton 100%','Form regular, dễ phối đồ công sở.',590000,449000,100,0,1,1,13,0,0,'Áo sơ mi Oxford',DATE_SUB(NOW(), INTERVAL 7 DAY)),
(14,'Quần jeans slim','quan-jeans-slim','TS-JN','LuxeWear','Denim stretch','Co giãn nhẹ, màu indigo.',790000,NULL,70,0,0,1,14,0,0,'Quần jeans slim',DATE_SUB(NOW(), INTERVAL 6 DAY)),
(15,'Áo khoác bomber','ao-khoac-bomber','TS-BB','LuxeWear','Nylon chống gió','Phong cách streetwear.',1290000,990000,40,0,1,1,15,0,0,'Áo khoác bomber',DATE_SUB(NOW(), INTERVAL 5 DAY)),
(16,'Giày sneaker City','giay-sneaker-city','TS-SN','LuxeWear','Đế êm, da tổng hợp','Đi làm và đi chơi.',1590000,1290000,55,0,1,1,16,0,0,'Giày sneaker City',DATE_SUB(NOW(), INTERVAL 4 DAY)),
(17,'Nồi chiên không dầu 5L','noi-chien-5l','GD-AF5','HomePro','8 chương trình nấu','Giòn ít dầu, dễ vệ sinh.',1890000,1590000,33,0,0,1,17,0,0,'Nồi chiên không dầu 5L',DATE_SUB(NOW(), INTERVAL 3 DAY)),
(18,'Máy lọc không khí HEPA','may-loc-kk','GD-AP','HomePro','Lọc bụi mịn PM2.5','Phù hợp phòng 30m2.',3290000,2890000,21,0,1,0,18,0,0,'Máy lọc không khí HEPA',DATE_SUB(NOW(), INTERVAL 2 DAY)),
(19,'Robot hút bụi mini','robot-hut-bui','GD-RB','HomePro','Tự sạc, app điều khiển','Lau nhà kết hợp hút bụi.',4990000,4290000,12,0,1,1,19,0,0,'Robot hút bụi mini',DATE_SUB(NOW(), INTERVAL 1 DAY)),
(20,'Ấm siêu tốc inox 1.7L','am-sieu-toc','GD-KT','HomePro','Đun sôi nhanh','Tự ngắt, miệng rộng.',390000,290000,90,0,0,1,20,0,0,'Ấm siêu tốc inox 1.7L',DATE_SUB(NOW(), INTERVAL 0 DAY));
INSERT INTO product_categories (product_id, category_id) VALUES (1,1),(2,1),(3,1),(4,1),(5,2),(6,2),(7,2),(8,2),(9,3),(10,3),(11,3),(12,3),(13,4),(14,4),(15,4),(16,4),(17,5),(18,5),(19,5),(20,5);
INSERT INTO product_images (product_id,path,alt,is_primary) VALUES (1,'assets/images/products/p01.svg','iPhone 15 Pro',1),(2,'assets/images/products/p02.svg','Samsung Galaxy S24',1),(3,'assets/images/products/p03.svg','Xiaomi 14',1),(4,'assets/images/products/p04.svg','OPPO Find X7',1),(5,'assets/images/products/p05.svg','MacBook Air M3',1),(6,'assets/images/products/p06.svg','Dell XPS 13',1),(7,'assets/images/products/p07.svg','ASUS Vivobook 15',1),(8,'assets/images/products/p08.svg','Lenovo ThinkPad E14',1),(9,'assets/images/products/p09.svg','Sony WH-1000XM5',1),(10,'assets/images/products/p10.svg','AirPods Pro 2',1),(11,'assets/images/products/p11.svg','JBL Charge 5',1),(12,'assets/images/products/p12.svg','Marshall Emberton II',1),(13,'assets/images/products/p13.svg','Áo sơ mi Oxford',1),(14,'assets/images/products/p14.svg','Quần jeans slim',1),(15,'assets/images/products/p15.svg','Áo khoác bomber',1),(16,'assets/images/products/p16.svg','Giày sneaker City',1),(17,'assets/images/products/p17.svg','Nồi chiên không dầu 5L',1),(18,'assets/images/products/p18.svg','Máy lọc không khí HEPA',1),(19,'assets/images/products/p19.svg','Robot hút bụi mini',1),(20,'assets/images/products/p20.svg','Ấm siêu tốc inox 1.7L',1);
INSERT INTO product_variants (product_id,sku,size,color,price,stock) VALUES
(13,'TS-OXF-M-W','M','Trắng',449000,30),
(13,'TS-OXF-L-W','L','Trắng',449000,30),
(13,'TS-OXF-M-B','M','Xanh',449000,20),
(16,'TS-SN-42-W','42','Trắng',1290000,20),
(16,'TS-SN-43-B','43','Đen',1290000,20);
UPDATE products SET has_variants=1 WHERE id IN (13,16);
INSERT INTO inventory (product_id,change_qty,reason,note) SELECT id, stock, 'import', 'Tồn đầu' FROM products;
INSERT INTO coupons (code,discount_type,discount_value,min_order,max_discount,starts_at,ends_at,usage_limit,per_user_limit,is_active) VALUES
('WELCOME10','percent',10,500000,100000,DATE_SUB(NOW(),INTERVAL 1 DAY),DATE_ADD(NOW(),INTERVAL 90 DAY),1000,3,1),
('SALE50K','fixed',50000,300000,NULL,DATE_SUB(NOW(),INTERVAL 1 DAY),DATE_ADD(NOW(),INTERVAL 60 DAY),500,2,1),
('FREESHIP','fixed',30000,0,30000,DATE_SUB(NOW(),INTERVAL 1 DAY),DATE_ADD(NOW(),INTERVAL 30 DAY),200,1,1),
('VIP20','percent',20,2000000,400000,DATE_SUB(NOW(),INTERVAL 1 DAY),DATE_ADD(NOW(),INTERVAL 120 DAY),50,1,1);
INSERT INTO banners (title,subtitle,image,link,position,sort_order,is_active) VALUES
('Bộ sưu tập thu 2026','Giảm đến 20% điện thoại flagship','assets/images/placeholders/product.svg','/danh-muc/dien-thoai','hero',1,1),
('Laptop cho công việc','MacBook & XPS siêu nhẹ','assets/images/placeholders/product.svg','/danh-muc/laptop','hero',2,1),
('Freeship đơn từ 500.000đ','Toàn quốc','assets/images/placeholders/product.svg','/san-pham','promo',1,1);
INSERT INTO settings (`key`,`value`) VALUES
('site_name','Luxe Shop'),
('meta_description','Cửa hàng Luxe Shop — điện thoại, laptop, thời trang, gia dụng.'),
('hotline','1900 6868'),
('address','123 Nguyễn Huệ, Quận 1, TP.HCM'),
('email_contact','hello@luxe-shop.local'),
('low_stock_threshold','5');
INSERT INTO orders (id,order_number,user_id,address_id,shipping_name,shipping_phone,shipping_address,subtotal,discount,shipping_fee,total,coupon_code,status,payment_status,payment_method,created_at) VALUES
(1,'LX260901A1',3,1,'Nguyễn Văn An','0901111111','12 Lê Lợi, Bến Nghé, Quận 1, TP.HCM',26990000,0,0,26990000,NULL,'delivered','paid','cod',DATE_SUB(NOW(),INTERVAL 20 DAY)),
(2,'LX260902B2',4,2,'Trần Thị Bình','0902222222','88 Pasteur, Võ Thị Sáu, Quận 3, TP.HCM',7490000,0,0,7490000,NULL,'shipping','unpaid','cod',DATE_SUB(NOW(),INTERVAL 5 DAY)),
(3,'LX260903C3',5,3,'Lê Quốc Cường','0903333333','5 Nguyễn Huệ, Bến Nghé, Quận 1, TP.HCM',1590000,50000,0,1540000,'SALE50K','processing','unpaid','cod',DATE_SUB(NOW(),INTERVAL 3 DAY)),
(4,'LX260904D4',3,1,'Nguyễn Văn An','0901111111','12 Lê Lợi, Bến Nghé, Quận 1, TP.HCM',449000,0,30000,479000,NULL,'confirmed','unpaid','cod',DATE_SUB(NOW(),INTERVAL 1 DAY)),
(5,'LX260905E5',4,2,'Trần Thị Bình','0902222222','88 Pasteur, Quận 3, TP.HCM',3490000,0,0,3490000,NULL,'pending','unpaid','cod',NOW());
INSERT INTO order_items (order_id,product_id,product_name,sku,unit_price,quantity,line_total) VALUES
(1,1,'iPhone 15 Pro','IP15P',26990000,1,26990000),
(2,9,'Sony WH-1000XM5','SN-XM5',7490000,1,7490000),
(3,17,'Nồi chiên không dầu 5L','GD-AF5',1590000,1,1590000),
(4,13,'Áo sơ mi Oxford','TS-OXF',449000,1,449000),
(5,11,'JBL Charge 5','JBL-C5',3490000,1,3490000);
INSERT INTO order_status_history (order_id,from_status,to_status,note,created_by) VALUES
(1,NULL,'pending','Đặt hàng',3),(1,'pending','confirmed','OK',2),(1,'confirmed','processing','',2),(1,'processing','shipping','',2),(1,'shipping','delivered','Giao thành công',2),
(2,NULL,'pending','Đặt hàng',4),(2,'pending','confirmed','',2),(2,'confirmed','processing','',2),(2,'processing','shipping','',2),
(3,NULL,'pending','Đặt hàng',5),(3,'pending','confirmed','',2),(3,'confirmed','processing','',2),
(4,NULL,'pending','Đặt hàng',3),(4,'pending','confirmed','',2),
(5,NULL,'pending','Đặt hàng',4);
INSERT INTO payments (order_id,method,amount,status) VALUES
(1,'cod',26990000,'paid'),(2,'cod',7490000,'pending'),(3,'cod',1540000,'pending'),(4,'cod',479000,'pending'),(5,'cod',3490000,'pending');
INSERT INTO coupon_usages (coupon_id,user_id,order_id) SELECT id,5,3 FROM coupons WHERE code='SALE50K';
UPDATE coupons SET used_count=1 WHERE code='SALE50K';
INSERT INTO reviews (product_id,user_id,order_id,rating,content,status) VALUES
(1,3,1,5,'Máy đẹp, mượt, pin ổn.','approved'),
(9,4,2,4,'Chống ồn tốt, hơi đắt.','approved'),
(13,3,4,5,'Vải mát, form chuẩn.','pending');
UPDATE products SET rating_avg=5, rating_count=1 WHERE id=1;
UPDATE products SET rating_avg=4, rating_count=1 WHERE id=9;
INSERT INTO notifications (user_id,title,body,link) VALUES
(3,'Đơn LX260901A1 đã giao','Cảm ơn bạn đã mua sắm tại Luxe Shop.','/don-hang/1'),
(4,'Đơn LX260902B2 đang giao','Shipper đang trên đường.','/don-hang/2');
INSERT INTO wishlists (user_id) VALUES (3),(4);
INSERT INTO wishlist_items (wishlist_id,product_id) VALUES (1,5),(1,10),(2,15);
SET FOREIGN_KEY_CHECKS=1;
