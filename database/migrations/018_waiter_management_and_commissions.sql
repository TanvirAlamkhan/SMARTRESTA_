-- SMARTRESTA Migration 018: Waiter Profiles, Commission Rules, Transactions, Adjustments & Payouts
USE `smartresta_db`;

-- 1. Create waiter_profiles Table
CREATE TABLE IF NOT EXISTS `waiter_profiles` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL UNIQUE,
  `employee_code` VARCHAR(40) NOT NULL UNIQUE,
  `branch_id` INT UNSIGNED NOT NULL DEFAULT 1,
  `commission_enabled` TINYINT(1) DEFAULT 1,
  `default_commission_rule_id` INT UNSIGNED NULL,
  `status` ENUM('ACTIVE', 'INACTIVE', 'SUSPENDED') DEFAULT 'ACTIVE',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Enhance waiter_assignments Table
ALTER TABLE `waiter_assignments`
  ADD COLUMN IF NOT EXISTS `branch_id` INT UNSIGNED NOT NULL DEFAULT 1 AFTER `id`,
  ADD COLUMN IF NOT EXISTS `dining_session_id` INT UNSIGNED NULL AFTER `table_id`,
  ADD COLUMN IF NOT EXISTS `order_id` INT UNSIGNED NULL AFTER `dining_session_id`,
  ADD COLUMN IF NOT EXISTS `assignment_type` ENUM('TABLE', 'SESSION', 'ORDER') DEFAULT 'TABLE' AFTER `order_id`,
  ADD COLUMN IF NOT EXISTS `assigned_by_user_id` INT UNSIGNED NULL AFTER `assignment_type`,
  ADD COLUMN IF NOT EXISTS `status` ENUM('ACTIVE', 'COMPLETED', 'CANCELLED') DEFAULT 'ACTIVE' AFTER `assigned_by_user_id`,
  ADD COLUMN IF NOT EXISTS `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `assigned_at`;

-- 3. Enhance commission_rules Table
ALTER TABLE `commission_rules`
  ADD COLUMN IF NOT EXISTS `branch_id` INT UNSIGNED NOT NULL DEFAULT 1 AFTER `id`,
  ADD COLUMN IF NOT EXISTS `code` VARCHAR(50) NULL AFTER `name`,
  ADD COLUMN IF NOT EXISTS `fixed_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER `rate`,
  ADD COLUMN IF NOT EXISTS `minimum_sales` DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER `fixed_amount`,
  ADD COLUMN IF NOT EXISTS `maximum_commission` DECIMAL(12,2) NULL AFTER `minimum_sales`,
  ADD COLUMN IF NOT EXISTS `commission_eligible` ENUM('ALL', 'PAID_ONLY', 'CONFIRMED_ONLY') DEFAULT 'PAID_ONLY' AFTER `maximum_commission`,
  ADD COLUMN IF NOT EXISTS `effective_from` DATETIME NULL AFTER `is_active`,
  ADD COLUMN IF NOT EXISTS `effective_to` DATETIME NULL AFTER `effective_from`,
  ADD COLUMN IF NOT EXISTS `created_by` INT UNSIGNED NULL AFTER `effective_to`,
  ADD COLUMN IF NOT EXISTS `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER `created_by`,
  ADD COLUMN IF NOT EXISTS `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`;

-- Seed Default 5% Commission Rule for Branch 1
INSERT INTO `commission_rules` (`id`, `branch_id`, `name`, `code`, `calculation_base`, `rate`, `fixed_amount`, `is_active`) VALUES
  (1, 1, 'Default Waiter 5% Net Sales Commission', 'COMM-RULE-5PCT', 'PERCENTAGE', 5.00, 0.00, 1)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`), `rate` = VALUES(`rate`), `is_active` = VALUES(`is_active`);

