-- SMARTRESTA Migration 009: Ingredients, Recipes, Inventory & Purchases
USE `smartresta_db`;

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
