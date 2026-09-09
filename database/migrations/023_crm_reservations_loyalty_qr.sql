-- Migration 023: CRM, Reservations, Loyalty, Coupons/Promotions & QR Ordering Engine
-- SMARTRESTA Prompt 14

-- 1. Extend Customers Table
ALTER TABLE `customers`
  ADD COLUMN IF NOT EXISTS `branch_id` INT UNSIGNED NULL AFTER `id`,
  ADD COLUMN IF NOT EXISTS `customer_code` VARCHAR(50) NULL AFTER `branch_id`,
  ADD COLUMN IF NOT EXISTS `first_name` VARCHAR(50) NULL AFTER `name`,
  ADD COLUMN IF NOT EXISTS `last_name` VARCHAR(50) NULL AFTER `first_name`,
  ADD COLUMN IF NOT EXISTS `date_of_birth` DATE NULL AFTER `email`,
  ADD COLUMN IF NOT EXISTS `gender` ENUM('MALE', 'FEMALE', 'OTHER', 'UNDISCLOSED') DEFAULT 'UNDISCLOSED' AFTER `date_of_birth`,
  ADD COLUMN IF NOT EXISTS `company_name` VARCHAR(100) NULL AFTER `gender`,
  ADD COLUMN IF NOT EXISTS `notes` TEXT NULL AFTER `company_name`,
  ADD COLUMN IF NOT EXISTS `marketing_opt_in` TINYINT(1) DEFAULT 1 AFTER `notes`,
  ADD COLUMN IF NOT EXISTS `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`,
  ADD COLUMN IF NOT EXISTS `deleted_at` TIMESTAMP NULL DEFAULT NULL AFTER `updated_at`,
  ADD UNIQUE INDEX IF NOT EXISTS `idx_customer_code` (`customer_code`),
  ADD INDEX IF NOT EXISTS `idx_customer_branch` (`branch_id`),
  ADD INDEX IF NOT EXISTS `idx_customer_phone` (`phone`),
  ADD INDEX IF NOT EXISTS `idx_customer_email` (`email`);

-- 2. Extend Reservations Table
ALTER TABLE `reservations`
  ADD COLUMN IF NOT EXISTS `branch_id` INT UNSIGNED NULL AFTER `id`,
  ADD COLUMN IF NOT EXISTS `reservation_number` VARCHAR(50) NULL AFTER `branch_id`,
  ADD COLUMN IF NOT EXISTS `reservation_date` DATE NULL AFTER `table_id`,
  ADD COLUMN IF NOT EXISTS `duration_minutes` INT DEFAULT 90 AFTER `guest_count`,
  ADD COLUMN IF NOT EXISTS `created_by_user_id` INT UNSIGNED NULL AFTER `notes`,
  ADD COLUMN IF NOT EXISTS `confirmed_by_user_id` INT UNSIGNED NULL AFTER `created_by_user_id`,
  ADD COLUMN IF NOT EXISTS `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`,
  ADD COLUMN IF NOT EXISTS `cancelled_at` DATETIME NULL AFTER `updated_at`,
  ADD COLUMN IF NOT EXISTS `seated_at` DATETIME NULL AFTER `cancelled_at`,
  ADD COLUMN IF NOT EXISTS `completed_at` DATETIME NULL AFTER `seated_at`,
  MODIFY COLUMN `status` ENUM('REQUESTED', 'CONFIRMED', 'SEATED', 'COMPLETED', 'CANCELLED', 'NO_SHOW', 'EXPIRED', 'REJECTED') DEFAULT 'CONFIRMED',
  ADD UNIQUE INDEX IF NOT EXISTS `idx_res_number` (`reservation_number`),
  ADD INDEX IF NOT EXISTS `idx_res_branch` (`branch_id`),
  ADD INDEX IF NOT EXISTS `idx_res_date` (`reservation_date`),
  ADD INDEX IF NOT EXISTS `idx_res_status` (`status`);

