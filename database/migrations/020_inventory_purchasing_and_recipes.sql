-- SMARTRESTA Migration 020: Inventory, Purchasing, Recipe Costing & Stock Management Schema Extensions
USE `smartresta_db`;

-- 1. Enhance ingredients Table
ALTER TABLE `ingredients`
  ADD COLUMN IF NOT EXISTS `branch_id` INT UNSIGNED NOT NULL DEFAULT 1 AFTER `id`,
  ADD COLUMN IF NOT EXISTS `sku` VARCHAR(50) NULL AFTER `name`,
  ADD COLUMN IF NOT EXISTS `ingredient_code` VARCHAR(50) NULL AFTER `sku`,
  ADD COLUMN IF NOT EXISTS `category_id` INT UNSIGNED NULL AFTER `ingredient_code`,
  ADD COLUMN IF NOT EXISTS `base_unit` VARCHAR(20) NOT NULL DEFAULT 'kg' AFTER `category_id`,
  ADD COLUMN IF NOT EXISTS `purchase_unit` VARCHAR(20) NOT NULL DEFAULT 'kg' AFTER `base_unit`,
  ADD COLUMN IF NOT EXISTS `conversion_factor` DECIMAL(12,4) NOT NULL DEFAULT 1.0000 AFTER `purchase_unit`,
  ADD COLUMN IF NOT EXISTS `reorder_level` DECIMAL(12,2) NOT NULL DEFAULT 10.00 AFTER `min_stock`,
  ADD COLUMN IF NOT EXISTS `max_stock` DECIMAL(12,2) NOT NULL DEFAULT 100.00 AFTER `reorder_level`,
  ADD COLUMN IF NOT EXISTS `average_cost` DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER `cost_per_unit`,
  ADD COLUMN IF NOT EXISTS `last_purchase_cost` DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER `average_cost`,
  ADD COLUMN IF NOT EXISTS `preferred_supplier_id` INT UNSIGNED NULL AFTER `last_purchase_cost`,
  ADD COLUMN IF NOT EXISTS `status` ENUM('ACTIVE', 'INACTIVE') DEFAULT 'ACTIVE' AFTER `preferred_supplier_id`,
  ADD COLUMN IF NOT EXISTS `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER `status`,
  ADD COLUMN IF NOT EXISTS `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`;

-- 2. Create ingredient_categories Table
CREATE TABLE IF NOT EXISTS `ingredient_categories` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `branch_id` INT UNSIGNED NOT NULL DEFAULT 1,
  `name` VARCHAR(100) NOT NULL,
  `description` TEXT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Create inventory_locations Table
CREATE TABLE IF NOT EXISTS `inventory_locations` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `branch_id` INT UNSIGNED NOT NULL DEFAULT 1,
  `name` VARCHAR(100) NOT NULL,
  `code` VARCHAR(30) NOT NULL,
  `location_type` ENUM('MAIN_STORE', 'KITCHEN_STORE', 'BAR_STORE', 'COLD_STORAGE', 'DRY_STORAGE') DEFAULT 'MAIN_STORE',
  `status` ENUM('ACTIVE', 'INACTIVE') DEFAULT 'ACTIVE',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Create inventory_stock Table
CREATE TABLE IF NOT EXISTS `inventory_stock` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `ingredient_id` INT UNSIGNED NOT NULL,
  `location_id` INT UNSIGNED NOT NULL,
  `quantity_on_hand` DECIMAL(12,4) NOT NULL DEFAULT 0.0000,
  `reserved_quantity` DECIMAL(12,4) NOT NULL DEFAULT 0.0000,
  `average_cost` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `last_purchase_cost` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `idx_ing_location` (`ingredient_id`, `location_id`),
  FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`location_id`) REFERENCES `inventory_locations`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Create suppliers Table
CREATE TABLE IF NOT EXISTS `suppliers` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `branch_id` INT UNSIGNED NOT NULL DEFAULT 1,
  `supplier_code` VARCHAR(50) NOT NULL UNIQUE,
  `name` VARCHAR(150) NOT NULL,
  `contact_person` VARCHAR(100) NULL,
  `email` VARCHAR(100) NULL,
  `phone` VARCHAR(30) NULL,
  `address` TEXT NULL,
  `tax_number` VARCHAR(50) NULL,
  `payment_terms` VARCHAR(100) NULL,
  `status` ENUM('ACTIVE', 'INACTIVE') DEFAULT 'ACTIVE',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Create purchase_orders & purchase_order_items Tables
CREATE TABLE IF NOT EXISTS `purchase_orders` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `branch_id` INT UNSIGNED NOT NULL DEFAULT 1,
  `po_number` VARCHAR(50) NOT NULL UNIQUE,
  `supplier_id` INT UNSIGNED NOT NULL,
  `status` ENUM('DRAFT', 'SUBMITTED', 'APPROVED', 'PARTIALLY_RECEIVED', 'RECEIVED', 'CLOSED', 'CANCELLED') DEFAULT 'DRAFT',
  `subtotal` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `tax` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `discount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `grand_total` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `created_by` INT UNSIGNED NULL,
  `approved_by` INT UNSIGNED NULL,
  `notes` TEXT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`supplier_id`) REFERENCES `suppliers`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `purchase_order_items` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `purchase_order_id` INT UNSIGNED NOT NULL,
  `ingredient_id` INT UNSIGNED NOT NULL,
  `ordered_quantity` DECIMAL(12,4) NOT NULL,
  `received_quantity` DECIMAL(12,4) NOT NULL DEFAULT 0.0000,
  `unit` VARCHAR(20) NOT NULL,
  `unit_price` DECIMAL(12,2) NOT NULL,
  `line_total` DECIMAL(12,2) NOT NULL,
  FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Create goods_receipts & goods_receipt_items Tables
