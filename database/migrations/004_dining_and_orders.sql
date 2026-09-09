-- SMARTRESTA Migration 004: Dining Sessions, Orders, Order Items, Status History
USE `smartresta_db`;

CREATE TABLE IF NOT EXISTS `dining_sessions` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `table_id` INT UNSIGNED NOT NULL,
  `customer_id` INT UNSIGNED NULL,
  `opened_by_user_id` INT UNSIGNED NOT NULL,
  `guest_count` INT DEFAULT 1,
  `status` ENUM('OPEN', 'WAITING_PAYMENT', 'CLOSED', 'CANCELLED') DEFAULT 'OPEN',
  `opened_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `closed_at` TIMESTAMP NULL,
  `notes` VARCHAR(255) NULL,
  FOREIGN KEY (`table_id`) REFERENCES `restaurant_tables`(`id`) ON DELETE RESTRICT,
  FOREIGN KEY (`opened_by_user_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `orders` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_number` VARCHAR(40) NOT NULL UNIQUE,
  `dining_session_id` INT UNSIGNED NULL,
  `table_id` INT UNSIGNED NULL,
  `customer_id` INT UNSIGNED NULL,
  `taken_by_user_id` INT UNSIGNED NOT NULL,
  `served_by_user_id` INT UNSIGNED NULL,
  `order_type` ENUM('DINE_IN', 'TAKEAWAY', 'DELIVERY') DEFAULT 'DINE_IN',
  `subtotal` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `discount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `tax` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `service_charge` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `total` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `payment_status` ENUM('UNPAID', 'PARTIAL', 'PAID', 'REFUNDED') DEFAULT 'UNPAID',
  `order_status` ENUM('DRAFT', 'SUBMITTED', 'WAITING_PAYMENT', 'CONFIRMED', 'ROUTED', 'PREPARING', 'READY', 'SERVED', 'COMPLETED', 'CANCELLED', 'REFUNDED') DEFAULT 'SUBMITTED',
  `notes` VARCHAR(255) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `completed_at` TIMESTAMP NULL,
  `cancelled_at` TIMESTAMP NULL,
  INDEX `idx_order_number` (`order_number`),
  INDEX `idx_order_created_at` (`created_at`),
  INDEX `idx_order_status` (`order_status`),
  FOREIGN KEY (`dining_session_id`) REFERENCES `dining_sessions`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`table_id`) REFERENCES `restaurant_tables`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`taken_by_user_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `order_items` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NULL,
  `counter_id` INT UNSIGNED NOT NULL,
  `item_name` VARCHAR(120) NOT NULL,
  `quantity` INT NOT NULL DEFAULT 1,
  `unit_price` DECIMAL(12,2) NOT NULL,
  `subtotal` DECIMAL(12,2) NOT NULL,
  `status` ENUM('PENDING', 'ROUTED', 'PREPARING', 'READY', 'SERVED', 'CANCELLED') DEFAULT 'ROUTED',
  `notes` VARCHAR(255) NULL,
  INDEX `idx_order_items_order_id` (`order_id`),
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`counter_id`) REFERENCES `stations`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `order_status_history` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT UNSIGNED NOT NULL,
  `previous_status` VARCHAR(50) NULL,
  `new_status` VARCHAR(50) NOT NULL,
  `changed_by_user_id` INT UNSIGNED NOT NULL,
  `notes` VARCHAR(255) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
