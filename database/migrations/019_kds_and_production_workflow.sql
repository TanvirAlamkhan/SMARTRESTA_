-- SMARTRESTA Migration 019: KDS, Counter Operations & Multi-Station Production Workflow Schema Extensions
USE `smartresta_db`;

-- 1. Enhance stations Table for Operational States & Pause Management
ALTER TABLE `stations`
  MODIFY COLUMN `status` ENUM('ACTIVE', 'INACTIVE', 'PAUSED', 'OUT_OF_SERVICE') DEFAULT 'ACTIVE',
  ADD COLUMN IF NOT EXISTS `is_paused` TINYINT(1) DEFAULT 0 AFTER `status`,
  ADD COLUMN IF NOT EXISTS `pause_reason` VARCHAR(255) NULL AFTER `is_paused`,
  ADD COLUMN IF NOT EXISTS `paused_at` DATETIME NULL AFTER `pause_reason`;

-- 2. Enhance order_tickets Table for Re-Fire & Recall Tracking
ALTER TABLE `order_tickets`
  MODIFY COLUMN `ticket_number` VARCHAR(50) NOT NULL,
  MODIFY COLUMN `status` ENUM('NEW', 'PREPARING', 'READY', 'SERVED', 'CANCELLED') DEFAULT 'NEW',
  ADD COLUMN IF NOT EXISTS `refire_count` INT UNSIGNED DEFAULT 0 AFTER `completed_at`,
  ADD COLUMN IF NOT EXISTS `original_ticket_id` INT UNSIGNED NULL AFTER `refire_count`,
  ADD COLUMN IF NOT EXISTS `recalled_at` DATETIME NULL AFTER `original_ticket_id`,
  ADD COLUMN IF NOT EXISTS `recalled_by` INT UNSIGNED NULL AFTER `recalled_at`;

-- Add foreign key constraint for original_ticket_id if index doesn't exist
SET @dbname = DATABASE();
SET @tablename = 'order_tickets';
SET @columnname = 'idx_ticket_original';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND INDEX_NAME = @columnname
  ) > 0,
  'SELECT 1',
  'CREATE INDEX idx_ticket_original ON order_tickets(original_ticket_id);'
));
PREPARE createIndexTransaction FROM @preparedStatement;
EXECUTE createIndexTransaction;
DEALLOCATE PREPARE createIndexTransaction;

-- 3. Create order_ticket_status_history Table
CREATE TABLE IF NOT EXISTS `order_ticket_status_history` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_ticket_id` INT UNSIGNED NOT NULL,
  `from_status` VARCHAR(30) NULL,
  `to_status` VARCHAR(30) NOT NULL,
  `changed_by_user_id` INT UNSIGNED NULL,
  `reason` VARCHAR(255) NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`order_ticket_id`) REFERENCES `order_tickets`(`id`) ON DELETE CASCADE,
  INDEX `idx_ticket_history` (`order_ticket_id`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Add Operational Queue Indexes
SET @tablename = 'order_tickets';
SET @columnname = 'idx_queue_lookup';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND INDEX_NAME = @columnname
  ) > 0,
  'SELECT 1',
  'CREATE INDEX idx_queue_lookup ON order_tickets(station_id, status, priority, created_at);'
));
PREPARE createIndexTransaction FROM @preparedStatement;
EXECUTE createIndexTransaction;
DEALLOCATE PREPARE createIndexTransaction;
