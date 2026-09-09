-- =================================================================
-- SMARTRESTA Migration 022: Finance, Expenses, Shifts, Cash & Day Closing
-- =================================================================

USE `smartresta_db`;

-- 1. Cash Drawers Table
CREATE TABLE IF NOT EXISTS `cash_drawers` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `branch_id` INT NOT NULL DEFAULT 1,
    `name` VARCHAR(100) NOT NULL,
    `code` VARCHAR(30) NOT NULL,
    `location` VARCHAR(100) DEFAULT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_drawer_branch_code` (`branch_id`, `code`),
    KEY `idx_drawers_branch` (`branch_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default Cash Drawer if empty
INSERT IGNORE INTO `cash_drawers` (`id`, `branch_id`, `name`, `code`, `location`, `is_active`) 
VALUES (1, 1, 'Main POS Register Cash Drawer', 'CDR-01', 'Front Counter POS 1', 1);

-- 2. Alter Shifts Table (Safely add columns if missing)
SET @dbname = DATABASE();
SET @tablename = "shifts";
SET @columnname = "branch_id";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      TABLE_SCHEMA = @dbname
      AND TABLE_NAME = @tablename
      AND COLUMN_NAME = @columnname
  ) > 0,
  "SELECT 1",
  "ALTER TABLE `shifts` 
    ADD COLUMN `branch_id` INT NOT NULL DEFAULT 1 AFTER `id`,
    ADD COLUMN `cash_drawer_id` INT DEFAULT 1 AFTER `user_id`,
    ADD COLUMN `opening_cash` DECIMAL(14,2) NOT NULL DEFAULT 0.00 AFTER `opening_balance`,
    ADD COLUMN `expected_cash` DECIMAL(14,2) NOT NULL DEFAULT 0.00 AFTER `opening_cash`,
    ADD COLUMN `actual_cash` DECIMAL(14,2) NOT NULL DEFAULT 0.00 AFTER `closing_balance`,
    ADD COLUMN `cash_difference` DECIMAL(14,2) NOT NULL DEFAULT 0.00 AFTER `actual_cash`,
    ADD COLUMN `notes` VARCHAR(255) DEFAULT NULL AFTER `status`,
    ADD COLUMN `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP AFTER `notes`,
    ADD COLUMN `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`;"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- 3. Alter Expenses Table (Safely add columns if missing)
SET @columnname = "branch_id";
SET @tablename = "expenses";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      TABLE_SCHEMA = @dbname
      AND TABLE_NAME = @tablename
      AND COLUMN_NAME = @columnname
  ) > 0,
  "SELECT 1",
  "ALTER TABLE `expenses` 
    ADD COLUMN `branch_id` INT NOT NULL DEFAULT 1 AFTER `id`,
    ADD COLUMN `expense_category_id` INT DEFAULT NULL AFTER `category_id`,
    ADD COLUMN `expense_date` DATE DEFAULT NULL AFTER `amount`,
    ADD COLUMN `payment_method_id` INT DEFAULT 1 AFTER `expense_date`,
    ADD COLUMN `supplier_id` INT DEFAULT NULL AFTER `payment_method_id`,
    ADD COLUMN `description` TEXT DEFAULT NULL AFTER `title`,
    ADD COLUMN `reference_number` VARCHAR(100) DEFAULT NULL AFTER `description`,
    ADD COLUMN `status` ENUM('DRAFT', 'SUBMITTED', 'APPROVED', 'PAID', 'REJECTED', 'CANCELLED') NOT NULL DEFAULT 'APPROVED' AFTER `reference_number`,
    ADD COLUMN `approved_by_user_id` INT DEFAULT NULL AFTER `recorded_by_user_id`,
    ADD COLUMN `approved_at` DATETIME DEFAULT NULL AFTER `approved_by_user_id`,
    ADD COLUMN `paid_at` DATETIME DEFAULT NULL AFTER `approved_at`,
    ADD COLUMN `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`;"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- 4. Cash Movements Ledger Table
CREATE TABLE IF NOT EXISTS `cash_movements` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `branch_id` INT NOT NULL DEFAULT 1,
    `shift_id` INT DEFAULT NULL,
    `cash_drawer_id` INT DEFAULT 1,
    `movement_type` ENUM('OPENING_BALANCE', 'CASH_SALE', 'CASH_REFUND', 'CASH_EXPENSE', 'CASH_IN', 'CASH_OUT', 'CASH_TRANSFER', 'CASH_ADJUSTMENT', 'CLOSING_BALANCE') NOT NULL,
    `amount` DECIMAL(14,2) NOT NULL,
    `direction` ENUM('IN', 'OUT') NOT NULL,
    `reference_type` VARCHAR(50) DEFAULT NULL,
    `reference_id` INT DEFAULT NULL,
    `user_id` INT NOT NULL,
    `reason` VARCHAR(255) DEFAULT NULL,
    `notes` TEXT DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    KEY `idx_cm_branch_shift` (`branch_id`, `shift_id`),
    KEY `idx_cm_drawer` (`cash_drawer_id`),
    KEY `idx_cm_type_created` (`movement_type`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Payment Reconciliations Table
CREATE TABLE IF NOT EXISTS `reconciliations` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `branch_id` INT NOT NULL DEFAULT 1,
    `shift_id` INT DEFAULT NULL,
    `business_date` DATE NOT NULL,
    `payment_method_id` INT NOT NULL,
    `expected_amount` DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    `actual_amount` DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    `difference` DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    `status` ENUM('PENDING', 'VERIFIED', 'DISCREPANCY') NOT NULL DEFAULT 'PENDING',
    `notes` TEXT DEFAULT NULL,
    `verified_by_user_id` INT DEFAULT NULL,
    `verified_at` DATETIME DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    KEY `idx_recon_branch_date` (`branch_id`, `business_date`),
    KEY `idx_recon_method` (`payment_method_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Payment Settlements Table
