-- SMARTRESTA Migration 017: Payment Methods, Allocations, Refunds, Receipts & Invoices Schema Extensions
USE `smartresta_db`;

-- 1. Enhance payment_methods Table
ALTER TABLE `payment_methods`
  ADD COLUMN IF NOT EXISTS `type` ENUM('CASH', 'CARD', 'MOBILE_FINANCIAL_SERVICE', 'OTHER') DEFAULT 'CASH' AFTER `code`,
  ADD COLUMN IF NOT EXISTS `requires_reference` TINYINT(1) DEFAULT 0 AFTER `type`,
  ADD COLUMN IF NOT EXISTS `requires_transaction_id` TINYINT(1) DEFAULT 0 AFTER `requires_reference`,
  ADD COLUMN IF NOT EXISTS `display_order` INT DEFAULT 0 AFTER `requires_transaction_id`,
  ADD COLUMN IF NOT EXISTS `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER `display_order`,
  ADD COLUMN IF NOT EXISTS `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`;

-- Seed Default Payment Methods
INSERT INTO `payment_methods` (`id`, `name`, `code`, `type`, `requires_reference`, `requires_transaction_id`, `display_order`, `is_active`) VALUES
  (1, 'Cash', 'CASH', 'CASH', 0, 0, 1, 1),
  (2, 'Debit/Credit Card', 'CARD', 'CARD', 1, 1, 2, 1),
  (3, 'bKash Mobile Money', 'BKASH', 'MOBILE_FINANCIAL_SERVICE', 1, 1, 3, 1),
  (4, 'Nagad Mobile Money', 'NAGAD', 'MOBILE_FINANCIAL_SERVICE', 1, 1, 4, 1),
  (5, 'Rocket Mobile Money', 'ROCKET', 'MOBILE_FINANCIAL_SERVICE', 1, 1, 5, 1),
  (6, 'Other Payment', 'OTHER', 'OTHER', 0, 0, 6, 1)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`), `type` = VALUES(`type`), `requires_reference` = VALUES(`requires_reference`);

-- 2. Enhance payments Table
ALTER TABLE `payments`
  ADD COLUMN IF NOT EXISTS `payment_number` VARCHAR(40) NULL AFTER `id`,
  ADD COLUMN IF NOT EXISTS `branch_id` INT UNSIGNED NOT NULL DEFAULT 1 AFTER `payment_number`,
  ADD COLUMN IF NOT EXISTS `dining_session_id` INT UNSIGNED NULL AFTER `order_id`,
  ADD COLUMN IF NOT EXISTS `currency` VARCHAR(10) DEFAULT 'BDT' AFTER `amount`,
  ADD COLUMN IF NOT EXISTS `idempotency_key` VARCHAR(100) NULL AFTER `status`,
  ADD COLUMN IF NOT EXISTS `notes` TEXT NULL AFTER `idempotency_key`,
  ADD COLUMN IF NOT EXISTS `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`;

SET @dbname = DATABASE();
SET @tablename = 'payments';
SET @columnname = 'idx_payment_number';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND INDEX_NAME = @columnname
  ) > 0,
  'SELECT 1',
  'CREATE UNIQUE INDEX idx_payment_number ON payments(payment_number);'
));
PREPARE createIndexTransaction FROM @preparedStatement;
EXECUTE createIndexTransaction;
DEALLOCATE PREPARE createIndexTransaction;

-- 3. Create payment_allocations Table
CREATE TABLE IF NOT EXISTS `payment_allocations` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `payment_id` INT UNSIGNED NOT NULL,
  `order_id` INT UNSIGNED NOT NULL,
  `amount` DECIMAL(12,2) NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`payment_id`) REFERENCES `payments`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Enhance refunds Table
ALTER TABLE `refunds`
  ADD COLUMN IF NOT EXISTS `refund_number` VARCHAR(40) NULL AFTER `id`,
  ADD COLUMN IF NOT EXISTS `branch_id` INT UNSIGNED NOT NULL DEFAULT 1 AFTER `refund_number`,
  ADD COLUMN IF NOT EXISTS `status` ENUM('PENDING', 'APPROVED', 'PROCESSED', 'REJECTED') DEFAULT 'PROCESSED' AFTER `refund_amount`,
  ADD COLUMN IF NOT EXISTS `approved_by_user_id` INT UNSIGNED NULL AFTER `status`,
  ADD COLUMN IF NOT EXISTS `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`;

-- 5. Create invoices Table
CREATE TABLE IF NOT EXISTS `invoices` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `invoice_number` VARCHAR(40) NOT NULL UNIQUE,
  `branch_id` INT UNSIGNED NOT NULL DEFAULT 1,
  `order_id` INT UNSIGNED NOT NULL,
  `dining_session_id` INT UNSIGNED NULL,
  `customer_id` INT UNSIGNED NULL,
  `subtotal` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `discount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `tax` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `service_charge` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `total` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `paid_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `outstanding_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `status` ENUM('UNPAID', 'PARTIAL', 'PAID', 'CANCELLED') DEFAULT 'UNPAID',
  `invoice_data` JSON NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Create receipts Table
CREATE TABLE IF NOT EXISTS `receipts` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `receipt_number` VARCHAR(40) NOT NULL UNIQUE,
  `branch_id` INT UNSIGNED NOT NULL DEFAULT 1,
  `order_id` INT UNSIGNED NOT NULL,
  `payment_id` INT UNSIGNED NOT NULL,
  `amount_paid` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `receipt_data` JSON NULL,
  `created_by` INT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`payment_id`) REFERENCES `payments`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