-- 3. Loyalty Accounts Table
CREATE TABLE IF NOT EXISTS `loyalty_accounts` (
  `id` INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  `customer_id` INT UNSIGNED NOT NULL,
  `branch_id` INT UNSIGNED NULL,
  `points_balance` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `lifetime_earned` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `lifetime_redeemed` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `status` ENUM('ACTIVE', 'SUSPENDED') DEFAULT 'ACTIVE',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `idx_loyalty_cust` (`customer_id`),
  INDEX `idx_loyalty_branch` (`branch_id`),
  CONSTRAINT `fk_loyalty_cust` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Loyalty Transactions Ledger Table
CREATE TABLE IF NOT EXISTS `loyalty_transactions` (
  `id` INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  `customer_id` INT UNSIGNED NOT NULL,
  `branch_id` INT UNSIGNED NULL,
  `order_id` INT UNSIGNED NULL,
  `type` ENUM('EARN', 'REDEEM', 'ADJUST', 'EXPIRE', 'REVERSE', 'BONUS') NOT NULL,
  `points` DECIMAL(12,2) NOT NULL,
  `balance_before` DECIMAL(12,2) NOT NULL,
  `balance_after` DECIMAL(12,2) NOT NULL,
  `reason` VARCHAR(255) NULL,
  `created_by` INT UNSIGNED NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_l_tx_cust` (`customer_id`),
  INDEX `idx_l_tx_branch` (`branch_id`),
  INDEX `idx_l_tx_order` (`order_id`),
  INDEX `idx_l_tx_type` (`type`),
  CONSTRAINT `fk_l_tx_cust` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Extend Coupons Table
ALTER TABLE `coupons`
  ADD COLUMN IF NOT EXISTS `branch_id` INT UNSIGNED NULL AFTER `id`,
  ADD COLUMN IF NOT EXISTS `name` VARCHAR(100) NULL AFTER `code`,
  ADD COLUMN IF NOT EXISTS `description` VARCHAR(255) NULL AFTER `name`,
  ADD COLUMN IF NOT EXISTS `min_order_amount` DECIMAL(12,2) DEFAULT 0.00 AFTER `discount_amount`,
  ADD COLUMN IF NOT EXISTS `max_discount` DECIMAL(12,2) DEFAULT 0.00 AFTER `min_order_amount`,
  ADD COLUMN IF NOT EXISTS `usage_limit` INT DEFAULT 0 AFTER `max_discount`,
  ADD COLUMN IF NOT EXISTS `per_customer_limit` INT DEFAULT 0 AFTER `usage_limit`,
  ADD COLUMN IF NOT EXISTS `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER `is_active`,
  ADD COLUMN IF NOT EXISTS `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`,
  ADD INDEX IF NOT EXISTS `idx_coupon_branch` (`branch_id`),
  ADD INDEX IF NOT EXISTS `idx_coupon_code` (`code`);

-- 6. Extend Coupon Usage Table
ALTER TABLE `coupon_usage`
  ADD COLUMN IF NOT EXISTS `discount_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER `order_id`,
  ADD COLUMN IF NOT EXISTS `redeemed_by` INT UNSIGNED NULL AFTER `customer_id`,
  ADD INDEX IF NOT EXISTS `idx_usage_coupon` (`coupon_id`),
  ADD INDEX IF NOT EXISTS `idx_usage_customer` (`customer_id`),
  ADD INDEX IF NOT EXISTS `idx_usage_order` (`order_id`);

-- 7. Promotions Table
CREATE TABLE IF NOT EXISTS `promotions` (
  `id` INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  `branch_id` INT UNSIGNED NULL,
  `name` VARCHAR(100) NOT NULL,
  `code` VARCHAR(50) NULL,
  `discount_type` ENUM('PERCENTAGE', 'FIXED') DEFAULT 'PERCENTAGE',
  `discount_value` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `min_order_amount` DECIMAL(12,2) DEFAULT 0.00,
  `max_discount` DECIMAL(12,2) DEFAULT 0.00,
  `start_at` DATETIME NULL,
  `end_at` DATETIME NULL,
  `status` ENUM('ACTIVE', 'INACTIVE', 'EXPIRED') DEFAULT 'ACTIVE',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_promo_branch` (`branch_id`),
  INDEX `idx_promo_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. QR Table Tokens Table
CREATE TABLE IF NOT EXISTS `qr_table_tokens` (
  `id` INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  `branch_id` INT UNSIGNED NOT NULL,
  `table_id` INT UNSIGNED NOT NULL,
  `token` VARCHAR(64) NOT NULL,
  `status` ENUM('ACTIVE', 'REVOKED') DEFAULT 'ACTIVE',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `idx_qr_token` (`token`),
  UNIQUE KEY `idx_qr_branch_table` (`branch_id`, `table_id`),
  CONSTRAINT `fk_qr_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_qr_table` FOREIGN KEY (`table_id`) REFERENCES `restaurant_tables` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
