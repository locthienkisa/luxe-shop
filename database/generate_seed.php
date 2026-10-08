<?php
$hash = '$2y$10$dGOvpUJQAw8uKiaP2XWQe.QccC3uPI6F2ucrfAzi/DE0ItWkkLnZq';
$products = [
    [1,'iPhone 15 Pro','iphone-15-pro','IP15P','Apple','Flagship Apple A17 Pro','Điện thoại cao cấp, camera 48MP, titanium.',28990000,26990000,40,1,1,'Điện thoại'],
    [2,'Samsung Galaxy S24','galaxy-s24','SS-S24','Samsung','Màn hình AMOLED 120Hz','Galaxy AI, pin bền, chống nước IP68.',22990000,19990000,35,1,1,'Điện thoại'],
    [3,'Xiaomi 14','xiaomi-14','MI14','Xiaomi','Leica camera, sạc nhanh','Hiệu năng mạnh, giá tốt trong phân khúc flagship.',16990000,14990000,50,0,1,'Điện thoại'],
    [4,'OPPO Find X7','oppo-find-x7','OP-FX7','OPPO','Sạc siêu nhanh, màn cong','Thiết kế mỏng nhẹ, camera Hasselblad.',18990000,null,25,0,1,'Điện thoại'],
    [5,'MacBook Air M3','macbook-air-m3','MBA-M3','Apple','13 inch, chip M3','Laptop mỏng nhẹ cho công việc và sáng tạo.',32990000,30990000,20,1,1,'Laptop'],
    [6,'Dell XPS 13','dell-xps-13','DXPS13','Dell','Màn InfinityEdge','Ultrabook doanh nhân, bàn phím êm.',28990000,null,15,1,0,'Laptop'],
    [7,'ASUS Vivobook 15','asus-vivobook-15','AS-VB15','ASUS','i5, 16GB RAM','Học tập văn phòng, màn 15.6 inch.',16990000,14990000,30,0,1,'Laptop'],
    [8,'Lenovo ThinkPad E14','thinkpad-e14','LN-E14','Lenovo','Bền bỉ chuẩn doanh nghiệp','Bảo mật vân tay, bàn phím ThinkPad.',21990000,null,18,0,1,'Laptop'],
    [9,'Sony WH-1000XM5','sony-wh1000xm5','SN-XM5','Sony','Chống ồn chủ động','Tai nghe over-ear hi-end, pin 30 giờ.',8490000,7490000,60,1,1,'Âm thanh'],
    [10,'AirPods Pro 2','airpods-pro-2','AP-PRO2','Apple','USB-C, ANC','Tai nghe in-ear Apple, Spatial Audio.',6190000,5490000,80,1,1,'Âm thanh'],
    [11,'JBL Charge 5','jbl-charge-5','JBL-C5','JBL','Loa bluetooth chống nước','Bass mạnh, pin 20 giờ, PartyBoost.',3990000,3490000,45,0,1,'Âm thanh'],
    [12,'Marshall Emberton II','marshall-emberton-ii','MS-EM2','Marshall','Loa compact vintage','Âm thanh stereo 360 độ.',4590000,null,22,0,1,'Âm thanh'],
    [13,'Áo sơ mi Oxford','ao-so-mi-oxford','TS-OXF','LuxeWear','Cotton 100%','Form regular, dễ phối đồ công sở.',590000,449000,100,1,1,'Thời trang'],
    [14,'Quần jeans slim','quan-jeans-slim','TS-JN','LuxeWear','Denim stretch','Co giãn nhẹ, màu indigo.',790000,null,70,0,1,'Thời trang'],
    [15,'Áo khoác bomber','ao-khoac-bomber','TS-BB','LuxeWear','Nylon chống gió','Phong cách streetwear.',1290000,990000,40,1,1,'Thời trang'],
    [16,'Giày sneaker City','giay-sneaker-city','TS-SN','LuxeWear','Đế êm, da tổng hợp','Đi làm và đi chơi.',1590000,1290000,55,1,1,'Thời trang'],
    [17,'Nồi chiên không dầu 5L','noi-chien-5l','GD-AF5','HomePro','8 chương trình nấu','Giòn ít dầu, dễ vệ sinh.',1890000,1590000,33,0,1,'Gia dụng'],
    [18,'Máy lọc không khí HEPA','may-loc-kk','GD-AP','HomePro','Lọc bụi mịn PM2.5','Phù hợp phòng 30m2.',3290000,2890000,21,1,0,'Gia dụng'],
    [19,'Robot hút bụi mini','robot-hut-bui','GD-RB','HomePro','Tự sạc, app điều khiển','Lau nhà kết hợp hút bụi.',4990000,4290000,12,1,1,'Gia dụng'],
    [20,'Ấm siêu tốc inox 1.7L','am-sieu-toc','GD-KT','HomePro','Đun sôi nhanh','Tự ngắt, miệng rộng.',390000,290000,90,0,1,'Gia dụng'],
];