CREATE TABLE IF NOT EXISTS `goods_receipts` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `purchase_order_id` INT UNSIGNED NOT NULL,
  `receipt_number` VARCHAR(50) NOT NULL UNIQUE,
  `supplier_id` INT UNSIGNED NOT NULL,
  `received_by` INT UNSIGNED NULL,
  `notes` TEXT NULL,
  `received_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`supplier_id`) REFERENCES `suppliers`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `goods_receipt_items` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `goods_receipt_id` INT UNSIGNED NOT NULL,
  `ingredient_id` INT UNSIGNED NOT NULL,
  `location_id` INT UNSIGNED NOT NULL,
  `received_quantity` DECIMAL(12,4) NOT NULL,
  `unit_price` DECIMAL(12,2) NOT NULL,
  `total_cost` DECIMAL(12,2) NOT NULL,
  FOREIGN KEY (`goods_receipt_id`) REFERENCES `goods_receipts`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients`(`id`) ON DELETE RESTRICT,
  FOREIGN KEY (`location_id`) REFERENCES `inventory_locations`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Enhance recipes & Create recipe_items Tables
ALTER TABLE `recipes`
  ADD COLUMN IF NOT EXISTS `branch_id` INT UNSIGNED NOT NULL DEFAULT 1 AFTER `id`,
  ADD COLUMN IF NOT EXISTS `variant_id` INT UNSIGNED NULL AFTER `product_id`,
  ADD COLUMN IF NOT EXISTS `version` INT UNSIGNED NOT NULL DEFAULT 1 AFTER `variant_id`,
  ADD COLUMN IF NOT EXISTS `name` VARCHAR(150) NULL AFTER `version`,
  ADD COLUMN IF NOT EXISTS `yield_quantity` DECIMAL(12,2) NOT NULL DEFAULT 1.00 AFTER `name`,
  ADD COLUMN IF NOT EXISTS `yield_unit` VARCHAR(20) NOT NULL DEFAULT 'portion' AFTER `yield_quantity`,
  ADD COLUMN IF NOT EXISTS `total_cost` DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER `yield_unit`,
  ADD COLUMN IF NOT EXISTS `is_active` TINYINT(1) DEFAULT 1 AFTER `total_cost`,
  ADD COLUMN IF NOT EXISTS `created_by` INT UNSIGNED NULL AFTER `is_active`,
  ADD COLUMN IF NOT EXISTS `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP AFTER `created_by`,
  ADD COLUMN IF NOT EXISTS `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`;

CREATE TABLE IF NOT EXISTS `recipe_items` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `recipe_id` INT UNSIGNED NOT NULL,
  `ingredient_id` INT UNSIGNED NOT NULL,
  `quantity` DECIMAL(12,4) NOT NULL,
  `unit` VARCHAR(20) NOT NULL,
  `wastage_percentage` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  `unit_cost` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `line_cost` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  FOREIGN KEY (`recipe_id`) REFERENCES `recipes`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Enhance inventory_transactions Table
ALTER TABLE `inventory_transactions`
  ADD COLUMN IF NOT EXISTS `branch_id` INT UNSIGNED NOT NULL DEFAULT 1 AFTER `id`,
  ADD COLUMN IF NOT EXISTS `location_id` INT UNSIGNED NULL AFTER `ingredient_id`,
  MODIFY COLUMN `type` ENUM('PURCHASE_RECEIPT', 'ORDER_CONSUMPTION', 'REVERSAL', 'WASTAGE', 'ADJUSTMENT_IN', 'ADJUSTMENT_OUT', 'TRANSFER_IN', 'TRANSFER_OUT', 'OPENING_BALANCE', 'purchase', 'adjustment', 'recipe_deduction') NOT NULL,
  ADD COLUMN IF NOT EXISTS `unit_cost` DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER `quantity`,
  ADD COLUMN IF NOT EXISTS `total_cost` DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER `unit_cost`,
  ADD COLUMN IF NOT EXISTS `balance_before` DECIMAL(12,4) NOT NULL DEFAULT 0.0000 AFTER `total_cost`,
  ADD COLUMN IF NOT EXISTS `balance_after` DECIMAL(12,4) NOT NULL DEFAULT 0.0000 AFTER `balance_before`,
  ADD COLUMN IF NOT EXISTS `reference_type` VARCHAR(50) NULL AFTER `balance_after`,
  ADD COLUMN IF NOT EXISTS `created_by` INT UNSIGNED NULL AFTER `reference_id`;

-- 10. Create order_item_consumptions Table (Idempotency Tracker)
CREATE TABLE IF NOT EXISTS `order_item_consumptions` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_item_id` INT UNSIGNED NOT NULL,
  `order_ticket_id` INT UNSIGNED NULL,
  `recipe_id` INT UNSIGNED NULL,
  `status` ENUM('CONSUMED', 'REVERSED') DEFAULT 'CONSUMED',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `idx_item_ticket` (`order_item_id`, `order_ticket_id`),
  FOREIGN KEY (`order_item_id`) REFERENCES `order_items`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. Create wastage_records & stock_transfers Tables
