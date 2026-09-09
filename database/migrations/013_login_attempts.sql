-- SMARTRESTA Migration 013: Failed Login Attempts Rate Limiting Table
USE `smartresta_db`;

CREATE TABLE IF NOT EXISTS `login_attempts` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `identifier` VARCHAR(120) NOT NULL,
  `ip_address` VARCHAR(45) NOT NULL,
  `attempted_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_rate_limit` (`ip_address`, `identifier`, `attempted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