-- 4. Enhance commission_transactions Table
ALTER TABLE `commission_transactions`
  ADD COLUMN IF NOT EXISTS `branch_id` INT UNSIGNED NOT NULL DEFAULT 1 AFTER `id`,
  ADD COLUMN IF NOT EXISTS `order_item_id` INT UNSIGNED NULL AFTER `order_id`,
  ADD COLUMN IF NOT EXISTS `fixed_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER `rate`,
  ADD COLUMN IF NOT EXISTS `reason` VARCHAR(255) NULL AFTER `status`,
  ADD COLUMN IF NOT EXISTS `paid_by` INT UNSIGNED NULL AFTER `approved_at`,
  ADD COLUMN IF NOT EXISTS `paid_at` TIMESTAMP NULL AFTER `paid_by`,
  ADD COLUMN IF NOT EXISTS `idempotency_key` VARCHAR(100) NULL AFTER `paid_at`,
  ADD COLUMN IF NOT EXISTS `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`;

-- Update status ENUM on commission_transactions
ALTER TABLE `commission_transactions`
  MODIFY COLUMN `status` ENUM('PENDING', 'APPROVED', 'PAID', 'REJECTED', 'REVERSED', 'ADJUSTED') DEFAULT 'PENDING';

-- Unique Index for Idempotency
SET @dbname = DATABASE();
SET @tablename = 'commission_transactions';
SET @columnname = 'idx_comm_order_waiter';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND INDEX_NAME = @columnname
  ) > 0,
  'SELECT 1',
  'CREATE UNIQUE INDEX idx_comm_order_waiter ON commission_transactions(order_id, waiter_id);'
));
PREPARE createIndexTransaction FROM @preparedStatement;
EXECUTE createIndexTransaction;
DEALLOCATE PREPARE createIndexTransaction;

-- 5. Create commission_adjustments Table
CREATE TABLE IF NOT EXISTS `commission_adjustments` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `commission_transaction_id` INT UNSIGNED NOT NULL,
  `refund_id` INT UNSIGNED NULL,
  `reason` VARCHAR(255) NOT NULL,
  `adjustment_amount` DECIMAL(12,2) NOT NULL,
  `adjustment_type` ENUM('REFUND_REVERSAL', 'MANUAL_CORRECTION') DEFAULT 'REFUND_REVERSAL',
  `created_by` INT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`commission_transaction_id`) REFERENCES `commission_transactions`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Create commission_payouts Table
CREATE TABLE IF NOT EXISTS `commission_payouts` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `payout_number` VARCHAR(40) NOT NULL UNIQUE,
  `branch_id` INT UNSIGNED NOT NULL DEFAULT 1,
  `waiter_id` INT UNSIGNED NOT NULL,
  `amount` DECIMAL(12,2) NOT NULL,
  `payment_method` ENUM('CASH', 'BANK_TRANSFER', 'MOBILE_WALLET', 'OTHER') DEFAULT 'CASH',
  `reference_number` VARCHAR(100) NULL,
  `period_start` DATE NULL,
  `period_end` DATE NULL,
  `status` ENUM('PENDING', 'PROCESSED', 'CANCELLED') DEFAULT 'PROCESSED',
  `processed_by_user_id` INT UNSIGNED NOT NULL,
  `processed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `notes` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`waiter_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT,
  FOREIGN KEY (`processed_by_user_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Create commission_payout_items Table
CREATE TABLE IF NOT EXISTS `commission_payout_items` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `payout_id` INT UNSIGNED NOT NULL,
  `commission_transaction_id` INT UNSIGNED NOT NULL,
  `amount` DECIMAL(12,2) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`payout_id`) REFERENCES `commission_payouts`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`commission_transaction_id`) REFERENCES `commission_transactions`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Essential Indexes for Aggregations
CREATE INDEX IF NOT EXISTS `idx_comm_waiter_status` ON `commission_transactions` (`waiter_id`, `status`, `created_at`);
CREATE INDEX IF NOT EXISTS `idx_payout_waiter_date` ON `commission_payouts` (`waiter_id`, `created_at`);
