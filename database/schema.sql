-- ====================================================================
-- SMARTRESTA — Production Enterprise Relational Database Schema
-- Technology: MySQL 8.x / MariaDB
-- Character Set: utf8mb4 / Collation: utf8mb4_unicode_ci
-- ====================================================================

CREATE DATABASE IF NOT EXISTS `smartresta_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `smartresta_db`;

-- --------------------------------------------------------------------
-- 1. USERS, ROLES & ACCESS CONTROL (RBAC)
-- --------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `roles` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(50) NOT NULL UNIQUE,
  `description` VARCHAR(255) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `permissions` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(80) NOT NULL UNIQUE,
  `module` VARCHAR(50) NOT NULL,
  `description` VARCHAR(255) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `role_permissions` (
  `role_id` INT UNSIGNED NOT NULL,
  `permission_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`role_id`, `permission_id`),
  FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`permission_id`) REFERENCES `permissions`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `role_id` INT UNSIGNED NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(120) NOT NULL UNIQUE,
  `phone` VARCHAR(30) NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `role` ENUM('admin', 'manager', 'reception', 'waiter', 'kitchen') NOT NULL DEFAULT 'waiter',
  `status` ENUM('ACTIVE', 'INACTIVE', 'ON_BREAK') DEFAULT 'ACTIVE',
  `last_login` TIMESTAMP NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL,
  FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------------------
-- 2. RESTAURANT STRUCTURE & TABLES
-- --------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `branches` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `code` VARCHAR(30) NOT NULL UNIQUE,
  `address` TEXT NULL,
  `phone` VARCHAR(30) NULL,
  `status` ENUM('ACTIVE', 'INACTIVE') DEFAULT 'ACTIVE',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `floors` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `branch_id` INT UNSIGNED NOT NULL,
  `name` VARCHAR(80) NOT NULL,
  `sort_order` INT DEFAULT 0,
  FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `stations` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `branch_id` INT UNSIGNED NOT NULL,
  `name` VARCHAR(50) NOT NULL,
  `badge_code` VARCHAR(20) NOT NULL UNIQUE,
  `status` ENUM('ACTIVE', 'INACTIVE') DEFAULT 'ACTIVE',
  FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `restaurant_tables` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `floor_id` INT UNSIGNED NOT NULL,
  `table_number` VARCHAR(30) NOT NULL,
  `capacity` INT NOT NULL DEFAULT 4,
  `status` ENUM('AVAILABLE', 'OCCUPIED', 'RESERVED', 'WAITING_PAYMENT', 'CLEANING', 'OUT_OF_SERVICE') DEFAULT 'AVAILABLE',
  `is_active` TINYINT(1) DEFAULT 1,
  `deleted_at` TIMESTAMP NULL,
  UNIQUE KEY `idx_floor_table` (`floor_id`, `table_number`),
  FOREIGN KEY (`floor_id`) REFERENCES `floors`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------------------
-- 3. DINING SESSIONS
-- --------------------------------------------------------------------

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

-- --------------------------------------------------------------------
-- 4. MENU CATALOG & MODIFIERS
-- --------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(80) NOT NULL,
  `slug` VARCHAR(80) NOT NULL UNIQUE,
  `description` VARCHAR(255) NULL,
  `sort_order` INT DEFAULT 0,
  `status` ENUM('ACTIVE', 'INACTIVE') DEFAULT 'ACTIVE',
  `deleted_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `products` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT UNSIGNED NOT NULL,
  `default_station_id` INT UNSIGNED NOT NULL,
  `name` VARCHAR(120) NOT NULL,
  `sku` VARCHAR(50) NULL UNIQUE,
  `description` TEXT NULL,
  `price` DECIMAL(12,2) NOT NULL,
  `cost_price` DECIMAL(12,2) DEFAULT 0.00,
  `is_available` TINYINT(1) DEFAULT 1,
  `image_url` VARCHAR(255) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE RESTRICT,
  FOREIGN KEY (`default_station_id`) REFERENCES `stations`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `product_variants` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT UNSIGNED NOT NULL,
  `variant_name` VARCHAR(80) NOT NULL,
  `price_adjustment` DECIMAL(12,2) DEFAULT 0.00,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `modifiers` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(80) NOT NULL,
  `price` DECIMAL(12,2) DEFAULT 0.00,
  `status` ENUM('ACTIVE', 'INACTIVE') DEFAULT 'ACTIVE'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `product_modifiers` (
  `product_id` INT UNSIGNED NOT NULL,
  `modifier_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`product_id`, `modifier_id`),
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`modifier_id`) REFERENCES `modifiers`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------------------
-- 5. ORDERS & SMART ROUTING
-- --------------------------------------------------------------------

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