CREATE TABLE IF NOT EXISTS `wastage_records` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `branch_id` INT UNSIGNED NOT NULL DEFAULT 1,
  `ingredient_id` INT UNSIGNED NOT NULL,
  `location_id` INT UNSIGNED NOT NULL,
  `quantity` DECIMAL(12,4) NOT NULL,
  `unit` VARCHAR(20) NOT NULL,
  `reason` ENUM('EXPIRED', 'DAMAGED', 'SPOILED', 'BURNED', 'PREPARATION_LOSS', 'OTHER') DEFAULT 'OTHER',
  `estimated_cost` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `recorded_by` INT UNSIGNED NULL,
  `notes` TEXT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients`(`id`) ON DELETE RESTRICT,
  FOREIGN KEY (`location_id`) REFERENCES `inventory_locations`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `stock_transfers` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `branch_id` INT UNSIGNED NOT NULL DEFAULT 1,
  `transfer_number` VARCHAR(50) NOT NULL UNIQUE,
  `source_location_id` INT UNSIGNED NOT NULL,
  `destination_location_id` INT UNSIGNED NOT NULL,
  `status` ENUM('COMPLETED', 'CANCELLED') DEFAULT 'COMPLETED',
  `created_by` INT UNSIGNED NULL,
  `notes` TEXT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`source_location_id`) REFERENCES `inventory_locations`(`id`) ON DELETE RESTRICT,
  FOREIGN KEY (`destination_location_id`) REFERENCES `inventory_locations`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `stock_transfer_items` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `stock_transfer_id` INT UNSIGNED NOT NULL,
  `ingredient_id` INT UNSIGNED NOT NULL,
  `quantity` DECIMAL(12,4) NOT NULL,
  `unit` VARCHAR(20) NOT NULL,
  FOREIGN KEY (`stock_transfer_id`) REFERENCES `stock_transfers`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. Seed Default Locations for Branch 1
INSERT INTO `inventory_locations` (`id`, `branch_id`, `name`, `code`, `location_type`, `status`) VALUES
  (1, 1, 'Main Central Store', 'LOC-MAIN', 'MAIN_STORE', 'ACTIVE'),
  (2, 1, 'Kitchen Operational Store', 'LOC-KITCHEN', 'KITCHEN_STORE', 'ACTIVE')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`), `location_type` = VALUES(`location_type`), `status` = VALUES(`status`);