$dir = __DIR__ . '/../public/assets/images/products';
if (!is_dir($dir)) mkdir($dir, 0755, true);
foreach ($products as $p) {
    $id = str_pad((string)$p[0], 2, '0', STR_PAD_LEFT);
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="800" height="800"><rect width="800" height="800" fill="#'.substr(md5($p[1]),0,6).'"/><text x="400" y="390" text-anchor="middle" fill="#fff" font-size="28" font-family="sans-serif">'.htmlspecialchars($p[1]).'</text></svg>';
    file_put_contents("$dir/p$id.svg", $svg);
}

$out = "-- Luxe Shop demo seed (passwords: Password123!)\nSET NAMES utf8mb4;\nUSE `luxe_shop`;\nSET FOREIGN_KEY_CHECKS=0;\n";

$out .= "INSERT INTO roles (id, slug, name) VALUES (1,'customer','Customer'),(2,'staff','Staff'),(3,'admin','Admin'),(4,'super_admin','Super Admin');\n";
$perms = [
    ['products.manage','Quản lý sản phẩm'],['orders.manage','Quản lý đơn'],['users.manage','Quản lý user'],
    ['coupons.manage','Quản lý voucher'],['reviews.manage','Quản lý đánh giá'],['settings.manage','Cài đặt'],
];
$i=1;
$out .= "INSERT INTO permissions (id, slug, name) VALUES ";
$pv=[];
foreach ($perms as $pe) { $pv[]="($i,'{$pe[0]}','{$pe[1]}')"; $i++; }
$out .= implode(',', $pv).";\n";
$out .= "INSERT INTO role_permissions (role_id, permission_id) SELECT 2,id FROM permissions WHERE slug IN ('products.manage','orders.manage','reviews.manage');\n";
$out .= "INSERT INTO role_permissions (role_id, permission_id) SELECT 3,id FROM permissions;\n";
$out .= "INSERT INTO role_permissions (role_id, permission_id) SELECT 4,id FROM permissions;\n";

$out .= "INSERT INTO users (id,role_id,email,password_hash,full_name,phone,is_active,email_verified_at) VALUES
(1,4,'admin@luxe-shop.local','$hash','Super Admin','0900000001',1,NOW()),
(2,2,'staff@luxe-shop.local','$hash','Nhân viên Minh','0900000002',1,NOW()),
(3,1,'customer1@example.com','$hash','Nguyễn Văn An','0901111111',1,NOW()),
(4,1,'customer2@example.com','$hash','Trần Thị Bình','0902222222',1,NOW()),
(5,1,'customer3@example.com','$hash','Lê Quốc Cường','0903333333',1,NOW());\n";

$out .= "INSERT INTO addresses (user_id,label,full_name,phone,line1,ward,district,city,is_default) VALUES
(3,'Nhà','Nguyễn Văn An','0901111111','12 Lê Lợi','Bến Nghé','Quận 1','TP.HCM',1),
(4,'Nhà','Trần Thị Bình','0902222222','88 Pasteur','Võ Thị Sáu','Quận 3','TP.HCM',1),
(5,'Nhà','Lê Quốc Cường','0903333333','5 Nguyễn Huệ','Bến Nghé','Quận 1','TP.HCM',1);\n";

$out .= "INSERT INTO categories (id,name,slug,description,sort_order,is_active,meta_title) VALUES
(1,'Điện thoại','dien-thoai','Smartphone các hãng',1,1,'Điện thoại'),
(2,'Laptop','laptop','Máy tính xách tay',2,1,'Laptop'),
(3,'Âm thanh','am-thanh','Tai nghe, loa',3,1,'Âm thanh'),
(4,'Thời trang','thoi-trang','May mặc, giày dép',4,1,'Thời trang'),
(5,'Gia dụng','gia-dung','Thiết bị nhà bếp',5,1,'Gia dụng');\n";

