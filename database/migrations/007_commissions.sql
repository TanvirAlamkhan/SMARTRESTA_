-- SMARTRESTA Migration 007: Waiter Assignments, Commission Rules & Transactions
USE `smartresta_db`;

CREATE TABLE IF NOT EXISTS `waiter_assignments` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `waiter_id` INT UNSIGNED NOT NULL,
  `table_id` INT UNSIGNED NOT NULL,
  `assigned_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `unassigned_at` TIMESTAMP NULL,
  FOREIGN KEY (`waiter_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`table_id`) REFERENCES `restaurant_tables`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `commission_rules` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(80) NOT NULL,
  `calculation_base` ENUM('PERCENTAGE', 'PER_ORDER', 'PER_ITEM', 'SALES_AMOUNT') DEFAULT 'PERCENTAGE',
  `rate` DECIMAL(8,2) NOT NULL DEFAULT 5.00,
  `is_active` TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `commission_transactions` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT UNSIGNED NOT NULL,
  `waiter_id` INT UNSIGNED NOT NULL,
  `commission_rule_id` INT UNSIGNED NULL,
  `base_amount` DECIMAL(12,2) NOT NULL,
  `rate` DECIMAL(8,2) NOT NULL,
  `commission_amount` DECIMAL(12,2) NOT NULL,
  `status` ENUM('PENDING', 'APPROVED', 'PAID', 'REJECTED') DEFAULT 'PENDING',
  `approved_by_user_id` INT UNSIGNED NULL,
  `approved_at` TIMESTAMP NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_comm_waiter_id` (`waiter_id`),
  INDEX `idx_comm_order_id` (`order_id`),
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE RESTRICT,
  FOREIGN KEY (`waiter_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
