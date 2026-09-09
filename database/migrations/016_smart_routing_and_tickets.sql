-- SMARTRESTA Migration 016: Smart Order Routing Engine & Station Tickets Schema Extensions
USE `smartresta_db`;

-- 1. Enhance stations Table
ALTER TABLE `stations`
  ADD COLUMN IF NOT EXISTS `type` ENUM('KITCHEN', 'BAR', 'COFFEE', 'GRILL', 'DESSERT', 'PACKAGING') DEFAULT 'KITCHEN' AFTER `name`,
  ADD COLUMN IF NOT EXISTS `display_device` VARCHAR(100) NULL AFTER `badge_code`,
  ADD COLUMN IF NOT EXISTS `printer` VARCHAR(100) NULL AFTER `display_device`,
  ADD COLUMN IF NOT EXISTS `display_order` INT DEFAULT 0 AFTER `printer`,
  ADD COLUMN IF NOT EXISTS `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER `display_order`,
  ADD COLUMN IF NOT EXISTS `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`;

-- 2. Enhance product_variants Table for Variant-Level Station Override
ALTER TABLE `product_variants`
  ADD COLUMN IF NOT EXISTS `station_id` INT UNSIGNED NULL AFTER `product_id`;

-- 3. Enhance order_items Table for Station Resolution & Status Tracking
ALTER TABLE `order_items`
  ADD COLUMN IF NOT EXISTS `station_id` INT UNSIGNED NULL AFTER `product_id`,
  ADD COLUMN IF NOT EXISTS `routing_status` ENUM('UNROUTED', 'ROUTED', 'IN_PROGRESS', 'READY', 'SERVED', 'CANCELLED') DEFAULT 'UNROUTED' AFTER `notes`,
  ADD COLUMN IF NOT EXISTS `routed_at` DATETIME NULL AFTER `routing_status`;

-- 4. Enhance order_routes Table
ALTER TABLE `order_routes`
  ADD COLUMN IF NOT EXISTS `route_status` ENUM('PENDING', 'SENT', 'ACKNOWLEDGED', 'IN_PROGRESS', 'READY', 'COMPLETED', 'CANCELLED', 'FAILED') DEFAULT 'SENT' AFTER `station_id`,
  ADD COLUMN IF NOT EXISTS `routed_by` INT UNSIGNED NULL AFTER `route_status`,
  ADD COLUMN IF NOT EXISTS `routed_at` DATETIME DEFAULT CURRENT_TIMESTAMP AFTER `routed_by`,
  ADD COLUMN IF NOT EXISTS `received_at` DATETIME NULL AFTER `routed_at`,
  ADD COLUMN IF NOT EXISTS `completed_at` DATETIME NULL AFTER `received_at`,
  ADD COLUMN IF NOT EXISTS `notes` TEXT NULL AFTER `completed_at`;

-- Add Unique constraint on order_routes if index doesn't exist
SET @dbname = DATABASE();
SET @tablename = 'order_routes';
SET @columnname = 'idx_order_station';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND INDEX_NAME = @columnname
  ) > 0,
  'SELECT 1',
  'CREATE UNIQUE INDEX idx_order_station ON order_routes(order_id, station_id);'
));
PREPARE createIndexTransaction FROM @preparedStatement;
EXECUTE createIndexTransaction;
DEALLOCATE PREPARE createIndexTransaction;

-- 5. Enhance order_tickets Table
ALTER TABLE `order_tickets`
  ADD COLUMN IF NOT EXISTS `order_route_id` INT UNSIGNED NULL AFTER `order_id`,
  ADD COLUMN IF NOT EXISTS `priority` ENUM('NORMAL', 'HIGH', 'URGENT') DEFAULT 'NORMAL' AFTER `status`,
  ADD COLUMN IF NOT EXISTS `created_by` INT UNSIGNED NULL AFTER `priority`,
  ADD COLUMN IF NOT EXISTS `started_at` DATETIME NULL AFTER `created_at`,
  ADD COLUMN IF NOT EXISTS `ready_at` DATETIME NULL AFTER `started_at`,
  ADD COLUMN IF NOT EXISTS `completed_at` DATETIME NULL AFTER `ready_at`;

SET @tablename = 'order_tickets';
SET @columnname = 'idx_ticket_number';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND INDEX_NAME = @columnname
  ) > 0,
  'SELECT 1',
  'CREATE UNIQUE INDEX idx_ticket_number ON order_tickets(ticket_number);'
));
PREPARE createIndexTransaction FROM @preparedStatement;
EXECUTE createIndexTransaction;
DEALLOCATE PREPARE createIndexTransaction;

-- 6. Create order_ticket_items Table
CREATE TABLE IF NOT EXISTS `order_ticket_items` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_ticket_id` INT UNSIGNED NOT NULL,
  `order_item_id` INT UNSIGNED NOT NULL,
  `product_name` VARCHAR(150) NOT NULL,
  `variant_name` VARCHAR(80) NULL,
  `quantity` INT UNSIGNED NOT NULL DEFAULT 1,
  `modifiers_snapshot` TEXT NULL,
  `special_instructions` TEXT NULL,
  `item_status` ENUM('NEW', 'PREPARING', 'READY', 'SERVED', 'CANCELLED') DEFAULT 'NEW',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`order_ticket_id`) REFERENCES `order_tickets`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`order_item_id`) REFERENCES `order_items`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Ensure Default Stations for Branch 1
INSERT INTO `stations` (`id`, `branch_id`, `name`, `badge_code`, `type`, `status`) VALUES
  (1, 1, 'Main Kitchen', 'ST-KITCHEN', 'KITCHEN', 'ACTIVE'),
  (2, 1, 'Beverage Bar', 'ST-BAR', 'BAR', 'ACTIVE'),
  (3, 1, 'Coffee Corner', 'ST-COFFEE', 'COFFEE', 'ACTIVE'),
  (4, 1, 'Dessert Station', 'ST-DESSERT', 'DESSERT', 'ACTIVE')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`), `type` = VALUES(`type`), `status` = VALUES(`status`);
