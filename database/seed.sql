-- ====================================================================
-- SMARTRESTA — Structural Configuration & Reference Seed Data ONLY
-- Super Prompt 02 Compliance: ZERO Fake Customers, ZERO Fake Orders, ZERO Fake Sales
-- ====================================================================

USE `smartresta_db`;

-- 1. System Roles
INSERT INTO `roles` (`id`, `name`, `description`) VALUES
(1, 'admin', 'System Administrator with full management & configuration access'),
(2, 'manager', 'Restaurant Operational Manager'),
(3, 'reception', 'Cashier & Front Desk Receptionist'),
(4, 'waiter', 'Service Staff & Order Taker'),
(5, 'kitchen', 'Kitchen Display System & Station Operator')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- 2. System Permissions
INSERT INTO `permissions` (`id`, `name`, `module`, `description`) VALUES
(1, 'manage_settings', 'admin', 'Configure restaurant branch and global parameters'),
(2, 'view_reports', 'reports', 'View financial and sales metrics'),
(3, 'create_order', 'pos', 'Create orders and route items'),
(4, 'process_payment', 'reception', 'Process billing payments and refunds'),
(5, 'update_kds', 'kitchen', 'Update ticket status on Kitchen Display System')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- 3. Default Operational Stations
INSERT INTO `branches` (`id`, `name`, `code`, `address`, `phone`) VALUES
(1, 'Main Outlet', 'MAIN-01', 'Dhaka Main Outlet', '01700000000')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

INSERT INTO `floors` (`id`, `branch_id`, `name`, `sort_order`) VALUES
(1, 1, 'Main Dining Hall', 1),
(2, 1, 'Terrace Lounge', 2)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

INSERT INTO `stations` (`id`, `branch_id`, `name`, `badge_code`, `status`) VALUES
(1, 1, 'Kitchen Station', 'KITCHEN', 'ACTIVE'),
(2, 1, 'Bar & Drinks', 'BAR', 'ACTIVE'),
(3, 1, 'Coffee Corner', 'COFFEE', 'ACTIVE'),
(4, 1, 'Grill Counter', 'GRILL', 'ACTIVE'),
(5, 1, 'Dessert Station', 'DESSERT', 'ACTIVE'),
(6, 1, 'Packaging Area', 'PACKAGING', 'ACTIVE')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- 4. Payment Methods Configuration
INSERT INTO `payment_methods` (`id`, `name`, `code`, `is_active`) VALUES
(1, 'Cash', 'CASH', 1),
(2, 'bKash Mobile Money', 'BKASH', 1),
(3, 'Nagad Mobile Money', 'NAGAD', 1),
(4, 'Rocket Mobile Money', 'ROCKET', 1),
(5, 'Debit / Credit Card', 'CARD', 1)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- 5. Default Commission Rules
INSERT INTO `commission_rules` (`id`, `name`, `calculation_base`, `rate`, `is_active`) VALUES
(1, 'Standard Waiter Sales Commission', 'PERCENTAGE', 5.00, 1)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- 6. System Settings
INSERT INTO `settings` (`setting_key`, `setting_value`, `description`) VALUES
('currency_symbol', '৳', 'Primary Currency Symbol (Taka)'),
('vat_rate', '5.00', 'Standard VAT Percentage'),
('allow_partial_payment', '1', 'Enable Partial Payments'),
('default_routing_mode', 'AUTOMATIC', 'Automatic Station Routing Mode')
ON DUPLICATE KEY UPDATE `setting_value` = VALUES(`setting_value`);
