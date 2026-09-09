-- SMARTRESTA Migration 014: Menus, Categories, Products, Variants & Modifiers Schema Extensions
USE `smartresta_db`;

-- 1. Create Menus Table
CREATE TABLE IF NOT EXISTS `menus` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `branch_id` INT UNSIGNED NOT NULL DEFAULT 1,
  `name` VARCHAR(100) NOT NULL,
  `description` VARCHAR(255) NULL,
  `status` ENUM('ACTIVE', 'INACTIVE') DEFAULT 'ACTIVE',
  `display_order` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL,
  FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Add menu_id to Categories if missing
ALTER TABLE `categories` 
  ADD COLUMN IF NOT EXISTS `menu_id` INT UNSIGNED NULL AFTER `id`,
  ADD COLUMN IF NOT EXISTS `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `status`;

-- 3. Enhance Products Table
ALTER TABLE `products`
  ADD COLUMN IF NOT EXISTS `slug` VARCHAR(120) NULL AFTER `name`,
  ADD COLUMN IF NOT EXISTS `short_description` VARCHAR(255) NULL AFTER `description`,
  ADD COLUMN IF NOT EXISTS `status` ENUM('ACTIVE', 'INACTIVE') DEFAULT 'ACTIVE' AFTER `cost_price`,
  ADD COLUMN IF NOT EXISTS `display_order` INT DEFAULT 0 AFTER `is_available`,
  ADD COLUMN IF NOT EXISTS `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`;

-- 4. Create Modifier Groups Table
CREATE TABLE IF NOT EXISTS `modifier_groups` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(80) NOT NULL,
  `description` VARCHAR(255) NULL,
  `min_selection` INT DEFAULT 0,
  `max_selection` INT DEFAULT 1,
  `is_required` TINYINT(1) DEFAULT 0,
  `status` ENUM('ACTIVE', 'INACTIVE') DEFAULT 'ACTIVE',
  `display_order` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Enhance Modifiers Table
ALTER TABLE `modifiers`
  ADD COLUMN IF NOT EXISTS `modifier_group_id` INT UNSIGNED NULL AFTER `id`,
  ADD COLUMN IF NOT EXISTS `description` VARCHAR(255) NULL AFTER `name`,
  ADD COLUMN IF NOT EXISTS `display_order` INT DEFAULT 0 AFTER `status`,
  ADD COLUMN IF NOT EXISTS `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

-- 6. Enhance Product Variants Table
ALTER TABLE `product_variants`
  ADD COLUMN IF NOT EXISTS `sku` VARCHAR(50) NULL AFTER `variant_name`,
  ADD COLUMN IF NOT EXISTS `price` DECIMAL(12,2) NULL AFTER `sku`,
  ADD COLUMN IF NOT EXISTS `is_default` TINYINT(1) DEFAULT 0 AFTER `price_adjustment`,
  ADD COLUMN IF NOT EXISTS `status` ENUM('ACTIVE', 'INACTIVE') DEFAULT 'ACTIVE' AFTER `is_default`,
  ADD COLUMN IF NOT EXISTS `display_order` INT DEFAULT 0 AFTER `status`;

-- 7. Add Essential Indexes
CREATE INDEX IF NOT EXISTS `idx_products_cat_status` ON `products` (`category_id`, `status`, `is_available`);
CREATE INDEX IF NOT EXISTS `idx_products_sku` ON `products` (`sku`);
