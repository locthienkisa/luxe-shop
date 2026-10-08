-- Luxe Shop schema — MySQL 8 / MariaDB 10.11+
-- Charset: utf8mb4

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `luxe_shop` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `luxe_shop`;

DROP TABLE IF EXISTS `api_tokens`;
DROP TABLE IF EXISTS `admin_logs`;
DROP TABLE IF EXISTS `settings`;
DROP TABLE IF EXISTS `banners`;
DROP TABLE IF EXISTS `notifications`;
DROP TABLE IF EXISTS `review_images`;
DROP TABLE IF EXISTS `reviews`;
DROP TABLE IF EXISTS `payments`;
DROP TABLE IF EXISTS `order_status_history`;
DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `coupon_usages`;
DROP TABLE IF EXISTS `coupons`;
DROP TABLE IF EXISTS `wishlist_items`;
DROP TABLE IF EXISTS `wishlists`;
DROP TABLE IF EXISTS `cart_items`;
DROP TABLE IF EXISTS `carts`;
DROP TABLE IF EXISTS `inventory`;
DROP TABLE IF EXISTS `product_variants`;
DROP TABLE IF EXISTS `product_images`;
DROP TABLE IF EXISTS `product_categories`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `addresses`;
DROP TABLE IF EXISTS `login_attempts`;
DROP TABLE IF EXISTS `password_resets`;
DROP TABLE IF EXISTS `role_permissions`;
DROP TABLE IF EXISTS `permissions`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `roles`;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE `roles` (
  `id` TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `slug` VARCHAR(32) NOT NULL,
  `name` VARCHAR(50) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_roles_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `permissions` (
  `id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `slug` VARCHAR(64) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_permissions_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `role_permissions` (
  `role_id` TINYINT UNSIGNED NOT NULL,
  `permission_id` SMALLINT UNSIGNED NOT NULL,
  PRIMARY KEY (`role_id`, `permission_id`),
  CONSTRAINT `fk_rp_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_rp_perm` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_id` TINYINT UNSIGNED NOT NULL,
  `email` VARCHAR(190) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(120) NOT NULL,
  `phone` VARCHAR(20) NULL DEFAULT NULL,
  `avatar` VARCHAR(255) NULL DEFAULT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `email_verified_at` DATETIME NULL DEFAULT NULL,
  `must_change_password` TINYINT(1) NOT NULL DEFAULT 0,
  `last_login_at` DATETIME NULL DEFAULT NULL,
  `last_login_ip` VARCHAR(45) NULL DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_users_email` (`email`),
  KEY `idx_users_role` (`role_id`),
  KEY `idx_users_name` (`full_name`),
  CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `password_resets` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `token_hash` CHAR(64) NOT NULL,
  `expires_at` DATETIME NOT NULL,
  `used_at` DATETIME NULL DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_pr_token` (`token_hash`),
  KEY `idx_pr_user` (`user_id`),
  CONSTRAINT `fk_pr_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `login_attempts` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(190) NOT NULL,
  `ip` VARCHAR(45) NOT NULL,
  `success` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_la_email_time` (`email`, `created_at`),
  KEY `idx_la_ip_time` (`ip`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `addresses` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `label` VARCHAR(50) NULL DEFAULT NULL,
  `full_name` VARCHAR(120) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `line1` VARCHAR(200) NOT NULL,
  `line2` VARCHAR(200) NULL DEFAULT NULL,
  `ward` VARCHAR(100) NULL DEFAULT NULL,
  `district` VARCHAR(100) NOT NULL,
  `city` VARCHAR(100) NOT NULL,
  `postal_code` VARCHAR(20) NULL DEFAULT NULL,
  `is_default` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_addr_user` (`user_id`),
  CONSTRAINT `fk_addr_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `categories` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_id` INT UNSIGNED NULL DEFAULT NULL,
  `name` VARCHAR(120) NOT NULL,
  `slug` VARCHAR(140) NOT NULL,
  `description` TEXT NULL,
  `image` VARCHAR(255) NULL DEFAULT NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `meta_title` VARCHAR(160) NULL DEFAULT NULL,
  `meta_description` VARCHAR(255) NULL DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_categories_slug` (`slug`),
  KEY `idx_categories_parent` (`parent_id`),
  CONSTRAINT `fk_cat_parent` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `products` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(200) NOT NULL,
  `slug` VARCHAR(220) NOT NULL,
  `sku` VARCHAR(64) NOT NULL,
  `brand` VARCHAR(80) NULL DEFAULT NULL,
  `short_description` VARCHAR(500) NULL DEFAULT NULL,
  `description` MEDIUMTEXT NULL,
  `price` DECIMAL(12,2) NOT NULL,
  `sale_price` DECIMAL(12,2) NULL DEFAULT NULL,
  `stock` INT NOT NULL DEFAULT 0,
  `has_variants` TINYINT(1) NOT NULL DEFAULT 0,
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `rating_avg` DECIMAL(3,2) NOT NULL DEFAULT 0.00,
  `rating_count` INT NOT NULL DEFAULT 0,
  `sold_count` INT NOT NULL DEFAULT 0,
  `meta_title` VARCHAR(160) NULL DEFAULT NULL,
  `meta_description` VARCHAR(255) NULL DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_products_slug` (`slug`),
  UNIQUE KEY `uniq_products_sku` (`sku`),
  KEY `idx_products_price` (`price`),
  KEY `idx_products_brand` (`brand`),
  KEY `idx_products_featured` (`is_featured`, `is_active`),
  KEY `idx_products_created` (`created_at`),
  KEY `idx_products_sold` (`sold_count`),
  KEY `idx_products_rating` (`rating_avg`),
  FULLTEXT KEY `ft_products_search` (`name`, `sku`, `brand`, `short_description`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `product_categories` (
  `product_id` INT UNSIGNED NOT NULL,
  `category_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`product_id`, `category_id`),
  KEY `idx_pc_cat` (`category_id`),
  CONSTRAINT `fk_pc_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pc_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `product_images` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` INT UNSIGNED NOT NULL,
  `path` VARCHAR(255) NOT NULL,
  `alt` VARCHAR(160) NULL DEFAULT NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_primary` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_pi_product` (`product_id`),
  CONSTRAINT `fk_pi_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `product_variants` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` INT UNSIGNED NOT NULL,
  `sku` VARCHAR(64) NOT NULL,
  `size` VARCHAR(32) NULL DEFAULT NULL,
  `color` VARCHAR(32) NULL DEFAULT NULL,
  `price` DECIMAL(12,2) NULL DEFAULT NULL,
  `stock` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_variant_sku` (`sku`),
  KEY `idx_pv_product` (`product_id`),
  CONSTRAINT `fk_pv_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `inventory` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` INT UNSIGNED NOT NULL,
  `variant_id` INT UNSIGNED NULL DEFAULT NULL,
  `change_qty` INT NOT NULL,
  `reason` ENUM('import','sale','adjust','cancel_restore','return') NOT NULL,
  `reference_type` VARCHAR(32) NULL DEFAULT NULL,
  `reference_id` INT UNSIGNED NULL DEFAULT NULL,
  `note` VARCHAR(255) NULL DEFAULT NULL,
  `created_by` INT UNSIGNED NULL DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_inv_product` (`product_id`, `created_at`),
  KEY `idx_inv_variant` (`variant_id`),
  CONSTRAINT `fk_inv_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_inv_variant` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_inv_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `coupons` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` VARCHAR(40) NOT NULL,
  `discount_type` ENUM('percent','fixed') NOT NULL,
  `discount_value` DECIMAL(12,2) NOT NULL,
  `min_order` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `max_discount` DECIMAL(12,2) NULL DEFAULT NULL,
  `starts_at` DATETIME NOT NULL,
  `ends_at` DATETIME NOT NULL,
  `usage_limit` INT UNSIGNED NULL DEFAULT NULL,
  `per_user_limit` INT UNSIGNED NOT NULL DEFAULT 1,
  `used_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_coupons_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `carts` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NULL DEFAULT NULL,
  `session_id` VARCHAR(64) NULL DEFAULT NULL,
  `coupon_id` INT UNSIGNED NULL DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_cart_user` (`user_id`),
  KEY `idx_cart_session` (`session_id`),
  CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cart_coupon` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cart_items` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `cart_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NOT NULL,
  `variant_id` INT UNSIGNED NULL DEFAULT NULL,
  `quantity` INT UNSIGNED NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_cart_item` (`cart_id`, `product_id`, `variant_id`),
  KEY `idx_ci_product` (`product_id`),
  CONSTRAINT `fk_ci_cart` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ci_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ci_variant` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `wishlists` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_wl_user` (`user_id`),
  CONSTRAINT `fk_wl_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `wishlist_items` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `wishlist_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_wl_item` (`wishlist_id`, `product_id`),
  CONSTRAINT `fk_wli_wl` FOREIGN KEY (`wishlist_id`) REFERENCES `wishlists` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_wli_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `orders` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_number` VARCHAR(24) NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `address_id` INT UNSIGNED NULL DEFAULT NULL,
  `shipping_name` VARCHAR(120) NOT NULL,
  `shipping_phone` VARCHAR(20) NOT NULL,
  `shipping_address` VARCHAR(500) NOT NULL,
  `subtotal` DECIMAL(12,2) NOT NULL,
  `discount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `shipping_fee` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `total` DECIMAL(12,2) NOT NULL,
  `coupon_id` INT UNSIGNED NULL DEFAULT NULL,
  `coupon_code` VARCHAR(40) NULL DEFAULT NULL,
  `status` ENUM('pending','confirmed','processing','shipping','delivered','cancelled','refunded') NOT NULL DEFAULT 'pending',
  `payment_status` ENUM('unpaid','paid','refunded','failed') NOT NULL DEFAULT 'unpaid',
  `payment_method` VARCHAR(32) NOT NULL DEFAULT 'cod',
  `notes` TEXT NULL,
  `cancelled_at` DATETIME NULL DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_order_number` (`order_number`),
  KEY `idx_orders_user` (`user_id`, `created_at`),
  KEY `idx_orders_status` (`status`),
  KEY `idx_orders_created` (`created_at`),
  CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `fk_orders_addr` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_orders_coupon` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `order_items` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NOT NULL,
  `variant_id` INT UNSIGNED NULL DEFAULT NULL,
  `product_name` VARCHAR(200) NOT NULL,
  `sku` VARCHAR(64) NOT NULL,
  `size` VARCHAR(32) NULL DEFAULT NULL,
  `color` VARCHAR(32) NULL DEFAULT NULL,
  `unit_price` DECIMAL(12,2) NOT NULL,
  `quantity` INT UNSIGNED NOT NULL,
  `line_total` DECIMAL(12,2) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_oi_order` (`order_id`),
  KEY `idx_oi_product` (`product_id`),
  CONSTRAINT `fk_oi_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `order_status_history` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` INT UNSIGNED NOT NULL,
  `from_status` VARCHAR(32) NULL DEFAULT NULL,
  `to_status` VARCHAR(32) NOT NULL,
  `note` VARCHAR(255) NULL DEFAULT NULL,
  `created_by` INT UNSIGNED NULL DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_osh_order` (`order_id`),
  CONSTRAINT `fk_osh_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_osh_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `payments` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` INT UNSIGNED NOT NULL,
  `method` VARCHAR(32) NOT NULL,
  `amount` DECIMAL(12,2) NOT NULL,
  `status` ENUM('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending',
  `gateway_txn_id` VARCHAR(100) NULL DEFAULT NULL,
  `payload_json` JSON NULL DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_pay_order` (`order_id`),
  CONSTRAINT `fk_pay_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `coupon_usages` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `coupon_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `order_id` INT UNSIGNED NULL DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_cu_coupon_user` (`coupon_id`, `user_id`),
  CONSTRAINT `fk_cu_coupon` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cu_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cu_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `reviews` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `order_id` INT UNSIGNED NULL DEFAULT NULL,
  `rating` TINYINT UNSIGNED NOT NULL,
  `content` TEXT NULL,
  `status` ENUM('pending','approved','hidden') NOT NULL DEFAULT 'pending',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_review` (`product_id`, `user_id`, `order_id`),
  KEY `idx_reviews_product` (`product_id`, `status`),
  CONSTRAINT `fk_rev_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_rev_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_rev_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  CONSTRAINT `chk_rating` CHECK (`rating` BETWEEN 1 AND 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `review_images` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `review_id` INT UNSIGNED NOT NULL,
  `path` VARCHAR(255) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ri_review` (`review_id`),
  CONSTRAINT `fk_ri_review` FOREIGN KEY (`review_id`) REFERENCES `reviews` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `notifications` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `title` VARCHAR(160) NOT NULL,
  `body` VARCHAR(500) NOT NULL,
  `link` VARCHAR(255) NULL DEFAULT NULL,
  `is_read` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_notif_user` (`user_id`, `is_read`),
  CONSTRAINT `fk_notif_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `banners` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(160) NOT NULL,
  `subtitle` VARCHAR(255) NULL DEFAULT NULL,
  `image` VARCHAR(255) NOT NULL,
  `link` VARCHAR(255) NULL DEFAULT NULL,
  `position` ENUM('hero','promo','sidebar') NOT NULL DEFAULT 'hero',
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `starts_at` DATETIME NULL DEFAULT NULL,
  `ends_at` DATETIME NULL DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_banners_pos` (`position`, `is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `settings` (
  `key` VARCHAR(64) NOT NULL,
  `value` TEXT NULL,
  `updated_at` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `admin_logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NULL DEFAULT NULL,
  `action` VARCHAR(80) NOT NULL,
  `entity` VARCHAR(40) NULL DEFAULT NULL,
  `entity_id` INT UNSIGNED NULL DEFAULT NULL,
  `ip` VARCHAR(45) NULL DEFAULT NULL,
  `user_agent` VARCHAR(255) NULL DEFAULT NULL,
  `meta_json` JSON NULL DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_al_user` (`user_id`, `created_at`),
  KEY `idx_al_action` (`action`),
  CONSTRAINT `fk_al_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `api_tokens` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `token_hash` CHAR(64) NOT NULL,
  `expires_at` DATETIME NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_api_token` (`token_hash`),
  KEY `idx_api_user` (`user_id`),
  CONSTRAINT `fk_api_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
