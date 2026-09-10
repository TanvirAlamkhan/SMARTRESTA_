-- SMARTRESTA Migration 025: POS ORDER PLACE & Coupon Engine Integration
USE `smartresta_db`;

-- 1. Extend Orders Table for Coupons & Discount Tracking
ALTER TABLE `orders`
  ADD COLUMN IF NOT EXISTS `coupon_id` INT UNSIGNED NULL AFTER `customer_id`,
  ADD COLUMN IF NOT EXISTS `coupon_code` VARCHAR(50) NULL AFTER `coupon_id`,
  ADD INDEX IF NOT EXISTS `idx_orders_coupon` (`coupon_id`),
  ADD INDEX IF NOT EXISTS `idx_orders_coupon_code` (`coupon_code`);

-- 2. Ensure Coupons Table Schema Completeness
CREATE TABLE IF NOT EXISTS `coupons` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `branch_id` INT UNSIGNED NULL,
  `code` VARCHAR(50) NOT NULL UNIQUE,
  `name` VARCHAR(100) NULL,
  `description` VARCHAR(255) NULL,
  `discount_type` ENUM('PERCENTAGE', 'FIXED') DEFAULT 'PERCENTAGE',
  `discount_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `min_order_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `max_discount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `valid_from` DATETIME NULL,
  `valid_until` DATETIME NULL,
  `usage_limit` INT NOT NULL DEFAULT 0,
  `per_customer_limit` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_coupon_branch` (`branch_id`),
  INDEX `idx_coupon_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Ensure Coupon Usage Table Schema Completeness
CREATE TABLE IF NOT EXISTS `coupon_usage` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `coupon_id` INT UNSIGNED NOT NULL,
  `order_id` INT UNSIGNED NOT NULL,
  `customer_id` INT UNSIGNED NULL,
  `discount_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `redeemed_by` INT UNSIGNED NULL,
  `used_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_usage_coupon` (`coupon_id`),
  INDEX `idx_usage_customer` (`customer_id`),
  INDEX `idx_usage_order` (`order_id`),
  CONSTRAINT `fk_usage_coupon` FOREIGN KEY (`coupon_id`) REFERENCES `coupons`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_usage_order` FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Seed Essential Active Demo Coupons
INSERT IGNORE INTO `coupons` 
  (`code`, `name`, `description`, `discount_type`, `discount_amount`, `min_order_amount`, `max_discount`, `valid_from`, `valid_until`, `usage_limit`, `per_customer_limit`, `is_active`)
VALUES 
  ('SAVE100', 'Flat ৳100 Discount', 'Get ৳100 off on orders ৳500 or more', 'FIXED', 100.00, 500.00, 100.00, NOW(), DATE_ADD(NOW(), INTERVAL 1 YEAR), 1000, 10, 1),
  ('SAVE20', '20% Mega Promo', 'Get 20% off on orders ৳300 or more', 'PERCENTAGE', 20.00, 300.00, 500.00, NOW(), DATE_ADD(NOW(), INTERVAL 1 YEAR), 1000, 10, 1),
  ('WELCOME10', '10% Welcome Offer', '10% discount for all dining guests', 'PERCENTAGE', 10.00, 200.00, 300.00, NOW(), DATE_ADD(NOW(), INTERVAL 1 YEAR), 5000, 5, 1);
