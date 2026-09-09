-- SMARTRESTA Migration 003: Menu Categories, Products, Variants, Modifiers
USE `smartresta_db`;

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
