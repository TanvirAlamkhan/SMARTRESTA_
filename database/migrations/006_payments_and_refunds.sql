-- SMARTRESTA Migration 006: Payment Methods, Payments, and Refunds
USE `smartresta_db`;

CREATE TABLE IF NOT EXISTS `payment_methods` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(50) NOT NULL UNIQUE,
  `code` VARCHAR(30) NOT NULL UNIQUE,
  `is_active` TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `payments` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT UNSIGNED NOT NULL,
  `payment_method_id` INT UNSIGNED NOT NULL,
  `amount` DECIMAL(12,2) NOT NULL,
  `transaction_reference` VARCHAR(100) NULL,
  `received_by_user_id` INT UNSIGNED NOT NULL,
  `status` ENUM('PENDING', 'COMPLETED', 'FAILED', 'REFUNDED') DEFAULT 'COMPLETED',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_payments_order_id` (`order_id`),
  INDEX `idx_payments_created_at` (`created_at`),
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE RESTRICT,
  FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods`(`id`) ON DELETE RESTRICT,
  FOREIGN KEY (`received_by_user_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `refunds` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `payment_id` INT UNSIGNED NOT NULL,
  `order_id` INT UNSIGNED NOT NULL,
  `refund_amount` DECIMAL(12,2) NOT NULL,
  `reason` TEXT NOT NULL,
  `processed_by_user_id` INT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`payment_id`) REFERENCES `payments`(`id`) ON DELETE RESTRICT,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