CREATE TABLE IF NOT EXISTS `settlements` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `branch_id` INT NOT NULL DEFAULT 1,
    `payment_method_id` INT NOT NULL,
    `settlement_reference` VARCHAR(100) NOT NULL,
    `settlement_date` DATE NOT NULL,
    `expected_amount` DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    `actual_amount` DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    `difference` DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    `status` ENUM('PENDING', 'SUBMITTED', 'VERIFIED', 'DISCREPANCY', 'CLOSED') NOT NULL DEFAULT 'PENDING',
    `created_by_user_id` INT NOT NULL,
    `verified_by_user_id` INT DEFAULT NULL,
    `verified_at` DATETIME DEFAULT NULL,
    `notes` TEXT DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    KEY `idx_set_branch_date` (`branch_id`, `settlement_date`),
    KEY `idx_set_method` (`payment_method_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Business Day Closings Table
CREATE TABLE IF NOT EXISTS `day_closings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `branch_id` INT NOT NULL DEFAULT 1,
    `business_date` DATE NOT NULL,
    `status` ENUM('OPEN', 'REVIEW', 'CLOSING', 'CLOSED') NOT NULL DEFAULT 'CLOSED',
    `gross_sales` DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    `discounts` DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    `tax` DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    `service_charge` DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    `refunds` DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    `net_sales` DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    `cash_collected` DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    `non_cash_collected` DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    `total_expenses` DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    `total_purchases` DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    `total_commissions` DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    `cash_difference` DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    `snapshot_data` LONGTEXT DEFAULT NULL,
    `opened_at` DATETIME DEFAULT NULL,
    `closed_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `closed_by_user_id` INT NOT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_day_closing_branch_date` (`branch_id`, `business_date`),
    KEY `idx_dc_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Financial Adjustments Table
CREATE TABLE IF NOT EXISTS `financial_adjustments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `branch_id` INT NOT NULL DEFAULT 1,
    `adjustment_type` VARCHAR(50) NOT NULL,
    `amount` DECIMAL(14,2) NOT NULL,
    `reason` VARCHAR(255) NOT NULL,
    `created_by_user_id` INT NOT NULL,
    `approved_by_user_id` INT DEFAULT NULL,
    `status` ENUM('PENDING', 'APPROVED', 'REJECTED') NOT NULL DEFAULT 'APPROVED',
    `notes` TEXT DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    KEY `idx_fa_branch_date` (`branch_id`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Seed Default Expense Categories
INSERT IGNORE INTO `expense_categories` (`id`, `name`) VALUES
(1, 'Utilities (Electricity, Water, Gas)'),
(2, 'Rent & Lease'),
(3, 'Cleaning & Maintenance'),
(4, 'Transportation & Freight'),
(5, 'Supplies & Consumables'),
(6, 'Marketing & Promotion'),
(7, 'Staff Expenses'),
(8, 'Other Operating Expenses');
