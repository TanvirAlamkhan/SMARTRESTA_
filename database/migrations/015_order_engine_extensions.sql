-- SMARTRESTA Migration 015: Order Engine Extensions, Item Variant & Modifier Snapshots
USE `smartresta_db`;

-- 1. Enhance Orders Table
ALTER TABLE `orders`
  ADD COLUMN IF NOT EXISTS `branch_id` INT UNSIGNED NOT NULL DEFAULT 1 AFTER `order_number`,
  ADD COLUMN IF NOT EXISTS `delivery_charge` DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER `service_charge`,
  ADD COLUMN IF NOT EXISTS `priority` ENUM('NORMAL', 'HIGH', 'URGENT') DEFAULT 'NORMAL' AFTER `order_status`;

-- 2. Enhance Order Items Table
ALTER TABLE `order_items`
  ADD COLUMN IF NOT EXISTS `variant_id` INT UNSIGNED NULL AFTER `product_id`,
  ADD COLUMN IF NOT EXISTS `variant_name` VARCHAR(80) NULL AFTER `item_name`,
  ADD COLUMN IF NOT EXISTS `sku` VARCHAR(50) NULL AFTER `variant_name`,
  ADD COLUMN IF NOT EXISTS `modifier_total` DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER `unit_price`,
  ADD COLUMN IF NOT EXISTS `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `notes`;

-- 3. Create Order Item Modifiers Snapshot Table
CREATE TABLE IF NOT EXISTS `order_item_modifiers` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_item_id` INT UNSIGNED NOT NULL,
  `modifier_id` INT UNSIGNED NULL,
  `modifier_name` VARCHAR(80) NOT NULL,
  `unit_price` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `quantity` INT NOT NULL DEFAULT 1,
  `subtotal` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`order_item_id`) REFERENCES `order_items`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Essential Indexes for High Throughput Querying
CREATE INDEX IF NOT EXISTS `idx_orders_branch_status_date` ON `orders` (`branch_id`, `order_status`, `created_at`);
CREATE INDEX IF NOT EXISTS `idx_orders_session` ON `orders` (`dining_session_id`);
CREATE INDEX IF NOT EXISTS `idx_order_items_order_prod` ON `order_items` (`order_id`, `product_id`);