$catMap = ['Điện thoại'=>1,'Laptop'=>2,'Âm thanh'=>3,'Thời trang'=>4,'Gia dụng'=>5];
$out .= "INSERT INTO products (id,name,slug,sku,brand,short_description,description,price,sale_price,stock,has_variants,is_featured,is_active,sold_count,rating_avg,rating_count,meta_title,created_at) VALUES\n";
$rows=[];
foreach ($products as $p) {
    $sale = $p[8]===null ? 'NULL' : $p[8];
    $desc = addslashes($p[6]);
    $short = addslashes($p[5]);
    $name = addslashes($p[1]);
    $days = 20 - $p[0];
    $rows[] = "({$p[0]},'{$name}','{$p[2]}','{$p[3]}','{$p[4]}','{$short}','{$desc}',{$p[7]},{$sale},{$p[9]},0,{$p[10]},{$p[11]},{$p[0]},0,0,'{$name}',DATE_SUB(NOW(), INTERVAL {$days} DAY))";
}
$out .= implode(",\n",$rows).";\n";

$out .= "INSERT INTO product_categories (product_id, category_id) VALUES ";
$pc=[];
foreach ($products as $p) { $pc[]="({$p[0]},".$catMap[$p[12]].")"; }
$out .= implode(',',$pc).";\n";

$out .= "INSERT INTO product_images (product_id,path,alt,is_primary) VALUES ";
$im=[];
foreach ($products as $p) {
    $id = str_pad((string)$p[0], 2, '0', STR_PAD_LEFT);
    $im[]="({$p[0]},'assets/images/products/p{$id}.svg','".addslashes($p[1])."',1)";
}
$out .= implode(',',$im).";\n";

$out .= "INSERT INTO product_variants (product_id,sku,size,color,price,stock) VALUES
(13,'TS-OXF-M-W','M','Trắng',449000,30),
(13,'TS-OXF-L-W','L','Trắng',449000,30),
(13,'TS-OXF-M-B','M','Xanh',449000,20),
(16,'TS-SN-42-W','42','Trắng',1290000,20),
(16,'TS-SN-43-B','43','Đen',1290000,20);\n";
$out .= "UPDATE products SET has_variants=1 WHERE id IN (13,16);\n";

$out .= "INSERT INTO inventory (product_id,change_qty,reason,note) SELECT id, stock, 'import', 'Tồn đầu' FROM products;\n";

$out .= "INSERT INTO coupons (code,discount_type,discount_value,min_order,max_discount,starts_at,ends_at,usage_limit,per_user_limit,is_active) VALUES
('WELCOME10','percent',10,500000,100000,DATE_SUB(NOW(),INTERVAL 1 DAY),DATE_ADD(NOW(),INTERVAL 90 DAY),1000,3,1),
('SALE50K','fixed',50000,300000,NULL,DATE_SUB(NOW(),INTERVAL 1 DAY),DATE_ADD(NOW(),INTERVAL 60 DAY),500,2,1),
('FREESHIP','fixed',30000,0,30000,DATE_SUB(NOW(),INTERVAL 1 DAY),DATE_ADD(NOW(),INTERVAL 30 DAY),200,1,1),
('VIP20','percent',20,2000000,400000,DATE_SUB(NOW(),INTERVAL 1 DAY),DATE_ADD(NOW(),INTERVAL 120 DAY),50,1,1);\n";

$out .= "INSERT INTO banners (title,subtitle,image,link,position,sort_order,is_active) VALUES
('Bộ sưu tập thu 2026','Giảm đến 20% điện thoại flagship','assets/images/placeholders/product.svg','/danh-muc/dien-thoai','hero',1,1),
('Laptop cho công việc','MacBook & XPS siêu nhẹ','assets/images/placeholders/product.svg','/danh-muc/laptop','hero',2,1),
('Freeship đơn từ 500.000đ','Toàn quốc','assets/images/placeholders/product.svg','/san-pham','promo',1,1);\n";

$out .= "INSERT INTO settings (`key`,`value`) VALUES
('site_name','Luxe Shop'),
('meta_description','Cửa hàng Luxe Shop — điện thoại, laptop, thời trang, gia dụng.'),
('hotline','1900 6868'),
('address','123 Nguyễn Huệ, Quận 1, TP.HCM'),
('email_contact','hello@luxe-shop.local'),
('low_stock_threshold','5');\n";