CREATE TABLE IF NOT EXISTS `order_routes` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT UNSIGNED NOT NULL,
  `station_id` INT UNSIGNED NOT NULL,
  `routing_mode` ENUM('AUTOMATIC', 'MANUAL', 'HYBRID') DEFAULT 'AUTOMATIC',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`station_id`) REFERENCES `stations`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `order_tickets` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT UNSIGNED NOT NULL,
  `station_id` INT UNSIGNED NOT NULL,
  `ticket_number` VARCHAR(30) NOT NULL,
  `status` ENUM('NEW', 'PREPARING', 'READY', 'SERVED') DEFAULT 'NEW',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`station_id`) REFERENCES `stations`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------------------
-- 6. PAYMENTS & REFUNDS
-- --------------------------------------------------------------------

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

-- --------------------------------------------------------------------
-- 7. WAITERS & COMMISSIONS
-- --------------------------------------------------------------------

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
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE RESTRICT,
  FOREIGN KEY (`waiter_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------------------
-- 8. CUSTOMERS & RESERVATIONS
-- --------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `customers` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(30) NOT NULL UNIQUE,
  `email` VARCHAR(120) NULL,
  `status` ENUM('ACTIVE', 'INACTIVE') DEFAULT 'ACTIVE',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `reservations` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `customer_id` INT UNSIGNED NOT NULL,
  `table_id` INT UNSIGNED NOT NULL,
  `guest_count` INT NOT NULL DEFAULT 2,
  `reservation_time` DATETIME NOT NULL,
  `status` ENUM('CONFIRMED', 'CANCELLED', 'SEATED', 'NO_SHOW') DEFAULT 'CONFIRMED',
  `notes` VARCHAR(255) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE RESTRICT,
  FOREIGN KEY (`table_id`) REFERENCES `restaurant_tables`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------------------
-- 9. INVENTORY & RECIPES
-- --------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `ingredients` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL UNIQUE,
  `unit` VARCHAR(20) NOT NULL,
  `current_stock` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `min_stock` DECIMAL(12,2) NOT NULL DEFAULT 10.00,
  `cost_per_unit` DECIMAL(12,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `recipes` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT UNSIGNED NOT NULL,
  `ingredient_id` INT UNSIGNED NOT NULL,
  `quantity_required` DECIMAL(12,2) NOT NULL,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `inventory_transactions` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `ingredient_id` INT UNSIGNED NOT NULL,
  `type` ENUM('purchase', 'wastage', 'adjustment', 'recipe_deduction') NOT NULL,
  `quantity` DECIMAL(12,2) NOT NULL,
  `reference_id` INT UNSIGNED NULL,
  `notes` VARCHAR(255) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------------------
-- 10. FINANCE & SHIFTS
-- --------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `expense_categories` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(80) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `expenses` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT UNSIGNED NOT NULL,
  `amount` DECIMAL(12,2) NOT NULL,
  `title` VARCHAR(150) NOT NULL,
  `recorded_by_user_id` INT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `expense_categories`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `shifts` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL,
  `opened_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `closed_at` TIMESTAMP NULL,
  `opening_balance` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `closing_balance` DECIMAL(12,2) NULL,
  `status` ENUM('OPEN', 'CLOSED') DEFAULT 'OPEN',
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------------------
-- 11. AUDIT LOGS, NOTIFICATIONS & SETTINGS
-- --------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NULL,
  `action` VARCHAR(80) NOT NULL,
  `module` VARCHAR(50) NOT NULL,
  `record_id` INT UNSIGNED NULL,
  `old_value` JSON NULL,
  `new_value` JSON NULL,
  `ip_address` VARCHAR(45) NULL,
  `user_agent` VARCHAR(255) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_action_module` (`module`, `action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `settings` (
  `setting_key` VARCHAR(50) PRIMARY KEY,
  `setting_value` TEXT NOT NULL,
  `description` VARCHAR(255) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