# 5 orders
$out .= "INSERT INTO orders (id,order_number,user_id,address_id,shipping_name,shipping_phone,shipping_address,subtotal,discount,shipping_fee,total,coupon_code,status,payment_status,payment_method,created_at) VALUES
(1,'LX260901A1',3,1,'Nguyễn Văn An','0901111111','12 Lê Lợi, Bến Nghé, Quận 1, TP.HCM',26990000,0,0,26990000,NULL,'delivered','paid','cod',DATE_SUB(NOW(),INTERVAL 20 DAY)),
(2,'LX260902B2',4,2,'Trần Thị Bình','0902222222','88 Pasteur, Võ Thị Sáu, Quận 3, TP.HCM',7490000,0,0,7490000,NULL,'shipping','unpaid','cod',DATE_SUB(NOW(),INTERVAL 5 DAY)),
(3,'LX260903C3',5,3,'Lê Quốc Cường','0903333333','5 Nguyễn Huệ, Bến Nghé, Quận 1, TP.HCM',1590000,50000,0,1540000,'SALE50K','processing','unpaid','cod',DATE_SUB(NOW(),INTERVAL 3 DAY)),
(4,'LX260904D4',3,1,'Nguyễn Văn An','0901111111','12 Lê Lợi, Bến Nghé, Quận 1, TP.HCM',449000,0,30000,479000,NULL,'confirmed','unpaid','cod',DATE_SUB(NOW(),INTERVAL 1 DAY)),
(5,'LX260905E5',4,2,'Trần Thị Bình','0902222222','88 Pasteur, Quận 3, TP.HCM',3490000,0,0,3490000,NULL,'pending','unpaid','cod',NOW());\n";

$out .= "INSERT INTO order_items (order_id,product_id,product_name,sku,unit_price,quantity,line_total) VALUES
(1,1,'iPhone 15 Pro','IP15P',26990000,1,26990000),
(2,9,'Sony WH-1000XM5','SN-XM5',7490000,1,7490000),
(3,17,'Nồi chiên không dầu 5L','GD-AF5',1590000,1,1590000),
(4,13,'Áo sơ mi Oxford','TS-OXF',449000,1,449000),
(5,11,'JBL Charge 5','JBL-C5',3490000,1,3490000);\n";

$out .= "INSERT INTO order_status_history (order_id,from_status,to_status,note,created_by) VALUES
(1,NULL,'pending','Đặt hàng',3),(1,'pending','confirmed','OK',2),(1,'confirmed','processing','',2),(1,'processing','shipping','',2),(1,'shipping','delivered','Giao thành công',2),
(2,NULL,'pending','Đặt hàng',4),(2,'pending','confirmed','',2),(2,'confirmed','processing','',2),(2,'processing','shipping','',2),
(3,NULL,'pending','Đặt hàng',5),(3,'pending','confirmed','',2),(3,'confirmed','processing','',2),
(4,NULL,'pending','Đặt hàng',3),(4,'pending','confirmed','',2),
(5,NULL,'pending','Đặt hàng',4);\n";

$out .= "INSERT INTO payments (order_id,method,amount,status) VALUES
(1,'cod',26990000,'paid'),(2,'cod',7490000,'pending'),(3,'cod',1540000,'pending'),(4,'cod',479000,'pending'),(5,'cod',3490000,'pending');\n";

$out .= "INSERT INTO coupon_usages (coupon_id,user_id,order_id) SELECT id,5,3 FROM coupons WHERE code='SALE50K';\n";
$out .= "UPDATE coupons SET used_count=1 WHERE code='SALE50K';\n";

$out .= "INSERT INTO reviews (product_id,user_id,order_id,rating,content,status) VALUES
(1,3,1,5,'Máy đẹp, mượt, pin ổn.','approved'),
(9,4,2,4,'Chống ồn tốt, hơi đắt.','approved'),
(13,3,4,5,'Vải mát, form chuẩn.','pending');\n";
$out .= "UPDATE products SET rating_avg=5, rating_count=1 WHERE id=1;\n";
$out .= "UPDATE products SET rating_avg=4, rating_count=1 WHERE id=9;\n";

$out .= "INSERT INTO notifications (user_id,title,body,link) VALUES
(3,'Đơn LX260901A1 đã giao','Cảm ơn bạn đã mua sắm tại Luxe Shop.','/don-hang/1'),
(4,'Đơn LX260902B2 đang giao','Shipper đang trên đường.','/don-hang/2');\n";

$out .= "INSERT INTO wishlists (user_id) VALUES (3),(4);\n";
$out .= "INSERT INTO wishlist_items (wishlist_id,product_id) VALUES (1,5),(1,10),(2,15);\n";

$out .= "SET FOREIGN_KEY_CHECKS=1;\n";

file_put_contents(__DIR__ . '/seed.sql', $out);
echo "seed.sql written\n";
