-- SMARTRESTA Production Database Backup
-- Export Date: 2026-09-09 01:03:44

SET FOREIGN_KEY_CHECKS = 0;

-- Table structure for `audit_logs`
DROP TABLE IF EXISTS `audit_logs`;
CREATE TABLE `audit_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `action` varchar(80) NOT NULL,
  `module` varchar(50) NOT NULL,
  `record_id` int(10) unsigned DEFAULT NULL,
  `old_value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `new_value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_action_module` (`module`,`action`),
  KEY `idx_audit_user_action` (`user_id`,`action`,`created_at`),
  KEY `idx_audit_created` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=315 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for `audit_logs`
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('1', '1', 'ROUTE_CREATED', 'orders', '4', NULL, '\"Order #ORD-B1-20260909-0003 routed to 3 station(s).\"', '127.0.0.1', 'Unknown', '2026-09-09 05:41:01');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('2', '1', 'ORDER_CREATED', 'order', '5', NULL, '{\"order_number\":\"ORD-B1-20260909-0004\",\"order_type\":\"DINE_IN\"}', '127.0.0.1', 'Unknown', '2026-09-09 05:41:14');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('3', '1', 'ORDER_ITEM_ADDED', 'order_item', '7', NULL, '{\"order_id\":5,\"item\":\"Chef Special Burger\",\"quantity\":1,\"subtotal\":12}', '127.0.0.1', 'Unknown', '2026-09-09 05:41:14');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('4', '1', 'ORDER_ITEM_ADDED', 'order_item', '8', NULL, '{\"order_id\":5,\"item\":\"Iced Americano\",\"quantity\":1,\"subtotal\":4.5}', '127.0.0.1', 'Unknown', '2026-09-09 05:41:14');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('5', '1', 'ORDER_ITEM_ADDED', 'order_item', '9', NULL, '{\"order_id\":5,\"item\":\"Molten Chocolate Cake\",\"quantity\":1,\"subtotal\":7.5}', '127.0.0.1', 'Unknown', '2026-09-09 05:41:14');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('6', '1', 'ORDER_SUBMITTED', 'order', '5', NULL, '{\"order_number\":\"ORD-B1-20260909-0004\",\"total\":\"25.20\"}', '127.0.0.1', 'Unknown', '2026-09-09 05:41:14');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('7', '1', 'ROUTE_CREATED', 'orders', '5', NULL, '\"Order #ORD-B1-20260909-0004 routed to 3 station(s).\"', '127.0.0.1', 'Unknown', '2026-09-09 05:41:14');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('8', '1', 'ORDER_CREATED', 'order', '6', NULL, '{\"order_number\":\"ORD-B1-20260909-0005\",\"order_type\":\"DINE_IN\"}', '127.0.0.1', 'Unknown', '2026-09-09 05:41:27');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('9', '1', 'ORDER_ITEM_ADDED', 'order_item', '10', NULL, '{\"order_id\":6,\"item\":\"Chef Special Burger\",\"quantity\":1,\"subtotal\":12}', '127.0.0.1', 'Unknown', '2026-09-09 05:41:27');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('10', '1', 'ORDER_ITEM_ADDED', 'order_item', '11', NULL, '{\"order_id\":6,\"item\":\"Iced Americano\",\"quantity\":1,\"subtotal\":4.5}', '127.0.0.1', 'Unknown', '2026-09-09 05:41:27');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('11', '1', 'ORDER_ITEM_ADDED', 'order_item', '12', NULL, '{\"order_id\":6,\"item\":\"Molten Chocolate Cake\",\"quantity\":1,\"subtotal\":7.5}', '127.0.0.1', 'Unknown', '2026-09-09 05:41:27');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('12', '1', 'ORDER_SUBMITTED', 'order', '6', NULL, '{\"order_number\":\"ORD-B1-20260909-0005\",\"total\":\"25.20\"}', '127.0.0.1', 'Unknown', '2026-09-09 05:41:27');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('13', '1', 'ROUTE_CREATED', 'orders', '6', NULL, '\"Order #ORD-B1-20260909-0005 routed to 3 station(s).\"', '127.0.0.1', 'Unknown', '2026-09-09 05:41:27');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('14', '1', 'ORDER_CREATED', 'order', '7', NULL, '{\"order_number\":\"ORD-B1-20260909-0006\",\"order_type\":\"DINE_IN\"}', '127.0.0.1', 'Unknown', '2026-09-09 05:41:47');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('15', '1', 'ORDER_ITEM_ADDED', 'order_item', '13', NULL, '{\"order_id\":7,\"item\":\"Chef Special Burger\",\"quantity\":1,\"subtotal\":12}', '127.0.0.1', 'Unknown', '2026-09-09 05:41:47');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('16', '1', 'ORDER_ITEM_ADDED', 'order_item', '14', NULL, '{\"order_id\":7,\"item\":\"Iced Americano\",\"quantity\":1,\"subtotal\":4.5}', '127.0.0.1', 'Unknown', '2026-09-09 05:41:47');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('17', '1', 'ORDER_ITEM_ADDED', 'order_item', '15', NULL, '{\"order_id\":7,\"item\":\"Molten Chocolate Cake\",\"quantity\":1,\"subtotal\":7.5}', '127.0.0.1', 'Unknown', '2026-09-09 05:41:47');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('18', '1', 'ORDER_SUBMITTED', 'order', '7', NULL, '{\"order_number\":\"ORD-B1-20260909-0006\",\"total\":\"25.20\"}', '127.0.0.1', 'Unknown', '2026-09-09 05:41:47');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('19', '1', 'ROUTE_CREATED', 'orders', '7', NULL, '\"Order #ORD-B1-20260909-0006 routed to 3 station(s).\"', '127.0.0.1', 'Unknown', '2026-09-09 05:41:47');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('20', '1', 'TICKET_STATUS_UPDATED', 'order_tickets', '13', NULL, '\"Ticket #TKT-ST-KITCHEN-20260909-7103 set to PREPARING\"', '127.0.0.1', 'Unknown', '2026-09-09 05:41:47');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('21', '1', 'TICKET_STATUS_UPDATED', 'order_tickets', '13', NULL, '\"Ticket #TKT-ST-KITCHEN-20260909-7103 set to READY\"', '127.0.0.1', 'Unknown', '2026-09-09 05:41:47');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('23', '1', 'PAYMENT_CREATED', 'payments', '2', NULL, '{\"order_id\":12,\"amount\":200,\"method\":\"Cash\"}', '127.0.0.1', 'Unknown', '2026-09-09 05:46:28');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('24', '1', 'PAYMENT_CREATED', 'payments', '3', NULL, '{\"order_id\":12,\"amount\":325,\"method\":\"bKash Mobile Money\"}', '127.0.0.1', 'Unknown', '2026-09-09 05:46:28');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('25', '1', 'PAYMENT_CREATED', 'payments', '4', NULL, '{\"order_id\":13,\"amount\":200,\"method\":\"Cash\"}', '127.0.0.1', 'Unknown', '2026-09-09 05:46:43');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('26', '1', 'PAYMENT_CREATED', 'payments', '5', NULL, '{\"order_id\":13,\"amount\":325,\"method\":\"bKash Mobile Money\"}', '127.0.0.1', 'Unknown', '2026-09-09 05:46:43');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('27', '1', 'PAYMENT_CREATED', 'payments', '6', NULL, '{\"order_id\":14,\"amount\":200,\"method\":\"Cash\"}', '127.0.0.1', 'Unknown', '2026-09-09 05:46:55');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('28', '1', 'PAYMENT_CREATED', 'payments', '7', NULL, '{\"order_id\":14,\"amount\":325,\"method\":\"bKash Mobile Money\"}', '127.0.0.1', 'Unknown', '2026-09-09 05:46:55');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('29', '1', 'PAYMENT_CREATED', 'payments', '8', NULL, '{\"order_id\":15,\"amount\":200,\"method\":\"Cash\"}', '127.0.0.1', 'Unknown', '2026-09-09 05:47:02');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('30', '1', 'PAYMENT_CREATED', 'payments', '9', NULL, '{\"order_id\":15,\"amount\":325,\"method\":\"bKash Mobile Money\"}', '127.0.0.1', 'Unknown', '2026-09-09 05:47:02');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('31', '1', 'REFUND_PROCESSED', 'refunds', '1', NULL, '{\"payment_id\":8,\"order_id\":15,\"refund_amount\":50,\"reason\":\"Customer price adjustment discount request\"}', '127.0.0.1', 'Unknown', '2026-09-09 05:47:02');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('32', '1', 'WAITER_PROFILE_CREATED', 'waiter_profiles', '1', NULL, '{\"employee_code\":\"WTR-001\",\"user_name\":\"System Administrator\"}', '127.0.0.1', 'Unknown', '2026-09-09 05:50:13');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('33', '1', 'WAITER_ASSIGNED', 'waiter_assignments', '2', NULL, '{\"waiter_id\":1,\"table_id\":2,\"dining_session_id\":null,\"assignment_type\":\"TABLE\"}', '127.0.0.1', 'Unknown', '2026-09-09 05:51:12');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('34', '1', 'WAITER_ASSIGNED', 'waiter_assignments', '3', NULL, '{\"waiter_id\":1,\"table_id\":2,\"dining_session_id\":null,\"assignment_type\":\"TABLE\"}', '127.0.0.1', 'Unknown', '2026-09-09 05:51:21');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('35', '1', 'PAYMENT_CREATED', 'payments', '10', NULL, '{\"order_id\":17,\"amount\":1050,\"method\":\"Cash\"}', '127.0.0.1', 'Unknown', '2026-09-09 05:51:21');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('36', '1', 'COMMISSION_CREATED', 'commission_transactions', '1', NULL, '{\"order_id\":17,\"waiter_id\":1,\"base_amount\":1000,\"commission_amount\":50}', '127.0.0.1', 'Unknown', '2026-09-09 05:51:21');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('37', '1', 'REFUND_PROCESSED', 'refunds', '2', NULL, '{\"payment_id\":10,\"order_id\":17,\"refund_amount\":525,\"reason\":\"Customer partial dish refund\"}', '127.0.0.1', 'Unknown', '2026-09-09 05:51:21');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('38', '1', 'COMMISSION_ADJUSTED', 'commission_transactions', '1', NULL, '{\"refund_id\":2,\"original_commission\":50,\"reversal_amount\":25,\"new_commission_amount\":25}', '127.0.0.1', 'Unknown', '2026-09-09 05:51:21');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('39', '1', 'WAITER_ASSIGNED', 'waiter_assignments', '4', NULL, '{\"waiter_id\":1,\"table_id\":2,\"dining_session_id\":null,\"assignment_type\":\"TABLE\"}', '127.0.0.1', 'Unknown', '2026-09-09 05:51:31');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('40', '1', 'PAYMENT_CREATED', 'payments', '11', NULL, '{\"order_id\":18,\"amount\":1050,\"method\":\"Cash\"}', '127.0.0.1', 'Unknown', '2026-09-09 05:51:31');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('41', '1', 'COMMISSION_CREATED', 'commission_transactions', '2', NULL, '{\"order_id\":18,\"waiter_id\":1,\"base_amount\":1000,\"commission_amount\":50}', '127.0.0.1', 'Unknown', '2026-09-09 05:51:31');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('42', '1', 'REFUND_PROCESSED', 'refunds', '3', NULL, '{\"payment_id\":11,\"order_id\":18,\"refund_amount\":525,\"reason\":\"Customer partial dish refund\"}', '127.0.0.1', 'Unknown', '2026-09-09 05:51:31');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('43', '1', 'COMMISSION_ADJUSTED', 'commission_transactions', '2', NULL, '{\"refund_id\":3,\"original_commission\":50,\"reversal_amount\":25,\"new_commission_amount\":25}', '127.0.0.1', 'Unknown', '2026-09-09 05:51:31');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('44', '1', 'COMMISSION_APPROVED', 'commission_transactions', '2', NULL, '{\"waiter_id\":1,\"amount\":\"25.00\"}', '127.0.0.1', 'Unknown', '2026-09-09 05:51:31');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('45', '1', 'COMMISSION_PAYOUT_PROCESSED', 'commission_payouts', '1', NULL, '{\"payout_number\":\"PAYOUT-20260908-164055\",\"waiter_id\":1,\"total_amount\":25,\"item_count\":1}', '127.0.0.1', 'Unknown', '2026-09-09 05:51:31');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('46', '1', 'WAITER_ASSIGNED', 'waiter_assignments', '5', NULL, '{\"waiter_id\":1,\"table_id\":2,\"dining_session_id\":null,\"assignment_type\":\"TABLE\"}', '127.0.0.1', 'Unknown', '2026-09-09 05:51:40');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('47', '1', 'PAYMENT_CREATED', 'payments', '12', NULL, '{\"order_id\":19,\"amount\":1050,\"method\":\"Cash\"}', '127.0.0.1', 'Unknown', '2026-09-09 05:51:40');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('48', '1', 'COMMISSION_CREATED', 'commission_transactions', '3', NULL, '{\"order_id\":19,\"waiter_id\":1,\"base_amount\":1000,\"commission_amount\":50}', '127.0.0.1', 'Unknown', '2026-09-09 05:51:40');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('49', '1', 'REFUND_PROCESSED', 'refunds', '4', NULL, '{\"payment_id\":12,\"order_id\":19,\"refund_amount\":525,\"reason\":\"Customer partial dish refund\"}', '127.0.0.1', 'Unknown', '2026-09-09 05:51:40');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('50', '1', 'COMMISSION_ADJUSTED', 'commission_transactions', '3', NULL, '{\"refund_id\":4,\"original_commission\":50,\"reversal_amount\":25,\"new_commission_amount\":25}', '127.0.0.1', 'Unknown', '2026-09-09 05:51:40');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('51', '1', 'COMMISSION_APPROVED', 'commission_transactions', '3', NULL, '{\"waiter_id\":1,\"amount\":\"25.00\"}', '127.0.0.1', 'Unknown', '2026-09-09 05:51:40');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('52', '1', 'COMMISSION_PAYOUT_PROCESSED', 'commission_payouts', '2', NULL, '{\"payout_number\":\"PAYOUT-20260908-959142\",\"waiter_id\":1,\"total_amount\":25,\"item_count\":1}', '127.0.0.1', 'Unknown', '2026-09-09 05:51:40');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('53', '1', 'WAITER_ASSIGNED', 'waiter_assignments', '6', NULL, '{\"waiter_id\":1,\"table_id\":2,\"dining_session_id\":null,\"assignment_type\":\"TABLE\"}', '127.0.0.1', 'Unknown', '2026-09-09 05:51:48');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('54', '1', 'PAYMENT_CREATED', 'payments', '13', NULL, '{\"order_id\":20,\"amount\":1050,\"method\":\"Cash\"}', '127.0.0.1', 'Unknown', '2026-09-09 05:51:48');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('55', '1', 'COMMISSION_CREATED', 'commission_transactions', '4', NULL, '{\"order_id\":20,\"waiter_id\":1,\"base_amount\":1000,\"commission_amount\":50}', '127.0.0.1', 'Unknown', '2026-09-09 05:51:48');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('56', '1', 'REFUND_PROCESSED', 'refunds', '5', NULL, '{\"payment_id\":13,\"order_id\":20,\"refund_amount\":525,\"reason\":\"Customer partial dish refund\"}', '127.0.0.1', 'Unknown', '2026-09-09 05:51:48');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('57', '1', 'COMMISSION_ADJUSTED', 'commission_transactions', '4', NULL, '{\"refund_id\":5,\"original_commission\":50,\"reversal_amount\":25,\"new_commission_amount\":25}', '127.0.0.1', 'Unknown', '2026-09-09 05:51:48');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('58', '1', 'COMMISSION_APPROVED', 'commission_transactions', '4', NULL, '{\"waiter_id\":1,\"amount\":\"25.00\"}', '127.0.0.1', 'Unknown', '2026-09-09 05:51:48');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('59', '1', 'COMMISSION_PAYOUT_PROCESSED', 'commission_payouts', '3', NULL, '{\"payout_number\":\"PAYOUT-20260908-554750\",\"waiter_id\":1,\"total_amount\":25,\"item_count\":1}', '127.0.0.1', 'Unknown', '2026-09-09 05:51:48');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('60', '1', 'STATION_CREATED', 'stations', '5', NULL, '\"Operational Station \'Test Grill Station\' (GRILL) created\"', '127.0.0.1', 'Unknown', '2026-09-09 05:54:57');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('61', '1', 'STATION_PAUSED', 'stations', '5', NULL, '\"Station ID #5 paused (Reason: Midday Cleaning Break)\"', '127.0.0.1', 'Unknown', '2026-09-09 05:54:57');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('62', '1', 'STATION_ACTIVATED', 'stations', '5', NULL, '\"Station ID #5 activated\"', '127.0.0.1', 'Unknown', '2026-09-09 05:54:57');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('63', '1', 'STATION_CREATED', 'stations', '7', NULL, '\"Operational Station \'Test Grill Station 415\' (GRILL) created\"', '127.0.0.1', 'Unknown', '2026-09-09 05:55:18');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('64', '1', 'STATION_PAUSED', 'stations', '7', NULL, '\"Station ID #7 paused (Reason: Midday Cleaning Break)\"', '127.0.0.1', 'Unknown', '2026-09-09 05:55:18');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('65', '1', 'STATION_ACTIVATED', 'stations', '7', NULL, '\"Station ID #7 activated\"', '127.0.0.1', 'Unknown', '2026-09-09 05:55:18');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('66', '1', 'STATION_CREATED', 'stations', '8', NULL, '\"Operational Station \'Test Grill Station 304\' (GRILL) created\"', '127.0.0.1', 'Unknown', '2026-09-09 05:55:25');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('67', '1', 'STATION_PAUSED', 'stations', '8', NULL, '\"Station ID #8 paused (Reason: Midday Cleaning Break)\"', '127.0.0.1', 'Unknown', '2026-09-09 05:55:25');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('68', '1', 'STATION_ACTIVATED', 'stations', '8', NULL, '\"Station ID #8 activated\"', '127.0.0.1', 'Unknown', '2026-09-09 05:55:25');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('69', '1', 'STATION_CREATED', 'stations', '9', NULL, '\"Operational Station \'Test Grill Station 146\' (GRILL) created\"', '127.0.0.1', 'Unknown', '2026-09-09 05:55:33');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('70', '1', 'STATION_PAUSED', 'stations', '9', NULL, '\"Station ID #9 paused (Reason: Midday Cleaning Break)\"', '127.0.0.1', 'Unknown', '2026-09-09 05:55:33');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('71', '1', 'STATION_ACTIVATED', 'stations', '9', NULL, '\"Station ID #9 activated\"', '127.0.0.1', 'Unknown', '2026-09-09 05:55:33');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('72', '1', 'ROUTE_CREATED', 'orders', '24', NULL, '\"Order #TEST-ORD-KDS-1788911733 routed to 2 station(s).\"', '127.0.0.1', 'Unknown', '2026-09-09 05:55:33');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('73', '1', 'TICKET_STATUS_UPDATED', 'order_tickets', '16', NULL, '\"Ticket #TKT-ST-KITCHEN-20260908-2680 status changed from NEW to PREPARING\"', '127.0.0.1', 'Unknown', '2026-09-09 05:55:33');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('74', '1', 'TICKET_STATUS_UPDATED', 'order_tickets', '16', NULL, '\"Ticket #TKT-ST-KITCHEN-20260908-2680 status changed from PREPARING to READY\"', '127.0.0.1', 'Unknown', '2026-09-09 05:55:33');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('75', '1', 'TICKET_RECALLED', 'order_tickets', '16', NULL, '\"Ticket #TKT-ST-KITCHEN-20260908-2680 recalled from READY to PREPARING\"', '127.0.0.1', 'Unknown', '2026-09-09 05:55:33');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('76', '1', 'STATION_CREATED', 'stations', '10', NULL, '\"Operational Station \'Test Grill Station 276\' (GRILL) created\"', '127.0.0.1', 'Unknown', '2026-09-09 05:55:45');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('77', '1', 'STATION_PAUSED', 'stations', '10', NULL, '\"Station ID #10 paused (Reason: Midday Cleaning Break)\"', '127.0.0.1', 'Unknown', '2026-09-09 05:55:45');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('78', '1', 'STATION_ACTIVATED', 'stations', '10', NULL, '\"Station ID #10 activated\"', '127.0.0.1', 'Unknown', '2026-09-09 05:55:45');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('79', '1', 'ROUTE_CREATED', 'orders', '25', NULL, '\"Order #TEST-ORD-KDS-1788911745 routed to 2 station(s).\"', '127.0.0.1', 'Unknown', '2026-09-09 05:55:46');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('80', '1', 'TICKET_STATUS_UPDATED', 'order_tickets', '18', NULL, '\"Ticket #TKT-ST-KITCHEN-20260908-6375 status changed from NEW to PREPARING\"', '127.0.0.1', 'Unknown', '2026-09-09 05:55:46');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('81', '1', 'TICKET_STATUS_UPDATED', 'order_tickets', '18', NULL, '\"Ticket #TKT-ST-KITCHEN-20260908-6375 status changed from PREPARING to READY\"', '127.0.0.1', 'Unknown', '2026-09-09 05:55:46');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('82', '1', 'TICKET_RECALLED', 'order_tickets', '18', NULL, '\"Ticket #TKT-ST-KITCHEN-20260908-6375 recalled from READY to PREPARING\"', '127.0.0.1', 'Unknown', '2026-09-09 05:55:46');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('83', '1', 'TICKET_REFIRED', 'order_tickets', '18', NULL, '\"Re-fired ticket #TKT-ST-KITCHEN-20260908-6375 -> New Ticket #TKT-ST-KITCHEN-REFIRE-20260908-8080 (Reason: Burned Patty \\/ Kitchen remake)\"', '127.0.0.1', 'Unknown', '2026-09-09 05:55:46');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('84', '1', 'STATION_CREATED', 'stations', '11', NULL, '\"Operational Station \'Test Grill Station 714\' (GRILL) created\"', '127.0.0.1', 'Unknown', '2026-09-09 05:56:00');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('85', '1', 'STATION_PAUSED', 'stations', '11', NULL, '\"Station ID #11 paused (Reason: Midday Cleaning Break)\"', '127.0.0.1', 'Unknown', '2026-09-09 05:56:00');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('86', '1', 'STATION_ACTIVATED', 'stations', '11', NULL, '\"Station ID #11 activated\"', '127.0.0.1', 'Unknown', '2026-09-09 05:56:00');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('87', '1', 'ROUTE_CREATED', 'orders', '26', NULL, '\"Order #TEST-ORD-KDS-1788911760 routed to 2 station(s).\"', '127.0.0.1', 'Unknown', '2026-09-09 05:56:00');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('88', '1', 'TICKET_STATUS_UPDATED', 'order_tickets', '21', NULL, '\"Ticket #TKT-ST-KITCHEN-20260908-8797 status changed from NEW to PREPARING\"', '127.0.0.1', 'Unknown', '2026-09-09 05:56:00');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('89', '1', 'TICKET_STATUS_UPDATED', 'order_tickets', '21', NULL, '\"Ticket #TKT-ST-KITCHEN-20260908-8797 status changed from PREPARING to READY\"', '127.0.0.1', 'Unknown', '2026-09-09 05:56:00');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('90', '1', 'TICKET_RECALLED', 'order_tickets', '21', NULL, '\"Ticket #TKT-ST-KITCHEN-20260908-8797 recalled from READY to PREPARING\"', '127.0.0.1', 'Unknown', '2026-09-09 05:56:00');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('91', '1', 'TICKET_REFIRED', 'order_tickets', '21', NULL, '\"Re-fired ticket #TKT-ST-KITCHEN-20260908-8797 -> New Ticket #TKT-ST-KITCHEN-REFIRE-20260908-2413 (Reason: Burned Patty \\/ Kitchen remake)\"', '127.0.0.1', 'Unknown', '2026-09-09 05:56:00');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('92', '1', 'TICKET_CANCELLED', 'order_tickets', '22', NULL, '\"Ticket #TKT-ST-BAR-20260908-1119 cancelled (Reason: Customer changed mind)\"', '127.0.0.1', 'Unknown', '2026-09-09 05:56:00');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('93', '1', 'TICKET_STATUS_UPDATED', 'order_tickets', '23', NULL, '\"Ticket #TKT-ST-KITCHEN-REFIRE-20260908-2413 status changed from NEW to PREPARING\"', '127.0.0.1', 'Unknown', '2026-09-09 05:56:00');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('94', '1', 'TICKET_STATUS_UPDATED', 'order_tickets', '23', NULL, '\"Ticket #TKT-ST-KITCHEN-REFIRE-20260908-2413 status changed from PREPARING to READY\"', '127.0.0.1', 'Unknown', '2026-09-09 05:56:00');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('95', '1', 'STATION_CREATED', 'stations', '12', NULL, '\"Operational Station \'Test Grill Station 966\' (GRILL) created\"', '127.0.0.1', 'Unknown', '2026-09-09 05:56:07');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('96', '1', 'STATION_PAUSED', 'stations', '12', NULL, '\"Station ID #12 paused (Reason: Midday Cleaning Break)\"', '127.0.0.1', 'Unknown', '2026-09-09 05:56:07');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('97', '1', 'STATION_ACTIVATED', 'stations', '12', NULL, '\"Station ID #12 activated\"', '127.0.0.1', 'Unknown', '2026-09-09 05:56:07');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('98', '1', 'ROUTE_CREATED', 'orders', '27', NULL, '\"Order #TEST-ORD-KDS-1788911767 routed to 2 station(s).\"', '127.0.0.1', 'Unknown', '2026-09-09 05:56:07');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('99', '1', 'TICKET_STATUS_UPDATED', 'order_tickets', '24', NULL, '\"Ticket #TKT-ST-KITCHEN-20260908-5549 status changed from NEW to PREPARING\"', '127.0.0.1', 'Unknown', '2026-09-09 05:56:07');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('100', '1', 'TICKET_STATUS_UPDATED', 'order_tickets', '24', NULL, '\"Ticket #TKT-ST-KITCHEN-20260908-5549 status changed from PREPARING to READY\"', '127.0.0.1', 'Unknown', '2026-09-09 05:56:07');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('101', '1', 'TICKET_RECALLED', 'order_tickets', '24', NULL, '\"Ticket #TKT-ST-KITCHEN-20260908-5549 recalled from READY to PREPARING\"', '127.0.0.1', 'Unknown', '2026-09-09 05:56:07');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('102', '1', 'TICKET_REFIRED', 'order_tickets', '24', NULL, '\"Re-fired ticket #TKT-ST-KITCHEN-20260908-5549 -> New Ticket #TKT-ST-KITCHEN-REFIRE-20260908-6963 (Reason: Burned Patty \\/ Kitchen remake)\"', '127.0.0.1', 'Unknown', '2026-09-09 05:56:07');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('103', '1', 'TICKET_CANCELLED', 'order_tickets', '25', NULL, '\"Ticket #TKT-ST-BAR-20260908-3903 cancelled (Reason: Customer changed mind)\"', '127.0.0.1', 'Unknown', '2026-09-09 05:56:07');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('104', '1', 'TICKET_STATUS_UPDATED', 'order_tickets', '24', NULL, '\"Ticket #TKT-ST-KITCHEN-20260908-5549 status changed from PREPARING to READY\"', '127.0.0.1', 'Unknown', '2026-09-09 05:56:07');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('105', '1', 'TICKET_STATUS_UPDATED', 'order_tickets', '26', NULL, '\"Ticket #TKT-ST-KITCHEN-REFIRE-20260908-6963 status changed from NEW to PREPARING\"', '127.0.0.1', 'Unknown', '2026-09-09 05:56:07');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('106', '1', 'TICKET_STATUS_UPDATED', 'order_tickets', '26', NULL, '\"Ticket #TKT-ST-KITCHEN-REFIRE-20260908-6963 status changed from PREPARING to READY\"', '127.0.0.1', 'Unknown', '2026-09-09 05:56:07');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('107', '1', 'INGREDIENT_CREATED', 'ingredients', '1', NULL, '\"Ingredient \'Premium Basmati Rice Test\' created.\"', '127.0.0.1', 'Unknown', '2026-09-09 06:02:36');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('108', '1', 'INGREDIENT_CREATED', 'ingredients', '3', NULL, '\"Ingredient \'Premium Basmati Rice Test 4366\' created.\"', '127.0.0.1', 'Unknown', '2026-09-09 06:02:59');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('109', '1', 'STOCK_ADJUSTED', 'inventory_stock', '3', NULL, '\"Stock adjusted (IN 20 kg). New balance: 20\"', '127.0.0.1', 'Unknown', '2026-09-09 06:02:59');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('110', '1', 'INGREDIENT_CREATED', 'ingredients', '4', NULL, '\"Ingredient \'Premium Basmati Rice Test 4224\' created.\"', '127.0.0.1', 'Unknown', '2026-09-09 06:03:28');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('111', '1', 'STOCK_ADJUSTED', 'inventory_stock', '4', NULL, '\"Stock adjusted (IN 20 kg). New balance: 20\"', '127.0.0.1', 'Unknown', '2026-09-09 06:03:28');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('112', '1', 'PURCHASE_ORDER_CREATED', 'purchase_orders', '2', NULL, '\"PO #PO-20260909-5881 created (Grand Total: \\u09f35500)\"', '127.0.0.1', 'Unknown', '2026-09-09 06:03:28');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('113', '1', 'INGREDIENT_CREATED', 'ingredients', '5', NULL, '\"Ingredient \'Premium Basmati Rice Test 6531\' created.\"', '127.0.0.1', 'Unknown', '2026-09-09 06:03:50');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('114', '1', 'STOCK_ADJUSTED', 'inventory_stock', '5', NULL, '\"Stock adjusted (IN 20 kg). New balance: 20\"', '127.0.0.1', 'Unknown', '2026-09-09 06:03:50');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('115', '1', 'PURCHASE_ORDER_CREATED', 'purchase_orders', '3', NULL, '\"PO #PO-20260909-1731 created (Grand Total: \\u09f35500)\"', '127.0.0.1', 'Unknown', '2026-09-09 06:03:50');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('116', '1', 'PURCHASE_ORDER_STATUS', 'purchase_orders', '3', NULL, '\"PO #PO-20260909-1731 status updated to APPROVED\"', '127.0.0.1', 'Unknown', '2026-09-09 06:03:50');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('117', '1', 'INGREDIENT_CREATED', 'ingredients', '6', NULL, '\"Ingredient \'Premium Basmati Rice Test 7985\' created.\"', '127.0.0.1', 'Unknown', '2026-09-09 06:04:00');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('118', '1', 'STOCK_ADJUSTED', 'inventory_stock', '6', NULL, '\"Stock adjusted (IN 20 kg). New balance: 20\"', '127.0.0.1', 'Unknown', '2026-09-09 06:04:00');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('119', '1', 'PURCHASE_ORDER_CREATED', 'purchase_orders', '4', NULL, '\"PO #PO-20260909-5931 created (Grand Total: \\u09f35500)\"', '127.0.0.1', 'Unknown', '2026-09-09 06:04:00');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('120', '1', 'PURCHASE_ORDER_STATUS', 'purchase_orders', '4', NULL, '\"PO #PO-20260909-5931 status updated to APPROVED\"', '127.0.0.1', 'Unknown', '2026-09-09 06:04:00');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('121', '1', 'GOODS_RECEIVED', 'goods_receipts', '1', NULL, '\"Goods Receipt #GRN-20260909-5183 processed for PO #PO-20260909-5931\"', '127.0.0.1', 'Unknown', '2026-09-09 06:04:00');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('122', '1', 'INGREDIENT_CREATED', 'ingredients', '7', NULL, '\"Ingredient \'Premium Basmati Rice Test 6094\' created.\"', '127.0.0.1', 'Unknown', '2026-09-09 06:04:08');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('123', '1', 'STOCK_ADJUSTED', 'inventory_stock', '7', NULL, '\"Stock adjusted (IN 20 kg). New balance: 20\"', '127.0.0.1', 'Unknown', '2026-09-09 06:04:08');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('124', '1', 'PURCHASE_ORDER_CREATED', 'purchase_orders', '5', NULL, '\"PO #PO-20260909-1769 created (Grand Total: \\u09f35500)\"', '127.0.0.1', 'Unknown', '2026-09-09 06:04:08');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('125', '1', 'PURCHASE_ORDER_STATUS', 'purchase_orders', '5', NULL, '\"PO #PO-20260909-1769 status updated to APPROVED\"', '127.0.0.1', 'Unknown', '2026-09-09 06:04:08');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('126', '1', 'GOODS_RECEIVED', 'goods_receipts', '2', NULL, '\"Goods Receipt #GRN-20260909-5451 processed for PO #PO-20260909-1769\"', '127.0.0.1', 'Unknown', '2026-09-09 06:04:08');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('127', '1', 'INGREDIENT_CREATED', 'ingredients', '8', NULL, '\"Ingredient \'Premium Basmati Rice Test 5605\' created.\"', '127.0.0.1', 'Unknown', '2026-09-09 06:04:26');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('128', '1', 'STOCK_ADJUSTED', 'inventory_stock', '8', NULL, '\"Stock adjusted (IN 20 kg). New balance: 20\"', '127.0.0.1', 'Unknown', '2026-09-09 06:04:26');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('129', '1', 'PURCHASE_ORDER_CREATED', 'purchase_orders', '6', NULL, '\"PO #PO-20260909-8246 created (Grand Total: \\u09f35500)\"', '127.0.0.1', 'Unknown', '2026-09-09 06:04:26');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('130', '1', 'PURCHASE_ORDER_STATUS', 'purchase_orders', '6', NULL, '\"PO #PO-20260909-8246 status updated to APPROVED\"', '127.0.0.1', 'Unknown', '2026-09-09 06:04:26');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('131', '1', 'GOODS_RECEIVED', 'goods_receipts', '3', NULL, '\"Goods Receipt #GRN-20260909-9573 processed for PO #PO-20260909-8246\"', '127.0.0.1', 'Unknown', '2026-09-09 06:04:26');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('132', '1', 'INGREDIENT_CREATED', 'ingredients', '9', NULL, '\"Ingredient \'Premium Basmati Rice Test 7960\' created.\"', '127.0.0.1', 'Unknown', '2026-09-09 06:04:35');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('133', '1', 'STOCK_ADJUSTED', 'inventory_stock', '9', NULL, '\"Stock adjusted (IN 20 kg). New balance: 20\"', '127.0.0.1', 'Unknown', '2026-09-09 06:04:35');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('134', '1', 'PURCHASE_ORDER_CREATED', 'purchase_orders', '7', NULL, '\"PO #PO-20260909-8070 created (Grand Total: \\u09f35500)\"', '127.0.0.1', 'Unknown', '2026-09-09 06:04:35');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('135', '1', 'PURCHASE_ORDER_STATUS', 'purchase_orders', '7', NULL, '\"PO #PO-20260909-8070 status updated to APPROVED\"', '127.0.0.1', 'Unknown', '2026-09-09 06:04:35');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('136', '1', 'GOODS_RECEIVED', 'goods_receipts', '4', NULL, '\"Goods Receipt #GRN-20260909-6174 processed for PO #PO-20260909-8070\"', '127.0.0.1', 'Unknown', '2026-09-09 06:04:35');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('137', '1', 'INGREDIENT_CREATED', 'ingredients', '10', NULL, '\"Ingredient \'Premium Basmati Rice Test 3567\' created.\"', '127.0.0.1', 'Unknown', '2026-09-09 06:04:43');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('138', '1', 'STOCK_ADJUSTED', 'inventory_stock', '10', NULL, '\"Stock adjusted (IN 20 kg). New balance: 20\"', '127.0.0.1', 'Unknown', '2026-09-09 06:04:43');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('139', '1', 'PURCHASE_ORDER_CREATED', 'purchase_orders', '8', NULL, '\"PO #PO-20260909-7475 created (Grand Total: \\u09f35500)\"', '127.0.0.1', 'Unknown', '2026-09-09 06:04:43');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('140', '1', 'PURCHASE_ORDER_STATUS', 'purchase_orders', '8', NULL, '\"PO #PO-20260909-7475 status updated to APPROVED\"', '127.0.0.1', 'Unknown', '2026-09-09 06:04:43');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('141', '1', 'GOODS_RECEIVED', 'goods_receipts', '5', NULL, '\"Goods Receipt #GRN-20260909-8859 processed for PO #PO-20260909-7475\"', '127.0.0.1', 'Unknown', '2026-09-09 06:04:43');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('142', '1', 'RECIPE_SAVED', 'recipes', '1', NULL, '\"Recipe BOM saved for Product #4 (Cost: \\u09f333.86)\"', '127.0.0.1', 'Unknown', '2026-09-09 06:04:43');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('143', '1', 'INGREDIENT_CREATED', 'ingredients', '11', NULL, '\"Ingredient \'Premium Basmati Rice Test 8820\' created.\"', '127.0.0.1', 'Unknown', '2026-09-09 06:04:55');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('144', '1', 'STOCK_ADJUSTED', 'inventory_stock', '11', NULL, '\"Stock adjusted (IN 20 kg). New balance: 20\"', '127.0.0.1', 'Unknown', '2026-09-09 06:04:55');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('145', '1', 'PURCHASE_ORDER_CREATED', 'purchase_orders', '9', NULL, '\"PO #PO-20260909-2668 created (Grand Total: \\u09f35500)\"', '127.0.0.1', 'Unknown', '2026-09-09 06:04:55');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('146', '1', 'PURCHASE_ORDER_STATUS', 'purchase_orders', '9', NULL, '\"PO #PO-20260909-2668 status updated to APPROVED\"', '127.0.0.1', 'Unknown', '2026-09-09 06:04:55');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('147', '1', 'GOODS_RECEIVED', 'goods_receipts', '6', NULL, '\"Goods Receipt #GRN-20260909-6859 processed for PO #PO-20260909-2668\"', '127.0.0.1', 'Unknown', '2026-09-09 06:04:55');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('148', '1', 'RECIPE_SAVED', 'recipes', '2', NULL, '\"Recipe BOM saved for Product #4 (Cost: \\u09f333.86)\"', '127.0.0.1', 'Unknown', '2026-09-09 06:04:55');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('149', '1', 'INGREDIENT_CREATED', 'ingredients', '12', NULL, '\"Ingredient \'Premium Basmati Rice Test 4705\' created.\"', '127.0.0.1', 'Unknown', '2026-09-09 06:05:06');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('150', '1', 'STOCK_ADJUSTED', 'inventory_stock', '12', NULL, '\"Stock adjusted (IN 20 kg). New balance: 20\"', '127.0.0.1', 'Unknown', '2026-09-09 06:05:06');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('151', '1', 'PURCHASE_ORDER_CREATED', 'purchase_orders', '10', NULL, '\"PO #PO-20260909-8780 created (Grand Total: \\u09f35500)\"', '127.0.0.1', 'Unknown', '2026-09-09 06:05:06');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('152', '1', 'PURCHASE_ORDER_STATUS', 'purchase_orders', '10', NULL, '\"PO #PO-20260909-8780 status updated to APPROVED\"', '127.0.0.1', 'Unknown', '2026-09-09 06:05:06');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('153', '1', 'GOODS_RECEIVED', 'goods_receipts', '7', NULL, '\"Goods Receipt #GRN-20260909-6300 processed for PO #PO-20260909-8780\"', '127.0.0.1', 'Unknown', '2026-09-09 06:05:06');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('154', '1', 'RECIPE_SAVED', 'recipes', '3', NULL, '\"Recipe BOM saved for Product #4 (Cost: \\u09f333.86)\"', '127.0.0.1', 'Unknown', '2026-09-09 06:05:06');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('155', '1', 'INGREDIENT_CREATED', 'ingredients', '13', NULL, '\"Ingredient \'Premium Basmati Rice Test 5943\' created.\"', '127.0.0.1', 'Unknown', '2026-09-09 06:05:15');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('156', '1', 'STOCK_ADJUSTED', 'inventory_stock', '13', NULL, '\"Stock adjusted (IN 20 kg). New balance: 20\"', '127.0.0.1', 'Unknown', '2026-09-09 06:05:15');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('157', '1', 'PURCHASE_ORDER_CREATED', 'purchase_orders', '11', NULL, '\"PO #PO-20260909-9955 created (Grand Total: \\u09f35500)\"', '127.0.0.1', 'Unknown', '2026-09-09 06:05:15');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('158', '1', 'PURCHASE_ORDER_STATUS', 'purchase_orders', '11', NULL, '\"PO #PO-20260909-9955 status updated to APPROVED\"', '127.0.0.1', 'Unknown', '2026-09-09 06:05:15');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('159', '1', 'GOODS_RECEIVED', 'goods_receipts', '8', NULL, '\"Goods Receipt #GRN-20260909-7175 processed for PO #PO-20260909-9955\"', '127.0.0.1', 'Unknown', '2026-09-09 06:05:15');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('160', '1', 'RECIPE_SAVED', 'recipes', '4', NULL, '\"Recipe BOM saved for Product #4 (Cost: \\u09f333.86)\"', '127.0.0.1', 'Unknown', '2026-09-09 06:05:15');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('161', '1', 'INGREDIENT_CREATED', 'ingredients', '14', NULL, '\"Ingredient \'Premium Basmati Rice Test 2713\' created.\"', '127.0.0.1', 'Unknown', '2026-09-09 06:05:23');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('162', '1', 'STOCK_ADJUSTED', 'inventory_stock', '14', NULL, '\"Stock adjusted (IN 20 kg). New balance: 20\"', '127.0.0.1', 'Unknown', '2026-09-09 06:05:23');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('163', '1', 'PURCHASE_ORDER_CREATED', 'purchase_orders', '12', NULL, '\"PO #PO-20260909-8592 created (Grand Total: \\u09f35500)\"', '127.0.0.1', 'Unknown', '2026-09-09 06:05:23');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('164', '1', 'PURCHASE_ORDER_STATUS', 'purchase_orders', '12', NULL, '\"PO #PO-20260909-8592 status updated to APPROVED\"', '127.0.0.1', 'Unknown', '2026-09-09 06:05:23');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('165', '1', 'GOODS_RECEIVED', 'goods_receipts', '9', NULL, '\"Goods Receipt #GRN-20260909-5318 processed for PO #PO-20260909-8592\"', '127.0.0.1', 'Unknown', '2026-09-09 06:05:23');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('166', '1', 'RECIPE_SAVED', 'recipes', '5', NULL, '\"Recipe BOM saved for Product #4 (Cost: \\u09f333.86)\"', '127.0.0.1', 'Unknown', '2026-09-09 06:05:23');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('167', '1', 'INGREDIENT_CREATED', 'ingredients', '15', NULL, '\"Ingredient \'Premium Basmati Rice Test 1556\' created.\"', '127.0.0.1', 'Unknown', '2026-09-09 06:05:31');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('168', '1', 'STOCK_ADJUSTED', 'inventory_stock', '15', NULL, '\"Stock adjusted (IN 20 kg). New balance: 20\"', '127.0.0.1', 'Unknown', '2026-09-09 06:05:31');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('169', '1', 'PURCHASE_ORDER_CREATED', 'purchase_orders', '13', NULL, '\"PO #PO-20260909-3565 created (Grand Total: \\u09f35500)\"', '127.0.0.1', 'Unknown', '2026-09-09 06:05:31');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('170', '1', 'PURCHASE_ORDER_STATUS', 'purchase_orders', '13', NULL, '\"PO #PO-20260909-3565 status updated to APPROVED\"', '127.0.0.1', 'Unknown', '2026-09-09 06:05:31');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('171', '1', 'GOODS_RECEIVED', 'goods_receipts', '10', NULL, '\"Goods Receipt #GRN-20260909-4955 processed for PO #PO-20260909-3565\"', '127.0.0.1', 'Unknown', '2026-09-09 06:05:31');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('172', '1', 'RECIPE_SAVED', 'recipes', '6', NULL, '\"Recipe BOM saved for Product #4 (Cost: \\u09f333.86)\"', '127.0.0.1', 'Unknown', '2026-09-09 06:05:31');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('173', '1', 'INGREDIENT_CREATED', 'ingredients', '16', NULL, '\"Ingredient \'Premium Basmati Rice Test 3786\' created.\"', '127.0.0.1', 'Unknown', '2026-09-09 06:05:43');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('174', '1', 'STOCK_ADJUSTED', 'inventory_stock', '16', NULL, '\"Stock adjusted (IN 20 kg). New balance: 20\"', '127.0.0.1', 'Unknown', '2026-09-09 06:05:43');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('175', '1', 'PURCHASE_ORDER_CREATED', 'purchase_orders', '14', NULL, '\"PO #PO-20260909-6227 created (Grand Total: \\u09f35500)\"', '127.0.0.1', 'Unknown', '2026-09-09 06:05:43');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('176', '1', 'PURCHASE_ORDER_STATUS', 'purchase_orders', '14', NULL, '\"PO #PO-20260909-6227 status updated to APPROVED\"', '127.0.0.1', 'Unknown', '2026-09-09 06:05:43');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('177', '1', 'GOODS_RECEIVED', 'goods_receipts', '11', NULL, '\"Goods Receipt #GRN-20260909-9809 processed for PO #PO-20260909-6227\"', '127.0.0.1', 'Unknown', '2026-09-09 06:05:43');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('178', '1', 'RECIPE_SAVED', 'recipes', '7', NULL, '\"Recipe BOM saved for Product #4 (Cost: \\u09f333.86)\"', '127.0.0.1', 'Unknown', '2026-09-09 06:05:43');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('179', '1', 'INGREDIENT_CREATED', 'ingredients', '17', NULL, '\"Ingredient \'Premium Basmati Rice Test 8331\' created.\"', '127.0.0.1', 'Unknown', '2026-09-09 06:05:58');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('180', '1', 'STOCK_ADJUSTED', 'inventory_stock', '17', NULL, '\"Stock adjusted (IN 20 kg). New balance: 20\"', '127.0.0.1', 'Unknown', '2026-09-09 06:05:58');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('181', '1', 'INGREDIENT_CREATED', 'ingredients', '18', NULL, '\"Ingredient \'Premium Basmati Rice Test 3318\' created.\"', '127.0.0.1', 'Unknown', '2026-09-09 06:06:06');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('182', '1', 'STOCK_ADJUSTED', 'inventory_stock', '18', NULL, '\"Stock adjusted (IN 20 kg). New balance: 20\"', '127.0.0.1', 'Unknown', '2026-09-09 06:06:06');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('183', '1', 'PURCHASE_ORDER_CREATED', 'purchase_orders', '16', NULL, '\"PO #PO-20260909-5611 created (Grand Total: \\u09f35500)\"', '127.0.0.1', 'Unknown', '2026-09-09 06:06:06');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('184', '1', 'PURCHASE_ORDER_STATUS', 'purchase_orders', '16', NULL, '\"PO #PO-20260909-5611 status updated to APPROVED\"', '127.0.0.1', 'Unknown', '2026-09-09 06:06:06');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('185', '1', 'GOODS_RECEIVED', 'goods_receipts', '12', NULL, '\"Goods Receipt #GRN-20260909-7062 processed for PO #PO-20260909-5611\"', '127.0.0.1', 'Unknown', '2026-09-09 06:06:06');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('186', '1', 'RECIPE_SAVED', 'recipes', '8', NULL, '\"Recipe BOM saved for Product #4 (Cost: \\u09f333.86)\"', '127.0.0.1', 'Unknown', '2026-09-09 06:06:06');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('187', '1', 'INGREDIENT_CREATED', 'ingredients', '19', NULL, '\"Ingredient \'Premium Basmati Rice Test 4850\' created.\"', '127.0.0.1', 'Unknown', '2026-09-09 06:06:20');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('188', '1', 'STOCK_ADJUSTED', 'inventory_stock', '19', NULL, '\"Stock adjusted (IN 20 kg). New balance: 20\"', '127.0.0.1', 'Unknown', '2026-09-09 06:06:20');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('189', '1', 'PURCHASE_ORDER_CREATED', 'purchase_orders', '17', NULL, '\"PO #PO-20260909-6324 created (Grand Total: \\u09f35500)\"', '127.0.0.1', 'Unknown', '2026-09-09 06:06:20');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('190', '1', 'PURCHASE_ORDER_STATUS', 'purchase_orders', '17', NULL, '\"PO #PO-20260909-6324 status updated to APPROVED\"', '127.0.0.1', 'Unknown', '2026-09-09 06:06:20');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('191', '1', 'GOODS_RECEIVED', 'goods_receipts', '13', NULL, '\"Goods Receipt #GRN-20260909-1332 processed for PO #PO-20260909-6324\"', '127.0.0.1', 'Unknown', '2026-09-09 06:06:20');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('192', '1', 'RECIPE_SAVED', 'recipes', '9', NULL, '\"Recipe BOM saved for Product #4 (Cost: \\u09f333.86)\"', '127.0.0.1', 'Unknown', '2026-09-09 06:06:20');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('193', '1', 'WASTAGE_RECORDED', 'wastage_records', '1', NULL, '\"Wastage recorded: 1.5 kg of Premium Basmati Rice Test 4850 (Reason: Kitchen Spoilage \\/ Mold)\"', '127.0.0.1', 'Unknown', '2026-09-09 06:06:20');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('194', '1', 'STOCK_TRANSFERRED', 'stock_transfers', '1', NULL, '\"Stock transfer TRF-20260909-7785 completed\"', '127.0.0.1', 'Unknown', '2026-09-09 06:06:20');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('195', '1', 'INVENTORY_REVERSED', 'orders', '34', NULL, '\"Inventory consumption reversed for Order #34 (Reason: Customer returned order)\"', '127.0.0.1', 'Unknown', '2026-09-09 06:06:20');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('196', '1', 'INGREDIENT_CREATED', 'ingredients', '20', NULL, '\"Ingredient \'Premium Basmati Rice Test 4068\' created.\"', '127.0.0.1', 'Unknown', '2026-09-09 06:06:26');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('197', '1', 'STOCK_ADJUSTED', 'inventory_stock', '20', NULL, '\"Stock adjusted (IN 20 kg). New balance: 20\"', '127.0.0.1', 'Unknown', '2026-09-09 06:06:26');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('198', '1', 'PURCHASE_ORDER_CREATED', 'purchase_orders', '18', NULL, '\"PO #PO-20260909-7205 created (Grand Total: \\u09f35500)\"', '127.0.0.1', 'Unknown', '2026-09-09 06:06:26');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('199', '1', 'PURCHASE_ORDER_STATUS', 'purchase_orders', '18', NULL, '\"PO #PO-20260909-7205 status updated to APPROVED\"', '127.0.0.1', 'Unknown', '2026-09-09 06:06:26');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('200', '1', 'GOODS_RECEIVED', 'goods_receipts', '14', NULL, '\"Goods Receipt #GRN-20260909-4900 processed for PO #PO-20260909-7205\"', '127.0.0.1', 'Unknown', '2026-09-09 06:06:26');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('201', '1', 'RECIPE_SAVED', 'recipes', '10', NULL, '\"Recipe BOM saved for Product #4 (Cost: \\u09f333.86)\"', '127.0.0.1', 'Unknown', '2026-09-09 06:06:26');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('202', '1', 'WASTAGE_RECORDED', 'wastage_records', '2', NULL, '\"Wastage recorded: 1.5 kg of Premium Basmati Rice Test 4068 (Reason: Kitchen Spoilage \\/ Mold)\"', '127.0.0.1', 'Unknown', '2026-09-09 06:06:27');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('203', '1', 'STOCK_TRANSFERRED', 'stock_transfers', '2', NULL, '\"Stock transfer TRF-20260909-2944 completed\"', '127.0.0.1', 'Unknown', '2026-09-09 06:06:27');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('204', '1', 'INVENTORY_REVERSED', 'orders', '35', NULL, '\"Inventory consumption reversed for Order #35 (Reason: Customer returned order)\"', '127.0.0.1', 'Unknown', '2026-09-09 06:06:27');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('205', NULL, 'REPORT_EXPORTED', 'reports', NULL, NULL, '\"Report CSV exported: sales (Branch #1)\"', '127.0.0.1', 'Unknown', '2026-09-09 06:42:02');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('206', NULL, 'REPORT_EXPORTED', 'reports', NULL, NULL, '\"Report CSV exported: orders (Branch #1)\"', '127.0.0.1', 'Unknown', '2026-09-09 06:42:02');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('207', NULL, 'SHIFT_OPENED', 'shifts', '2', NULL, '{\"user_id\":1,\"opening_cash\":10000}', '127.0.0.1', 'Unknown', '2026-09-09 06:45:46');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('208', NULL, 'CASH_MOVEMENT_RECORDED', 'cash_movements', '2', NULL, '{\"type\":\"CASH_IN\",\"amount\":2000,\"direction\":\"IN\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:45:46');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('209', NULL, 'CASH_MOVEMENT_RECORDED', 'cash_movements', '3', NULL, '{\"type\":\"CASH_OUT\",\"amount\":500,\"direction\":\"OUT\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:45:46');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('210', NULL, 'SHIFT_OPENED', 'shifts', '3', NULL, '{\"user_id\":1,\"opening_cash\":10000}', '127.0.0.1', 'Unknown', '2026-09-09 06:46:02');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('211', NULL, 'CASH_MOVEMENT_RECORDED', 'cash_movements', '5', NULL, '{\"type\":\"CASH_IN\",\"amount\":2000,\"direction\":\"IN\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:46:02');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('212', NULL, 'CASH_MOVEMENT_RECORDED', 'cash_movements', '6', NULL, '{\"type\":\"CASH_OUT\",\"amount\":500,\"direction\":\"OUT\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:46:02');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('213', NULL, 'EXPENSE_CREATED', 'expenses', '2', NULL, '{\"title\":\"Emergency Plumbing Repair\",\"amount\":800}', '127.0.0.1', 'Unknown', '2026-09-09 06:46:02');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('214', NULL, 'CASH_MOVEMENT_RECORDED', 'cash_movements', '7', NULL, '{\"type\":\"CASH_EXPENSE\",\"amount\":800,\"direction\":\"OUT\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:46:02');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('215', NULL, 'EXPENSE_PAID', 'expenses', '2', NULL, '{\"amount\":\"800.00\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:46:02');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('216', NULL, 'SHIFT_OPENED', 'shifts', '4', NULL, '{\"user_id\":1,\"opening_cash\":10000}', '127.0.0.1', 'Unknown', '2026-09-09 06:46:12');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('217', NULL, 'CASH_MOVEMENT_RECORDED', 'cash_movements', '9', NULL, '{\"type\":\"CASH_IN\",\"amount\":2000,\"direction\":\"IN\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:46:12');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('218', NULL, 'CASH_MOVEMENT_RECORDED', 'cash_movements', '10', NULL, '{\"type\":\"CASH_OUT\",\"amount\":500,\"direction\":\"OUT\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:46:12');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('219', NULL, 'EXPENSE_CREATED', 'expenses', '3', NULL, '{\"title\":\"Emergency Plumbing Repair\",\"amount\":800}', '127.0.0.1', 'Unknown', '2026-09-09 06:46:12');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('220', NULL, 'CASH_MOVEMENT_RECORDED', 'cash_movements', '11', NULL, '{\"type\":\"CASH_EXPENSE\",\"amount\":800,\"direction\":\"OUT\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:46:12');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('221', NULL, 'EXPENSE_PAID', 'expenses', '3', NULL, '{\"amount\":\"800.00\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:46:12');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('222', NULL, 'SHIFT_CLOSED', 'shifts', '4', NULL, '{\"expected_cash\":10700,\"actual_cash\":10650,\"difference\":-50}', '127.0.0.1', 'Unknown', '2026-09-09 06:46:12');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('223', NULL, 'DAY_CLOSED', 'day_closings', '1', NULL, '{\"business_date\":\"2026-09-09\",\"net_sales\":15972,\"closed_by\":1}', '127.0.0.1', 'Unknown', '2026-09-09 06:46:12');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('224', '1', 'CUSTOMER_CREATED', 'customer', '1', NULL, '{\"code\":\"CUST-20260909-0001\",\"name\":\"Automated Test Customer\",\"phone\":\"01751790482\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:52:08');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('225', '1', 'CUSTOMER_UPDATED', 'customer', '1', NULL, '{\"name\":\"Automated Test Customer\",\"phone\":\"01751790482\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:52:08');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('226', '1', 'CUSTOMER_CREATED', 'customer', '2', NULL, '{\"code\":\"CUST-20260909-0002\",\"name\":\"Automated Test Customer\",\"phone\":\"01710273079\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:52:16');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('227', '1', 'CUSTOMER_UPDATED', 'customer', '2', NULL, '{\"name\":\"Automated Test Customer\",\"phone\":\"01710273079\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:52:16');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('228', '1', 'RESERVATION_CREATED', 'reservation', '1', NULL, '{\"reservation_number\":\"RES-20260909-0001\",\"table_id\":2,\"date\":\"2026-09-10\",\"time\":\"19:30:00\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:52:16');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('229', '1', 'DINING_SESSION_CREATED', 'DiningSessions', '1', NULL, '{\"table_number\":\"T-100\",\"guest_count\":2,\"opened_by\":1}', '127.0.0.1', 'Unknown', '2026-09-09 06:52:16');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('230', '1', 'RESERVATION_SEATED', 'reservation', '1', NULL, '{\"table_id\":2,\"dining_session_id\":null}', '127.0.0.1', 'Unknown', '2026-09-09 06:52:16');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('231', '1', 'CUSTOMER_CREATED', 'customer', '3', NULL, '{\"code\":\"CUST-20260909-0003\",\"name\":\"Automated Test Customer\",\"phone\":\"01732273268\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:52:41');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('232', '1', 'CUSTOMER_UPDATED', 'customer', '3', NULL, '{\"name\":\"Automated Test Customer\",\"phone\":\"01732273268\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:52:41');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('233', '1', 'CUSTOMER_CREATED', 'customer', '4', NULL, '{\"code\":\"CUST-20260909-0004\",\"name\":\"Automated Test Customer\",\"phone\":\"01782458939\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:52:51');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('234', '1', 'CUSTOMER_UPDATED', 'customer', '4', NULL, '{\"name\":\"Automated Test Customer\",\"phone\":\"01782458939\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:52:51');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('235', '1', 'RESERVATION_CREATED', 'reservation', '2', NULL, '{\"reservation_number\":\"RES-20260909-0002\",\"table_id\":2,\"date\":\"2026-10-05\",\"time\":\"19:30:00\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:52:51');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('236', '1', 'RESERVATION_SEATED', 'reservation', '2', NULL, '{\"table_id\":2,\"dining_session_id\":1}', '127.0.0.1', 'Unknown', '2026-09-09 06:52:51');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('237', '1', 'ORDER_CREATED', 'order', '36', NULL, '{\"order_number\":\"ORD-B1-20260909-0007\",\"order_type\":\"TAKEAWAY\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:52:51');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('238', '1', 'ORDER_ITEM_ADDED', 'order_item', '42', NULL, '{\"order_id\":36,\"item\":\"Chef Special Burger\",\"quantity\":2,\"subtotal\":24}', '127.0.0.1', 'Unknown', '2026-09-09 06:52:51');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('239', '1', 'ORDER_SUBMITTED', 'order', '36', NULL, '{\"order_number\":\"ORD-B1-20260909-0007\",\"total\":\"25.20\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:52:51');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('240', '1', 'ORDER_STATUS_CHANGED', 'order', '36', NULL, '{\"previous\":\"SUBMITTED\",\"new\":\"COMPLETED\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:52:51');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('241', '1', 'LOYALTY_EARNED', 'loyalty_account', '4', NULL, '{\"points\":2.4,\"new_balance\":2.4,\"order_id\":36}', '127.0.0.1', 'Unknown', '2026-09-09 06:52:51');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('242', '1', 'LOYALTY_ADJUSTED', 'loyalty_account', '4', NULL, '{\"adjustment\":50,\"new_balance\":52.4,\"reason\":\"Automated test bonus credit\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:52:51');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('243', '1', 'COUPON_CREATED', 'coupon', '1', NULL, '{\"code\":\"PROMO9058\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:52:51');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('244', '1', 'CUSTOMER_CREATED', 'customer', '5', NULL, '{\"code\":\"CUST-20260909-0005\",\"name\":\"Automated Test Customer\",\"phone\":\"01768430241\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:53:01');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('245', '1', 'CUSTOMER_UPDATED', 'customer', '5', NULL, '{\"name\":\"Automated Test Customer\",\"phone\":\"01768430241\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:53:01');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('246', '1', 'RESERVATION_CREATED', 'reservation', '3', NULL, '{\"reservation_number\":\"RES-20260909-0003\",\"table_id\":2,\"date\":\"2026-10-01\",\"time\":\"19:30:00\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:53:01');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('247', '1', 'RESERVATION_SEATED', 'reservation', '3', NULL, '{\"table_id\":2,\"dining_session_id\":1}', '127.0.0.1', 'Unknown', '2026-09-09 06:53:01');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('248', '1', 'ORDER_CREATED', 'order', '37', NULL, '{\"order_number\":\"ORD-B1-20260909-0008\",\"order_type\":\"TAKEAWAY\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:53:01');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('249', '1', 'ORDER_ITEM_ADDED', 'order_item', '43', NULL, '{\"order_id\":37,\"item\":\"Chef Special Burger\",\"quantity\":2,\"subtotal\":24}', '127.0.0.1', 'Unknown', '2026-09-09 06:53:01');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('250', '1', 'ORDER_SUBMITTED', 'order', '37', NULL, '{\"order_number\":\"ORD-B1-20260909-0008\",\"total\":\"25.20\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:53:01');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('251', '1', 'ORDER_STATUS_CHANGED', 'order', '37', NULL, '{\"previous\":\"SUBMITTED\",\"new\":\"COMPLETED\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:53:01');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('252', '1', 'LOYALTY_EARNED', 'loyalty_account', '5', NULL, '{\"points\":2.4,\"new_balance\":2.4,\"order_id\":37}', '127.0.0.1', 'Unknown', '2026-09-09 06:53:01');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('253', '1', 'LOYALTY_ADJUSTED', 'loyalty_account', '5', NULL, '{\"adjustment\":50,\"new_balance\":52.4,\"reason\":\"Automated test bonus credit\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:53:01');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('254', '1', 'COUPON_CREATED', 'coupon', '2', NULL, '{\"code\":\"PROMO8479\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:53:01');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('255', '1', 'CUSTOMER_CREATED', 'customer', '6', NULL, '{\"code\":\"CUST-20260909-0006\",\"name\":\"Automated Test Customer\",\"phone\":\"01788895267\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:53:19');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('256', '1', 'CUSTOMER_UPDATED', 'customer', '6', NULL, '{\"name\":\"Automated Test Customer\",\"phone\":\"01788895267\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:53:19');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('257', '1', 'RESERVATION_CREATED', 'reservation', '4', NULL, '{\"reservation_number\":\"RES-20260909-0004\",\"table_id\":2,\"date\":\"2026-10-10\",\"time\":\"19:30:00\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:53:19');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('258', '1', 'RESERVATION_SEATED', 'reservation', '4', NULL, '{\"table_id\":2,\"dining_session_id\":1}', '127.0.0.1', 'Unknown', '2026-09-09 06:53:19');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('259', '1', 'ORDER_CREATED', 'order', '38', NULL, '{\"order_number\":\"ORD-B1-20260909-0009\",\"order_type\":\"TAKEAWAY\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:53:19');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('260', '1', 'ORDER_ITEM_ADDED', 'order_item', '44', NULL, '{\"order_id\":38,\"item\":\"Chef Special Burger\",\"quantity\":2,\"subtotal\":24}', '127.0.0.1', 'Unknown', '2026-09-09 06:53:19');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('261', '1', 'ORDER_SUBMITTED', 'order', '38', NULL, '{\"order_number\":\"ORD-B1-20260909-0009\",\"total\":\"25.20\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:53:19');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('262', '1', 'ORDER_STATUS_CHANGED', 'order', '38', NULL, '{\"previous\":\"SUBMITTED\",\"new\":\"COMPLETED\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:53:19');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('263', '1', 'LOYALTY_EARNED', 'loyalty_account', '6', NULL, '{\"points\":2.4,\"new_balance\":2.4,\"order_id\":38}', '127.0.0.1', 'Unknown', '2026-09-09 06:53:19');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('264', '1', 'LOYALTY_ADJUSTED', 'loyalty_account', '6', NULL, '{\"adjustment\":50,\"new_balance\":52.4,\"reason\":\"Automated test bonus credit\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:53:19');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('265', '1', 'COUPON_CREATED', 'coupon', '3', NULL, '{\"code\":\"PROMO8585\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:53:19');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('266', '1', 'CUSTOMER_CREATED', 'customer', '7', NULL, '{\"code\":\"CUST-20260909-0007\",\"name\":\"Automated Test Customer\",\"phone\":\"01717756393\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:53:45');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('267', '1', 'CUSTOMER_UPDATED', 'customer', '7', NULL, '{\"name\":\"Automated Test Customer\",\"phone\":\"01717756393\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:53:45');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('268', '1', 'RESERVATION_CREATED', 'reservation', '5', NULL, '{\"reservation_number\":\"RES-20260909-0005\",\"table_id\":2,\"date\":\"2026-09-26\",\"time\":\"19:30:00\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:53:45');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('269', '1', 'RESERVATION_SEATED', 'reservation', '5', NULL, '{\"table_id\":2,\"dining_session_id\":1}', '127.0.0.1', 'Unknown', '2026-09-09 06:53:45');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('270', '1', 'ORDER_CREATED', 'order', '39', NULL, '{\"order_number\":\"ORD-B1-20260909-0010\",\"order_type\":\"TAKEAWAY\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:53:45');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('271', '1', 'ORDER_ITEM_ADDED', 'order_item', '45', NULL, '{\"order_id\":39,\"item\":\"Chef Special Burger\",\"quantity\":2,\"subtotal\":24}', '127.0.0.1', 'Unknown', '2026-09-09 06:53:45');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('272', '1', 'ORDER_SUBMITTED', 'order', '39', NULL, '{\"order_number\":\"ORD-B1-20260909-0010\",\"total\":\"25.20\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:53:45');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('273', '1', 'ORDER_STATUS_CHANGED', 'order', '39', NULL, '{\"previous\":\"SUBMITTED\",\"new\":\"COMPLETED\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:53:45');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('274', '1', 'LOYALTY_EARNED', 'loyalty_account', '7', NULL, '{\"points\":2.4,\"new_balance\":2.4,\"order_id\":39}', '127.0.0.1', 'Unknown', '2026-09-09 06:53:45');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('275', '1', 'LOYALTY_ADJUSTED', 'loyalty_account', '7', NULL, '{\"adjustment\":50,\"new_balance\":52.4,\"reason\":\"Automated test bonus credit\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:53:45');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('276', '1', 'COUPON_CREATED', 'coupon', '4', NULL, '{\"code\":\"PROMO9285\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:53:45');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('277', '1', 'CUSTOMER_CREATED', 'customer', '8', NULL, '{\"code\":\"CUST-20260909-0008\",\"name\":\"QR Table Guest\",\"phone\":\"01955834768\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:53:45');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('278', '1', 'ORDER_CREATED', 'order', '40', NULL, '{\"order_number\":\"ORD-B1-20260909-0011\",\"order_type\":\"DINE_IN\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:53:45');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('279', '1', 'ORDER_ITEM_ADDED', 'order_item', '46', NULL, '{\"order_id\":40,\"item\":\"Chef Special Burger\",\"quantity\":1,\"subtotal\":12}', '127.0.0.1', 'Unknown', '2026-09-09 06:53:45');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('280', '1', 'ORDER_SUBMITTED', 'order', '40', NULL, '{\"order_number\":\"ORD-B1-20260909-0011\",\"total\":\"12.60\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:53:45');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('281', '1', 'CUSTOMER_CREATED', 'customer', '9', NULL, '{\"code\":\"CUST-20260909-0009\",\"name\":\"Automated Test Customer\",\"phone\":\"01739881020\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:53:54');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('282', '1', 'CUSTOMER_UPDATED', 'customer', '9', NULL, '{\"name\":\"Automated Test Customer\",\"phone\":\"01739881020\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:53:54');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('283', '1', 'RESERVATION_CREATED', 'reservation', '6', NULL, '{\"reservation_number\":\"RES-20260909-0006\",\"table_id\":2,\"date\":\"2026-10-19\",\"time\":\"19:30:00\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:53:54');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('284', '1', 'RESERVATION_SEATED', 'reservation', '6', NULL, '{\"table_id\":2,\"dining_session_id\":1}', '127.0.0.1', 'Unknown', '2026-09-09 06:53:54');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('285', '1', 'ORDER_CREATED', 'order', '41', NULL, '{\"order_number\":\"ORD-B1-20260909-0012\",\"order_type\":\"TAKEAWAY\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:53:54');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('286', '1', 'ORDER_ITEM_ADDED', 'order_item', '47', NULL, '{\"order_id\":41,\"item\":\"Chef Special Burger\",\"quantity\":2,\"subtotal\":24}', '127.0.0.1', 'Unknown', '2026-09-09 06:53:54');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('287', '1', 'ORDER_SUBMITTED', 'order', '41', NULL, '{\"order_number\":\"ORD-B1-20260909-0012\",\"total\":\"25.20\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:53:54');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('288', '1', 'ORDER_STATUS_CHANGED', 'order', '41', NULL, '{\"previous\":\"SUBMITTED\",\"new\":\"COMPLETED\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:53:54');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('289', '1', 'LOYALTY_EARNED', 'loyalty_account', '9', NULL, '{\"points\":2.4,\"new_balance\":2.4,\"order_id\":41}', '127.0.0.1', 'Unknown', '2026-09-09 06:53:54');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('290', '1', 'LOYALTY_ADJUSTED', 'loyalty_account', '9', NULL, '{\"adjustment\":50,\"new_balance\":52.4,\"reason\":\"Automated test bonus credit\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:53:54');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('291', '1', 'COUPON_CREATED', 'coupon', '5', NULL, '{\"code\":\"PROMO5011\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:53:54');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('292', '1', 'CUSTOMER_CREATED', 'customer', '10', NULL, '{\"code\":\"CUST-20260909-0010\",\"name\":\"QR Table Guest\",\"phone\":\"01982640758\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:53:54');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('293', '1', 'ORDER_CREATED', 'order', '42', NULL, '{\"order_number\":\"ORD-B1-20260909-0013\",\"order_type\":\"DINE_IN\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:53:54');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('294', '1', 'ORDER_ITEM_ADDED', 'order_item', '48', NULL, '{\"order_id\":42,\"item\":\"Chef Special Burger\",\"quantity\":1,\"subtotal\":12}', '127.0.0.1', 'Unknown', '2026-09-09 06:53:54');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('295', '1', 'ORDER_SUBMITTED', 'order', '42', NULL, '{\"order_number\":\"ORD-B1-20260909-0013\",\"total\":\"12.60\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:53:54');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('296', '1', 'ROUTE_CREATED', 'orders', '42', NULL, '\"Order #ORD-B1-20260909-0013 routed to 1 station(s).\"', '127.0.0.1', 'Unknown', '2026-09-09 06:53:54');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('297', '1', 'QR_ORDER_SUBMITTED', 'order', '42', NULL, '{\"token\":\"b83779d3990fa76fd80a58d150f2fc03dd95442203c41e5c4c108a609406aa93\",\"table_number\":\"T-100\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:53:54');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('298', '1', 'CUSTOMER_CREATED', 'customer', '11', NULL, '{\"code\":\"CUST-20260909-0011\",\"name\":\"Automated Test Customer\",\"phone\":\"01775276099\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:54:03');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('299', '1', 'CUSTOMER_UPDATED', 'customer', '11', NULL, '{\"name\":\"Automated Test Customer\",\"phone\":\"01775276099\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:54:03');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('300', '1', 'RESERVATION_CREATED', 'reservation', '7', NULL, '{\"reservation_number\":\"RES-20260909-0007\",\"table_id\":2,\"date\":\"2026-10-28\",\"time\":\"19:30:00\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:54:03');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('301', '1', 'RESERVATION_SEATED', 'reservation', '7', NULL, '{\"table_id\":2,\"dining_session_id\":1}', '127.0.0.1', 'Unknown', '2026-09-09 06:54:03');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('302', '1', 'ORDER_CREATED', 'order', '43', NULL, '{\"order_number\":\"ORD-B1-20260909-0014\",\"order_type\":\"TAKEAWAY\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:54:03');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('303', '1', 'ORDER_ITEM_ADDED', 'order_item', '49', NULL, '{\"order_id\":43,\"item\":\"Chef Special Burger\",\"quantity\":2,\"subtotal\":24}', '127.0.0.1', 'Unknown', '2026-09-09 06:54:03');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('304', '1', 'ORDER_SUBMITTED', 'order', '43', NULL, '{\"order_number\":\"ORD-B1-20260909-0014\",\"total\":\"25.20\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:54:03');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('305', '1', 'ORDER_STATUS_CHANGED', 'order', '43', NULL, '{\"previous\":\"SUBMITTED\",\"new\":\"COMPLETED\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:54:03');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('306', '1', 'LOYALTY_EARNED', 'loyalty_account', '11', NULL, '{\"points\":2.4,\"new_balance\":2.4,\"order_id\":43}', '127.0.0.1', 'Unknown', '2026-09-09 06:54:03');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('307', '1', 'LOYALTY_ADJUSTED', 'loyalty_account', '11', NULL, '{\"adjustment\":50,\"new_balance\":52.4,\"reason\":\"Automated test bonus credit\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:54:03');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('308', '1', 'COUPON_CREATED', 'coupon', '6', NULL, '{\"code\":\"PROMO1662\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:54:03');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('309', '1', 'CUSTOMER_CREATED', 'customer', '12', NULL, '{\"code\":\"CUST-20260909-0012\",\"name\":\"QR Table Guest\",\"phone\":\"01947574438\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:54:03');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('310', '1', 'ORDER_CREATED', 'order', '44', NULL, '{\"order_number\":\"ORD-B1-20260909-0015\",\"order_type\":\"DINE_IN\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:54:03');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('311', '1', 'ORDER_ITEM_ADDED', 'order_item', '50', NULL, '{\"order_id\":44,\"item\":\"Chef Special Burger\",\"quantity\":1,\"subtotal\":12}', '127.0.0.1', 'Unknown', '2026-09-09 06:54:03');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('312', '1', 'ORDER_SUBMITTED', 'order', '44', NULL, '{\"order_number\":\"ORD-B1-20260909-0015\",\"total\":\"12.60\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:54:03');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('313', '1', 'ROUTE_CREATED', 'orders', '44', NULL, '\"Order #ORD-B1-20260909-0015 routed to 1 station(s).\"', '127.0.0.1', 'Unknown', '2026-09-09 06:54:03');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES ('314', '1', 'QR_ORDER_SUBMITTED', 'order', '44', NULL, '{\"token\":\"b83779d3990fa76fd80a58d150f2fc03dd95442203c41e5c4c108a609406aa93\",\"table_number\":\"T-100\"}', '127.0.0.1', 'Unknown', '2026-09-09 06:54:03');

-- Table structure for `branches`
DROP TABLE IF EXISTS `branches`;
CREATE TABLE `branches` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `code` varchar(30) NOT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `status` enum('ACTIVE','INACTIVE') DEFAULT 'ACTIVE',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for `branches`
INSERT INTO `branches` (`id`, `name`, `code`, `address`, `phone`, `status`, `created_at`) VALUES ('1', 'Main Outlet', 'BR-MAIN', NULL, NULL, 'ACTIVE', '2026-09-09 05:40:30');

-- Table structure for `cash_drawers`
DROP TABLE IF EXISTS `cash_drawers`;
CREATE TABLE `cash_drawers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `branch_id` int(11) NOT NULL DEFAULT 1,
  `name` varchar(100) NOT NULL,
  `code` varchar(30) NOT NULL,
  `location` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_drawer_branch_code` (`branch_id`,`code`),
  KEY `idx_drawers_branch` (`branch_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `cash_drawers`
INSERT INTO `cash_drawers` (`id`, `branch_id`, `name`, `code`, `location`, `is_active`, `created_at`, `updated_at`) VALUES ('1', '1', 'Main POS Register Cash Drawer', 'CDR-01', 'Front Counter POS 1', '1', '2026-09-09 06:43:11', '2026-09-09 06:43:11');

-- Table structure for `cash_movements`
DROP TABLE IF EXISTS `cash_movements`;
CREATE TABLE `cash_movements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `branch_id` int(11) NOT NULL DEFAULT 1,
  `shift_id` int(11) DEFAULT NULL,
  `cash_drawer_id` int(11) DEFAULT 1,
  `movement_type` enum('OPENING_BALANCE','CASH_SALE','CASH_REFUND','CASH_EXPENSE','CASH_IN','CASH_OUT','CASH_TRANSFER','CASH_ADJUSTMENT','CLOSING_BALANCE') NOT NULL,
  `amount` decimal(14,2) NOT NULL,
  `direction` enum('IN','OUT') NOT NULL,
  `reference_type` varchar(50) DEFAULT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_cm_branch_shift` (`branch_id`,`shift_id`),
  KEY `idx_cm_drawer` (`cash_drawer_id`),
  KEY `idx_cm_type_created` (`movement_type`,`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `cash_movements`
INSERT INTO `cash_movements` (`id`, `branch_id`, `shift_id`, `cash_drawer_id`, `movement_type`, `amount`, `direction`, `reference_type`, `reference_id`, `user_id`, `reason`, `notes`, `created_at`) VALUES ('1', '1', '2', '1', 'OPENING_BALANCE', '10000.00', 'IN', 'shifts', '2', '1', 'Opening shift cash balance', NULL, '2026-09-09 06:45:46');
INSERT INTO `cash_movements` (`id`, `branch_id`, `shift_id`, `cash_drawer_id`, `movement_type`, `amount`, `direction`, `reference_type`, `reference_id`, `user_id`, `reason`, `notes`, `created_at`) VALUES ('2', '1', '2', '1', 'CASH_IN', '2000.00', 'IN', NULL, NULL, '1', 'Petty Cash Added for Register', '', '2026-09-09 06:45:46');
INSERT INTO `cash_movements` (`id`, `branch_id`, `shift_id`, `cash_drawer_id`, `movement_type`, `amount`, `direction`, `reference_type`, `reference_id`, `user_id`, `reason`, `notes`, `created_at`) VALUES ('3', '1', '2', '1', 'CASH_OUT', '500.00', 'OUT', NULL, NULL, '1', 'Change Disbursed to Register 2', '', '2026-09-09 06:45:46');
INSERT INTO `cash_movements` (`id`, `branch_id`, `shift_id`, `cash_drawer_id`, `movement_type`, `amount`, `direction`, `reference_type`, `reference_id`, `user_id`, `reason`, `notes`, `created_at`) VALUES ('4', '1', '3', '1', 'OPENING_BALANCE', '10000.00', 'IN', 'shifts', '3', '1', 'Opening shift cash balance', NULL, '2026-09-09 06:46:02');
INSERT INTO `cash_movements` (`id`, `branch_id`, `shift_id`, `cash_drawer_id`, `movement_type`, `amount`, `direction`, `reference_type`, `reference_id`, `user_id`, `reason`, `notes`, `created_at`) VALUES ('5', '1', '3', '1', 'CASH_IN', '2000.00', 'IN', NULL, NULL, '1', 'Petty Cash Added for Register', '', '2026-09-09 06:46:02');
INSERT INTO `cash_movements` (`id`, `branch_id`, `shift_id`, `cash_drawer_id`, `movement_type`, `amount`, `direction`, `reference_type`, `reference_id`, `user_id`, `reason`, `notes`, `created_at`) VALUES ('6', '1', '3', '1', 'CASH_OUT', '500.00', 'OUT', NULL, NULL, '1', 'Change Disbursed to Register 2', '', '2026-09-09 06:46:02');
INSERT INTO `cash_movements` (`id`, `branch_id`, `shift_id`, `cash_drawer_id`, `movement_type`, `amount`, `direction`, `reference_type`, `reference_id`, `user_id`, `reason`, `notes`, `created_at`) VALUES ('7', '1', '3', '1', 'CASH_EXPENSE', '800.00', 'OUT', NULL, NULL, '1', 'Paid Expense: Emergency Plumbing Repair', 'Expense #2', '2026-09-09 06:46:02');
INSERT INTO `cash_movements` (`id`, `branch_id`, `shift_id`, `cash_drawer_id`, `movement_type`, `amount`, `direction`, `reference_type`, `reference_id`, `user_id`, `reason`, `notes`, `created_at`) VALUES ('8', '1', '4', '1', 'OPENING_BALANCE', '10000.00', 'IN', 'shifts', '4', '1', 'Opening shift cash balance', NULL, '2026-09-09 06:46:12');
INSERT INTO `cash_movements` (`id`, `branch_id`, `shift_id`, `cash_drawer_id`, `movement_type`, `amount`, `direction`, `reference_type`, `reference_id`, `user_id`, `reason`, `notes`, `created_at`) VALUES ('9', '1', '4', '1', 'CASH_IN', '2000.00', 'IN', NULL, NULL, '1', 'Petty Cash Added for Register', '', '2026-09-09 06:46:12');
INSERT INTO `cash_movements` (`id`, `branch_id`, `shift_id`, `cash_drawer_id`, `movement_type`, `amount`, `direction`, `reference_type`, `reference_id`, `user_id`, `reason`, `notes`, `created_at`) VALUES ('10', '1', '4', '1', 'CASH_OUT', '500.00', 'OUT', NULL, NULL, '1', 'Change Disbursed to Register 2', '', '2026-09-09 06:46:12');
INSERT INTO `cash_movements` (`id`, `branch_id`, `shift_id`, `cash_drawer_id`, `movement_type`, `amount`, `direction`, `reference_type`, `reference_id`, `user_id`, `reason`, `notes`, `created_at`) VALUES ('11', '1', '4', '1', 'CASH_EXPENSE', '800.00', 'OUT', NULL, NULL, '1', 'Paid Expense: Emergency Plumbing Repair', 'Expense #3', '2026-09-09 06:46:12');
INSERT INTO `cash_movements` (`id`, `branch_id`, `shift_id`, `cash_drawer_id`, `movement_type`, `amount`, `direction`, `reference_type`, `reference_id`, `user_id`, `reason`, `notes`, `created_at`) VALUES ('12', '1', '4', '1', 'CLOSING_BALANCE', '10650.00', 'OUT', 'shifts', '4', '1', 'Shift closing physical cash count', '{\"1000\":10,\"500\":1,\"100\":1,\"50\":1}', '2026-09-09 06:46:12');

-- Table structure for `categories`
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `menu_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(80) NOT NULL,
  `slug` varchar(80) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `status` enum('ACTIVE','INACTIVE') DEFAULT 'ACTIVE',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for `categories`
INSERT INTO `categories` (`id`, `menu_id`, `name`, `slug`, `description`, `sort_order`, `status`, `updated_at`, `deleted_at`) VALUES ('1', NULL, 'Main Dishes', 'main-dishes', NULL, '0', 'ACTIVE', '2026-09-09 05:40:50', NULL);

-- Table structure for `commission_adjustments`
DROP TABLE IF EXISTS `commission_adjustments`;
CREATE TABLE `commission_adjustments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `commission_transaction_id` int(10) unsigned NOT NULL,
  `refund_id` int(10) unsigned DEFAULT NULL,
  `reason` varchar(255) NOT NULL,
  `adjustment_amount` decimal(12,2) NOT NULL,
  `adjustment_type` enum('REFUND_REVERSAL','MANUAL_CORRECTION') DEFAULT 'REFUND_REVERSAL',
  `created_by` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `commission_transaction_id` (`commission_transaction_id`),
  CONSTRAINT `commission_adjustments_ibfk_1` FOREIGN KEY (`commission_transaction_id`) REFERENCES `commission_transactions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `commission_adjustments`
INSERT INTO `commission_adjustments` (`id`, `commission_transaction_id`, `refund_id`, `reason`, `adjustment_amount`, `adjustment_type`, `created_by`, `created_at`) VALUES ('1', '1', '2', 'Customer refund #REF-20260908-711279 adjustment (-৳25.00)', '-25.00', 'REFUND_REVERSAL', '1', '2026-09-09 05:51:21');
INSERT INTO `commission_adjustments` (`id`, `commission_transaction_id`, `refund_id`, `reason`, `adjustment_amount`, `adjustment_type`, `created_by`, `created_at`) VALUES ('2', '2', '3', 'Customer refund #REF-20260908-238118 adjustment (-৳25.00)', '-25.00', 'REFUND_REVERSAL', '1', '2026-09-09 05:51:31');
INSERT INTO `commission_adjustments` (`id`, `commission_transaction_id`, `refund_id`, `reason`, `adjustment_amount`, `adjustment_type`, `created_by`, `created_at`) VALUES ('3', '3', '4', 'Customer refund #REF-20260908-713589 adjustment (-৳25.00)', '-25.00', 'REFUND_REVERSAL', '1', '2026-09-09 05:51:40');
INSERT INTO `commission_adjustments` (`id`, `commission_transaction_id`, `refund_id`, `reason`, `adjustment_amount`, `adjustment_type`, `created_by`, `created_at`) VALUES ('4', '4', '5', 'Customer refund #REF-20260908-126456 adjustment (-৳25.00)', '-25.00', 'REFUND_REVERSAL', '1', '2026-09-09 05:51:48');

-- Table structure for `commission_payout_items`
DROP TABLE IF EXISTS `commission_payout_items`;
CREATE TABLE `commission_payout_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `payout_id` int(10) unsigned NOT NULL,
  `commission_transaction_id` int(10) unsigned NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `payout_id` (`payout_id`),
  KEY `commission_transaction_id` (`commission_transaction_id`),
  CONSTRAINT `commission_payout_items_ibfk_1` FOREIGN KEY (`payout_id`) REFERENCES `commission_payouts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `commission_payout_items_ibfk_2` FOREIGN KEY (`commission_transaction_id`) REFERENCES `commission_transactions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `commission_payout_items`
INSERT INTO `commission_payout_items` (`id`, `payout_id`, `commission_transaction_id`, `amount`, `created_at`) VALUES ('1', '1', '2', '25.00', '2026-09-09 05:51:31');
INSERT INTO `commission_payout_items` (`id`, `payout_id`, `commission_transaction_id`, `amount`, `created_at`) VALUES ('2', '2', '3', '25.00', '2026-09-09 05:51:40');
INSERT INTO `commission_payout_items` (`id`, `payout_id`, `commission_transaction_id`, `amount`, `created_at`) VALUES ('3', '3', '4', '25.00', '2026-09-09 05:51:48');

-- Table structure for `commission_payouts`
DROP TABLE IF EXISTS `commission_payouts`;
CREATE TABLE `commission_payouts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `payout_number` varchar(40) NOT NULL,
  `branch_id` int(10) unsigned NOT NULL DEFAULT 1,
  `waiter_id` int(10) unsigned NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `payment_method` enum('CASH','BANK_TRANSFER','MOBILE_WALLET','OTHER') DEFAULT 'CASH',
  `reference_number` varchar(100) DEFAULT NULL,
  `period_start` date DEFAULT NULL,
  `period_end` date DEFAULT NULL,
  `status` enum('PENDING','PROCESSED','CANCELLED') DEFAULT 'PROCESSED',
  `processed_by_user_id` int(10) unsigned NOT NULL,
  `processed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `payout_number` (`payout_number`),
  KEY `processed_by_user_id` (`processed_by_user_id`),
  KEY `idx_payout_waiter_date` (`waiter_id`,`created_at`),
  CONSTRAINT `commission_payouts_ibfk_1` FOREIGN KEY (`waiter_id`) REFERENCES `users` (`id`),
  CONSTRAINT `commission_payouts_ibfk_2` FOREIGN KEY (`processed_by_user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `commission_payouts`
INSERT INTO `commission_payouts` (`id`, `payout_number`, `branch_id`, `waiter_id`, `amount`, `payment_method`, `reference_number`, `period_start`, `period_end`, `status`, `processed_by_user_id`, `processed_at`, `notes`, `created_at`) VALUES ('1', 'PAYOUT-20260908-164055', '1', '1', '25.00', 'CASH', 'PAYOUT-REF-100', '2026-09-09', '2026-09-09', 'PROCESSED', '1', '2026-09-09 05:51:31', 'Weekly waiter commission payout test', '2026-09-09 05:51:31');
INSERT INTO `commission_payouts` (`id`, `payout_number`, `branch_id`, `waiter_id`, `amount`, `payment_method`, `reference_number`, `period_start`, `period_end`, `status`, `processed_by_user_id`, `processed_at`, `notes`, `created_at`) VALUES ('2', 'PAYOUT-20260908-959142', '1', '1', '25.00', 'CASH', 'PAYOUT-REF-100', '2026-09-09', '2026-09-09', 'PROCESSED', '1', '2026-09-09 05:51:40', 'Weekly waiter commission payout test', '2026-09-09 05:51:40');
INSERT INTO `commission_payouts` (`id`, `payout_number`, `branch_id`, `waiter_id`, `amount`, `payment_method`, `reference_number`, `period_start`, `period_end`, `status`, `processed_by_user_id`, `processed_at`, `notes`, `created_at`) VALUES ('3', 'PAYOUT-20260908-554750', '1', '1', '25.00', 'CASH', 'PAYOUT-REF-100', '2026-09-09', '2026-09-09', 'PROCESSED', '1', '2026-09-09 05:51:48', 'Weekly waiter commission payout test', '2026-09-09 05:51:48');

-- Table structure for `commission_rules`
DROP TABLE IF EXISTS `commission_rules`;
CREATE TABLE `commission_rules` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` int(10) unsigned NOT NULL DEFAULT 1,
  `name` varchar(80) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `calculation_base` enum('PERCENTAGE','PER_ORDER','PER_ITEM','SALES_AMOUNT') DEFAULT 'PERCENTAGE',
  `rate` decimal(8,2) NOT NULL DEFAULT 5.00,
  `fixed_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `minimum_sales` decimal(12,2) NOT NULL DEFAULT 0.00,
  `maximum_commission` decimal(12,2) DEFAULT NULL,
  `commission_eligible` enum('ALL','PAID_ONLY','CONFIRMED_ONLY') DEFAULT 'PAID_ONLY',
  `is_active` tinyint(1) DEFAULT 1,
  `effective_from` datetime DEFAULT NULL,
  `effective_to` datetime DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for `commission_rules`
INSERT INTO `commission_rules` (`id`, `branch_id`, `name`, `code`, `calculation_base`, `rate`, `fixed_amount`, `minimum_sales`, `maximum_commission`, `commission_eligible`, `is_active`, `effective_from`, `effective_to`, `created_by`, `created_at`, `updated_at`) VALUES ('1', '1', 'Default Waiter 5% Net Sales Commission', 'COMM-RULE-5PCT', 'PERCENTAGE', '5.00', '0.00', '0.00', NULL, 'PAID_ONLY', '1', NULL, NULL, NULL, '2026-09-09 05:48:15', '2026-09-09 05:48:15');

-- Table structure for `commission_transactions`
DROP TABLE IF EXISTS `commission_transactions`;
CREATE TABLE `commission_transactions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` int(10) unsigned NOT NULL DEFAULT 1,
  `order_id` int(10) unsigned NOT NULL,
  `order_item_id` int(10) unsigned DEFAULT NULL,
  `waiter_id` int(10) unsigned NOT NULL,
  `commission_rule_id` int(10) unsigned DEFAULT NULL,
  `base_amount` decimal(12,2) NOT NULL,
  `rate` decimal(8,2) NOT NULL,
  `fixed_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `commission_amount` decimal(12,2) NOT NULL,
  `status` enum('PENDING','APPROVED','PAID','REJECTED','REVERSED','ADJUSTED') DEFAULT 'PENDING',
  `reason` varchar(255) DEFAULT NULL,
  `approved_by_user_id` int(10) unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `paid_by` int(10) unsigned DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `idempotency_key` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_comm_order_waiter` (`order_id`,`waiter_id`),
  KEY `idx_comm_waiter_id` (`waiter_id`),
  KEY `idx_comm_order_id` (`order_id`),
  KEY `idx_comm_waiter_status` (`waiter_id`,`status`,`created_at`),
  KEY `idx_comm_branch_created` (`branch_id`,`created_at`),
  CONSTRAINT `commission_transactions_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  CONSTRAINT `commission_transactions_ibfk_2` FOREIGN KEY (`waiter_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for `commission_transactions`
INSERT INTO `commission_transactions` (`id`, `branch_id`, `order_id`, `order_item_id`, `waiter_id`, `commission_rule_id`, `base_amount`, `rate`, `fixed_amount`, `commission_amount`, `status`, `reason`, `approved_by_user_id`, `approved_at`, `paid_by`, `paid_at`, `idempotency_key`, `created_at`, `updated_at`) VALUES ('1', '1', '17', NULL, '1', '1', '1000.00', '5.00', '0.00', '25.00', 'ADJUSTED', 'Customer refund #REF-20260908-711279 adjustment (-৳25.00)', NULL, NULL, NULL, NULL, 'COMM-ORD-17-WTR-1', '2026-09-09 05:51:21', '2026-09-09 05:51:21');
INSERT INTO `commission_transactions` (`id`, `branch_id`, `order_id`, `order_item_id`, `waiter_id`, `commission_rule_id`, `base_amount`, `rate`, `fixed_amount`, `commission_amount`, `status`, `reason`, `approved_by_user_id`, `approved_at`, `paid_by`, `paid_at`, `idempotency_key`, `created_at`, `updated_at`) VALUES ('2', '1', '18', NULL, '1', '1', '1000.00', '5.00', '0.00', '25.00', 'PAID', 'Customer refund #REF-20260908-238118 adjustment (-৳25.00)', '1', '2026-09-09 05:51:31', '1', '2026-09-09 05:51:31', 'COMM-ORD-18-WTR-1', '2026-09-09 05:51:31', '2026-09-09 05:51:31');
INSERT INTO `commission_transactions` (`id`, `branch_id`, `order_id`, `order_item_id`, `waiter_id`, `commission_rule_id`, `base_amount`, `rate`, `fixed_amount`, `commission_amount`, `status`, `reason`, `approved_by_user_id`, `approved_at`, `paid_by`, `paid_at`, `idempotency_key`, `created_at`, `updated_at`) VALUES ('3', '1', '19', NULL, '1', '1', '1000.00', '5.00', '0.00', '25.00', 'PAID', 'Customer refund #REF-20260908-713589 adjustment (-৳25.00)', '1', '2026-09-09 05:51:40', '1', '2026-09-09 05:51:40', 'COMM-ORD-19-WTR-1', '2026-09-09 05:51:40', '2026-09-09 05:51:40');
INSERT INTO `commission_transactions` (`id`, `branch_id`, `order_id`, `order_item_id`, `waiter_id`, `commission_rule_id`, `base_amount`, `rate`, `fixed_amount`, `commission_amount`, `status`, `reason`, `approved_by_user_id`, `approved_at`, `paid_by`, `paid_at`, `idempotency_key`, `created_at`, `updated_at`) VALUES ('4', '1', '20', NULL, '1', '1', '1000.00', '5.00', '0.00', '25.00', 'PAID', 'Customer refund #REF-20260908-126456 adjustment (-৳25.00)', '1', '2026-09-09 05:51:48', '1', '2026-09-09 05:51:48', 'COMM-ORD-20-WTR-1', '2026-09-09 05:51:48', '2026-09-09 05:51:48');

-- Table structure for `coupon_usage`
DROP TABLE IF EXISTS `coupon_usage`;
CREATE TABLE `coupon_usage` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `coupon_id` int(10) unsigned NOT NULL,
  `order_id` int(10) unsigned NOT NULL,
  `discount_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `customer_id` int(10) unsigned DEFAULT NULL,
  `redeemed_by` int(10) unsigned DEFAULT NULL,
  `used_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_usage_coupon` (`coupon_id`),
  KEY `idx_usage_customer` (`customer_id`),
  KEY `idx_usage_order` (`order_id`),
  CONSTRAINT `coupon_usage_ibfk_1` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE CASCADE,
  CONSTRAINT `coupon_usage_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for `coupons`
DROP TABLE IF EXISTS `coupons`;
CREATE TABLE `coupons` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` int(10) unsigned DEFAULT NULL,
  `code` varchar(30) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `discount_type` enum('PERCENTAGE','FIXED') DEFAULT 'PERCENTAGE',
  `discount_amount` decimal(12,2) NOT NULL,
  `min_order_amount` decimal(12,2) DEFAULT 0.00,
  `max_discount` decimal(12,2) DEFAULT 0.00,
  `usage_limit` int(11) DEFAULT 0,
  `per_customer_limit` int(11) DEFAULT 0,
  `valid_from` datetime NOT NULL,
  `valid_until` datetime NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `idx_coupon_branch` (`branch_id`),
  KEY `idx_coupon_code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for `coupons`
INSERT INTO `coupons` (`id`, `branch_id`, `code`, `name`, `description`, `discount_type`, `discount_amount`, `min_order_amount`, `max_discount`, `usage_limit`, `per_customer_limit`, `valid_from`, `valid_until`, `is_active`, `created_at`, `updated_at`) VALUES ('1', NULL, 'PROMO9058', '15% Off Test Coupon', NULL, 'PERCENTAGE', '15.00', '20.00', '0.00', '50', '0', '2026-09-09 00:52:51', '2027-09-09 00:52:51', '1', '2026-09-09 06:52:51', NULL);
INSERT INTO `coupons` (`id`, `branch_id`, `code`, `name`, `description`, `discount_type`, `discount_amount`, `min_order_amount`, `max_discount`, `usage_limit`, `per_customer_limit`, `valid_from`, `valid_until`, `is_active`, `created_at`, `updated_at`) VALUES ('2', NULL, 'PROMO8479', '15% Off Test Coupon', NULL, 'PERCENTAGE', '15.00', '20.00', '0.00', '50', '0', '2026-09-09 00:53:01', '2027-09-09 00:53:01', '1', '2026-09-09 06:53:01', NULL);
INSERT INTO `coupons` (`id`, `branch_id`, `code`, `name`, `description`, `discount_type`, `discount_amount`, `min_order_amount`, `max_discount`, `usage_limit`, `per_customer_limit`, `valid_from`, `valid_until`, `is_active`, `created_at`, `updated_at`) VALUES ('3', NULL, 'PROMO8585', '15% Off Test Coupon', NULL, 'PERCENTAGE', '15.00', '20.00', '0.00', '50', '0', '2026-09-09 00:53:19', '2027-09-09 00:53:19', '1', '2026-09-09 06:53:19', NULL);
INSERT INTO `coupons` (`id`, `branch_id`, `code`, `name`, `description`, `discount_type`, `discount_amount`, `min_order_amount`, `max_discount`, `usage_limit`, `per_customer_limit`, `valid_from`, `valid_until`, `is_active`, `created_at`, `updated_at`) VALUES ('4', NULL, 'PROMO9285', '15% Off Test Coupon', NULL, 'PERCENTAGE', '15.00', '20.00', '0.00', '50', '0', '2026-09-09 00:53:45', '2027-09-09 00:53:45', '1', '2026-09-09 06:53:45', NULL);
INSERT INTO `coupons` (`id`, `branch_id`, `code`, `name`, `description`, `discount_type`, `discount_amount`, `min_order_amount`, `max_discount`, `usage_limit`, `per_customer_limit`, `valid_from`, `valid_until`, `is_active`, `created_at`, `updated_at`) VALUES ('5', NULL, 'PROMO5011', '15% Off Test Coupon', NULL, 'PERCENTAGE', '15.00', '20.00', '0.00', '50', '0', '2026-09-09 00:53:54', '2027-09-09 00:53:54', '1', '2026-09-09 06:53:54', NULL);
INSERT INTO `coupons` (`id`, `branch_id`, `code`, `name`, `description`, `discount_type`, `discount_amount`, `min_order_amount`, `max_discount`, `usage_limit`, `per_customer_limit`, `valid_from`, `valid_until`, `is_active`, `created_at`, `updated_at`) VALUES ('6', NULL, 'PROMO1662', '15% Off Test Coupon', NULL, 'PERCENTAGE', '15.00', '20.00', '0.00', '50', '0', '2026-09-09 00:54:03', '2027-09-09 00:54:03', '1', '2026-09-09 06:54:03', NULL);

-- Table structure for `customers`
DROP TABLE IF EXISTS `customers`;
CREATE TABLE `customers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` int(10) unsigned DEFAULT NULL,
  `customer_code` varchar(50) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `phone` varchar(30) NOT NULL,
  `email` varchar(120) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('MALE','FEMALE','OTHER','UNDISCLOSED') DEFAULT 'UNDISCLOSED',
  `company_name` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `marketing_opt_in` tinyint(1) DEFAULT 1,
  `status` enum('ACTIVE','INACTIVE') DEFAULT 'ACTIVE',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `phone` (`phone`),
  UNIQUE KEY `idx_customer_code` (`customer_code`),
  KEY `idx_customer_branch` (`branch_id`),
  KEY `idx_customer_phone` (`phone`),
  KEY `idx_customer_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for `customers`
INSERT INTO `customers` (`id`, `branch_id`, `customer_code`, `name`, `first_name`, `last_name`, `phone`, `email`, `date_of_birth`, `gender`, `company_name`, `notes`, `marketing_opt_in`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES ('1', '1', 'CUST-20260909-0001', 'Automated Test Customer', NULL, NULL, '01751790482', 'test_cust_304@smartresta.com', NULL, 'MALE', 'SMARTRESTA Labs', 'Updated notes for VIP test customer', '1', 'ACTIVE', '2026-09-09 06:52:08', '2026-09-09 06:52:08', NULL);
INSERT INTO `customers` (`id`, `branch_id`, `customer_code`, `name`, `first_name`, `last_name`, `phone`, `email`, `date_of_birth`, `gender`, `company_name`, `notes`, `marketing_opt_in`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES ('2', '1', 'CUST-20260909-0002', 'Automated Test Customer', NULL, NULL, '01710273079', 'test_cust_632@smartresta.com', NULL, 'MALE', 'SMARTRESTA Labs', 'Updated notes for VIP test customer', '1', 'ACTIVE', '2026-09-09 06:52:16', '2026-09-09 06:52:16', NULL);
INSERT INTO `customers` (`id`, `branch_id`, `customer_code`, `name`, `first_name`, `last_name`, `phone`, `email`, `date_of_birth`, `gender`, `company_name`, `notes`, `marketing_opt_in`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES ('3', '1', 'CUST-20260909-0003', 'Automated Test Customer', NULL, NULL, '01732273268', 'test_cust_343@smartresta.com', NULL, 'MALE', 'SMARTRESTA Labs', 'Updated notes for VIP test customer', '1', 'ACTIVE', '2026-09-09 06:52:41', '2026-09-09 06:52:41', NULL);
INSERT INTO `customers` (`id`, `branch_id`, `customer_code`, `name`, `first_name`, `last_name`, `phone`, `email`, `date_of_birth`, `gender`, `company_name`, `notes`, `marketing_opt_in`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES ('4', '1', 'CUST-20260909-0004', 'Automated Test Customer', NULL, NULL, '01782458939', 'test_cust_358@smartresta.com', NULL, 'MALE', 'SMARTRESTA Labs', 'Updated notes for VIP test customer', '1', 'ACTIVE', '2026-09-09 06:52:51', '2026-09-09 06:52:51', NULL);
INSERT INTO `customers` (`id`, `branch_id`, `customer_code`, `name`, `first_name`, `last_name`, `phone`, `email`, `date_of_birth`, `gender`, `company_name`, `notes`, `marketing_opt_in`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES ('5', '1', 'CUST-20260909-0005', 'Automated Test Customer', NULL, NULL, '01768430241', 'test_cust_715@smartresta.com', NULL, 'MALE', 'SMARTRESTA Labs', 'Updated notes for VIP test customer', '1', 'ACTIVE', '2026-09-09 06:53:00', '2026-09-09 06:53:01', NULL);
INSERT INTO `customers` (`id`, `branch_id`, `customer_code`, `name`, `first_name`, `last_name`, `phone`, `email`, `date_of_birth`, `gender`, `company_name`, `notes`, `marketing_opt_in`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES ('6', '1', 'CUST-20260909-0006', 'Automated Test Customer', NULL, NULL, '01788895267', 'test_cust_735@smartresta.com', NULL, 'MALE', 'SMARTRESTA Labs', 'Updated notes for VIP test customer', '1', 'ACTIVE', '2026-09-09 06:53:19', '2026-09-09 06:53:19', NULL);
INSERT INTO `customers` (`id`, `branch_id`, `customer_code`, `name`, `first_name`, `last_name`, `phone`, `email`, `date_of_birth`, `gender`, `company_name`, `notes`, `marketing_opt_in`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES ('7', '1', 'CUST-20260909-0007', 'Automated Test Customer', NULL, NULL, '01717756393', 'test_cust_175@smartresta.com', NULL, 'MALE', 'SMARTRESTA Labs', 'Updated notes for VIP test customer', '1', 'ACTIVE', '2026-09-09 06:53:45', '2026-09-09 06:53:45', NULL);
INSERT INTO `customers` (`id`, `branch_id`, `customer_code`, `name`, `first_name`, `last_name`, `phone`, `email`, `date_of_birth`, `gender`, `company_name`, `notes`, `marketing_opt_in`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES ('8', '1', 'CUST-20260909-0008', 'QR Table Guest', NULL, NULL, '01955834768', NULL, NULL, 'UNDISCLOSED', NULL, NULL, '1', 'ACTIVE', '2026-09-09 06:53:45', NULL, NULL);
INSERT INTO `customers` (`id`, `branch_id`, `customer_code`, `name`, `first_name`, `last_name`, `phone`, `email`, `date_of_birth`, `gender`, `company_name`, `notes`, `marketing_opt_in`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES ('9', '1', 'CUST-20260909-0009', 'Automated Test Customer', NULL, NULL, '01739881020', 'test_cust_806@smartresta.com', NULL, 'MALE', 'SMARTRESTA Labs', 'Updated notes for VIP test customer', '1', 'ACTIVE', '2026-09-09 06:53:54', '2026-09-09 06:53:54', NULL);
INSERT INTO `customers` (`id`, `branch_id`, `customer_code`, `name`, `first_name`, `last_name`, `phone`, `email`, `date_of_birth`, `gender`, `company_name`, `notes`, `marketing_opt_in`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES ('10', '1', 'CUST-20260909-0010', 'QR Table Guest', NULL, NULL, '01982640758', NULL, NULL, 'UNDISCLOSED', NULL, NULL, '1', 'ACTIVE', '2026-09-09 06:53:54', NULL, NULL);
INSERT INTO `customers` (`id`, `branch_id`, `customer_code`, `name`, `first_name`, `last_name`, `phone`, `email`, `date_of_birth`, `gender`, `company_name`, `notes`, `marketing_opt_in`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES ('11', '1', 'CUST-20260909-0011', 'Automated Test Customer', NULL, NULL, '01775276099', 'test_cust_776@smartresta.com', NULL, 'MALE', 'SMARTRESTA Labs', 'Updated notes for VIP test customer', '1', 'ACTIVE', '2026-09-09 06:54:03', '2026-09-09 06:54:03', NULL);
INSERT INTO `customers` (`id`, `branch_id`, `customer_code`, `name`, `first_name`, `last_name`, `phone`, `email`, `date_of_birth`, `gender`, `company_name`, `notes`, `marketing_opt_in`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES ('12', '1', 'CUST-20260909-0012', 'QR Table Guest', NULL, NULL, '01947574438', NULL, NULL, 'UNDISCLOSED', NULL, NULL, '1', 'ACTIVE', '2026-09-09 06:54:03', NULL, NULL);

-- Table structure for `day_closings`
DROP TABLE IF EXISTS `day_closings`;
CREATE TABLE `day_closings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `branch_id` int(11) NOT NULL DEFAULT 1,
  `business_date` date NOT NULL,
  `status` enum('OPEN','REVIEW','CLOSING','CLOSED') NOT NULL DEFAULT 'CLOSED',
  `gross_sales` decimal(14,2) NOT NULL DEFAULT 0.00,
  `discounts` decimal(14,2) NOT NULL DEFAULT 0.00,
  `tax` decimal(14,2) NOT NULL DEFAULT 0.00,
  `service_charge` decimal(14,2) NOT NULL DEFAULT 0.00,
  `refunds` decimal(14,2) NOT NULL DEFAULT 0.00,
  `net_sales` decimal(14,2) NOT NULL DEFAULT 0.00,
  `cash_collected` decimal(14,2) NOT NULL DEFAULT 0.00,
  `non_cash_collected` decimal(14,2) NOT NULL DEFAULT 0.00,
  `total_expenses` decimal(14,2) NOT NULL DEFAULT 0.00,
  `total_purchases` decimal(14,2) NOT NULL DEFAULT 0.00,
  `total_commissions` decimal(14,2) NOT NULL DEFAULT 0.00,
  `cash_difference` decimal(14,2) NOT NULL DEFAULT 0.00,
  `snapshot_data` longtext DEFAULT NULL,
  `opened_at` datetime DEFAULT NULL,
  `closed_at` datetime DEFAULT current_timestamp(),
  `closed_by_user_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_day_closing_branch_date` (`branch_id`,`business_date`),
  KEY `idx_dc_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `day_closings`
INSERT INTO `day_closings` (`id`, `branch_id`, `business_date`, `status`, `gross_sales`, `discounts`, `tax`, `service_charge`, `refunds`, `net_sales`, `cash_collected`, `non_cash_collected`, `total_expenses`, `total_purchases`, `total_commissions`, `cash_difference`, `snapshot_data`, `opened_at`, `closed_at`, `closed_by_user_id`, `created_at`, `updated_at`) VALUES ('1', '1', '2026-09-09', 'CLOSED', '15120.00', '0.00', '756.00', '0.00', '0.00', '15972.00', '5000.00', '1300.00', '1600.00', '0.00', '0.00', '0.00', '{\"business_date\":\"2026-09-09\",\"closed_by_user_id\":1,\"closed_at\":\"2026-09-09 02:46:12\",\"kpis\":{\"gross_sales\":15120,\"discounts\":0,\"tax\":756,\"refunds\":0,\"net_sales\":15972,\"cash_collected\":5000,\"non_cash_collected\":1300,\"total_collected\":6300,\"total_expenses\":1600,\"net_cash_flow\":3400,\"open_shifts_count\":0}}', NULL, '2026-09-09 06:46:12', '1', '2026-09-09 06:46:12', '2026-09-09 06:46:12');

-- Table structure for `dining_sessions`
DROP TABLE IF EXISTS `dining_sessions`;
CREATE TABLE `dining_sessions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `table_id` int(10) unsigned NOT NULL,
  `customer_id` int(10) unsigned DEFAULT NULL,
  `opened_by_user_id` int(10) unsigned NOT NULL,
  `guest_count` int(11) DEFAULT 1,
  `status` enum('OPEN','WAITING_PAYMENT','CLOSED','CANCELLED') DEFAULT 'OPEN',
  `opened_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `closed_at` timestamp NULL DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `table_id` (`table_id`),
  KEY `opened_by_user_id` (`opened_by_user_id`),
  CONSTRAINT `dining_sessions_ibfk_1` FOREIGN KEY (`table_id`) REFERENCES `restaurant_tables` (`id`),
  CONSTRAINT `dining_sessions_ibfk_2` FOREIGN KEY (`opened_by_user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for `dining_sessions`
INSERT INTO `dining_sessions` (`id`, `table_id`, `customer_id`, `opened_by_user_id`, `guest_count`, `status`, `opened_at`, `closed_at`, `notes`) VALUES ('1', '2', NULL, '1', '2', 'OPEN', '2026-09-09 06:52:16', NULL, NULL);

-- Table structure for `expense_categories`
DROP TABLE IF EXISTS `expense_categories`;
CREATE TABLE `expense_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for `expense_categories`
INSERT INTO `expense_categories` (`id`, `name`) VALUES ('3', 'Cleaning & Maintenance');
INSERT INTO `expense_categories` (`id`, `name`) VALUES ('6', 'Marketing & Promotion');
INSERT INTO `expense_categories` (`id`, `name`) VALUES ('8', 'Other Operating Expenses');
INSERT INTO `expense_categories` (`id`, `name`) VALUES ('2', 'Rent & Lease');
INSERT INTO `expense_categories` (`id`, `name`) VALUES ('7', 'Staff Expenses');
INSERT INTO `expense_categories` (`id`, `name`) VALUES ('5', 'Supplies & Consumables');
INSERT INTO `expense_categories` (`id`, `name`) VALUES ('4', 'Transportation & Freight');
INSERT INTO `expense_categories` (`id`, `name`) VALUES ('1', 'Utilities (Electricity, Water, Gas)');

-- Table structure for `expenses`
DROP TABLE IF EXISTS `expenses`;
CREATE TABLE `expenses` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` int(11) NOT NULL DEFAULT 1,
  `category_id` int(10) unsigned NOT NULL,
  `expense_category_id` int(11) DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `expense_date` date DEFAULT NULL,
  `payment_method_id` int(11) DEFAULT 1,
  `supplier_id` int(11) DEFAULT NULL,
  `title` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `reference_number` varchar(100) DEFAULT NULL,
  `status` enum('DRAFT','SUBMITTED','APPROVED','PAID','REJECTED','CANCELLED') NOT NULL DEFAULT 'APPROVED',
  `recorded_by_user_id` int(10) unsigned NOT NULL,
  `approved_by_user_id` int(11) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `expenses_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `expense_categories` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for `expenses`
INSERT INTO `expenses` (`id`, `branch_id`, `category_id`, `expense_category_id`, `amount`, `expense_date`, `payment_method_id`, `supplier_id`, `title`, `description`, `reference_number`, `status`, `recorded_by_user_id`, `approved_by_user_id`, `approved_at`, `paid_at`, `created_at`, `updated_at`) VALUES ('2', '1', '1', NULL, '800.00', '2026-09-09', '1', NULL, 'Emergency Plumbing Repair', 'Fixed kitchen sink drain pipe leak', NULL, 'PAID', '1', '1', NULL, '2026-09-09 06:46:02', '2026-09-09 06:46:02', '2026-09-09 06:46:02');
INSERT INTO `expenses` (`id`, `branch_id`, `category_id`, `expense_category_id`, `amount`, `expense_date`, `payment_method_id`, `supplier_id`, `title`, `description`, `reference_number`, `status`, `recorded_by_user_id`, `approved_by_user_id`, `approved_at`, `paid_at`, `created_at`, `updated_at`) VALUES ('3', '1', '1', NULL, '800.00', '2026-09-09', '1', NULL, 'Emergency Plumbing Repair', 'Fixed kitchen sink drain pipe leak', NULL, 'PAID', '1', '1', NULL, '2026-09-09 06:46:12', '2026-09-09 06:46:12', '2026-09-09 06:46:12');

-- Table structure for `financial_adjustments`
DROP TABLE IF EXISTS `financial_adjustments`;
CREATE TABLE `financial_adjustments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `branch_id` int(11) NOT NULL DEFAULT 1,
  `adjustment_type` varchar(50) NOT NULL,
  `amount` decimal(14,2) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `created_by_user_id` int(11) NOT NULL,
  `approved_by_user_id` int(11) DEFAULT NULL,
  `status` enum('PENDING','APPROVED','REJECTED') NOT NULL DEFAULT 'APPROVED',
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_fa_branch_date` (`branch_id`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for `floors`
DROP TABLE IF EXISTS `floors`;
CREATE TABLE `floors` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` int(10) unsigned NOT NULL,
  `name` varchar(80) NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `branch_id` (`branch_id`),
  CONSTRAINT `floors_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for `floors`
INSERT INTO `floors` (`id`, `branch_id`, `name`, `sort_order`) VALUES ('1', '1', 'Main Dining Hall', '0');

-- Table structure for `goods_receipt_items`
DROP TABLE IF EXISTS `goods_receipt_items`;
CREATE TABLE `goods_receipt_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `goods_receipt_id` int(10) unsigned NOT NULL,
  `ingredient_id` int(10) unsigned NOT NULL,
  `location_id` int(10) unsigned NOT NULL,
  `received_quantity` decimal(12,4) NOT NULL,
  `unit_price` decimal(12,2) NOT NULL,
  `total_cost` decimal(12,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `goods_receipt_id` (`goods_receipt_id`),
  KEY `ingredient_id` (`ingredient_id`),
  KEY `location_id` (`location_id`),
  CONSTRAINT `goods_receipt_items_ibfk_1` FOREIGN KEY (`goods_receipt_id`) REFERENCES `goods_receipts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `goods_receipt_items_ibfk_2` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`id`),
  CONSTRAINT `goods_receipt_items_ibfk_3` FOREIGN KEY (`location_id`) REFERENCES `inventory_locations` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `goods_receipt_items`
INSERT INTO `goods_receipt_items` (`id`, `goods_receipt_id`, `ingredient_id`, `location_id`, `received_quantity`, `unit_price`, `total_cost`) VALUES ('1', '1', '6', '1', '50.0000', '110.00', '5500.00');
INSERT INTO `goods_receipt_items` (`id`, `goods_receipt_id`, `ingredient_id`, `location_id`, `received_quantity`, `unit_price`, `total_cost`) VALUES ('2', '2', '7', '1', '50.0000', '110.00', '5500.00');
INSERT INTO `goods_receipt_items` (`id`, `goods_receipt_id`, `ingredient_id`, `location_id`, `received_quantity`, `unit_price`, `total_cost`) VALUES ('3', '3', '8', '1', '50.0000', '110.00', '5500.00');
INSERT INTO `goods_receipt_items` (`id`, `goods_receipt_id`, `ingredient_id`, `location_id`, `received_quantity`, `unit_price`, `total_cost`) VALUES ('4', '4', '9', '1', '50.0000', '110.00', '5500.00');
INSERT INTO `goods_receipt_items` (`id`, `goods_receipt_id`, `ingredient_id`, `location_id`, `received_quantity`, `unit_price`, `total_cost`) VALUES ('5', '5', '10', '1', '50.0000', '110.00', '5500.00');
INSERT INTO `goods_receipt_items` (`id`, `goods_receipt_id`, `ingredient_id`, `location_id`, `received_quantity`, `unit_price`, `total_cost`) VALUES ('6', '6', '11', '1', '50.0000', '110.00', '5500.00');
INSERT INTO `goods_receipt_items` (`id`, `goods_receipt_id`, `ingredient_id`, `location_id`, `received_quantity`, `unit_price`, `total_cost`) VALUES ('7', '7', '12', '1', '50.0000', '110.00', '5500.00');
INSERT INTO `goods_receipt_items` (`id`, `goods_receipt_id`, `ingredient_id`, `location_id`, `received_quantity`, `unit_price`, `total_cost`) VALUES ('8', '8', '13', '1', '50.0000', '110.00', '5500.00');
INSERT INTO `goods_receipt_items` (`id`, `goods_receipt_id`, `ingredient_id`, `location_id`, `received_quantity`, `unit_price`, `total_cost`) VALUES ('9', '9', '14', '1', '50.0000', '110.00', '5500.00');
INSERT INTO `goods_receipt_items` (`id`, `goods_receipt_id`, `ingredient_id`, `location_id`, `received_quantity`, `unit_price`, `total_cost`) VALUES ('10', '10', '15', '1', '50.0000', '110.00', '5500.00');
INSERT INTO `goods_receipt_items` (`id`, `goods_receipt_id`, `ingredient_id`, `location_id`, `received_quantity`, `unit_price`, `total_cost`) VALUES ('11', '11', '16', '1', '50.0000', '110.00', '5500.00');
INSERT INTO `goods_receipt_items` (`id`, `goods_receipt_id`, `ingredient_id`, `location_id`, `received_quantity`, `unit_price`, `total_cost`) VALUES ('12', '12', '18', '1', '50.0000', '110.00', '5500.00');
INSERT INTO `goods_receipt_items` (`id`, `goods_receipt_id`, `ingredient_id`, `location_id`, `received_quantity`, `unit_price`, `total_cost`) VALUES ('13', '13', '19', '1', '50.0000', '110.00', '5500.00');
INSERT INTO `goods_receipt_items` (`id`, `goods_receipt_id`, `ingredient_id`, `location_id`, `received_quantity`, `unit_price`, `total_cost`) VALUES ('14', '14', '20', '1', '50.0000', '110.00', '5500.00');

-- Table structure for `goods_receipts`
DROP TABLE IF EXISTS `goods_receipts`;
CREATE TABLE `goods_receipts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `purchase_order_id` int(10) unsigned NOT NULL,
  `receipt_number` varchar(50) NOT NULL,
  `supplier_id` int(10) unsigned NOT NULL,
  `received_by` int(10) unsigned DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `received_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `receipt_number` (`receipt_number`),
  KEY `purchase_order_id` (`purchase_order_id`),
  KEY `supplier_id` (`supplier_id`),
  CONSTRAINT `goods_receipts_ibfk_1` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `goods_receipts_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `goods_receipts`
INSERT INTO `goods_receipts` (`id`, `purchase_order_id`, `receipt_number`, `supplier_id`, `received_by`, `notes`, `received_at`) VALUES ('1', '4', 'GRN-20260909-5183', '4', '1', 'INV-RECV-9911', '2026-09-09 06:04:00');
INSERT INTO `goods_receipts` (`id`, `purchase_order_id`, `receipt_number`, `supplier_id`, `received_by`, `notes`, `received_at`) VALUES ('2', '5', 'GRN-20260909-5451', '5', '1', 'INV-RECV-9911', '2026-09-09 06:04:08');
INSERT INTO `goods_receipts` (`id`, `purchase_order_id`, `receipt_number`, `supplier_id`, `received_by`, `notes`, `received_at`) VALUES ('3', '6', 'GRN-20260909-9573', '6', '1', 'INV-RECV-9911', '2026-09-09 06:04:26');
INSERT INTO `goods_receipts` (`id`, `purchase_order_id`, `receipt_number`, `supplier_id`, `received_by`, `notes`, `received_at`) VALUES ('4', '7', 'GRN-20260909-6174', '7', '1', 'INV-RECV-9911', '2026-09-09 06:04:35');
INSERT INTO `goods_receipts` (`id`, `purchase_order_id`, `receipt_number`, `supplier_id`, `received_by`, `notes`, `received_at`) VALUES ('5', '8', 'GRN-20260909-8859', '8', '1', 'INV-RECV-9911', '2026-09-09 06:04:43');
INSERT INTO `goods_receipts` (`id`, `purchase_order_id`, `receipt_number`, `supplier_id`, `received_by`, `notes`, `received_at`) VALUES ('6', '9', 'GRN-20260909-6859', '9', '1', 'INV-RECV-9911', '2026-09-09 06:04:55');
INSERT INTO `goods_receipts` (`id`, `purchase_order_id`, `receipt_number`, `supplier_id`, `received_by`, `notes`, `received_at`) VALUES ('7', '10', 'GRN-20260909-6300', '10', '1', 'INV-RECV-9911', '2026-09-09 06:05:06');
INSERT INTO `goods_receipts` (`id`, `purchase_order_id`, `receipt_number`, `supplier_id`, `received_by`, `notes`, `received_at`) VALUES ('8', '11', 'GRN-20260909-7175', '11', '1', 'INV-RECV-9911', '2026-09-09 06:05:15');
INSERT INTO `goods_receipts` (`id`, `purchase_order_id`, `receipt_number`, `supplier_id`, `received_by`, `notes`, `received_at`) VALUES ('9', '12', 'GRN-20260909-5318', '12', '1', 'INV-RECV-9911', '2026-09-09 06:05:23');
INSERT INTO `goods_receipts` (`id`, `purchase_order_id`, `receipt_number`, `supplier_id`, `received_by`, `notes`, `received_at`) VALUES ('10', '13', 'GRN-20260909-4955', '13', '1', 'INV-RECV-9911', '2026-09-09 06:05:31');
INSERT INTO `goods_receipts` (`id`, `purchase_order_id`, `receipt_number`, `supplier_id`, `received_by`, `notes`, `received_at`) VALUES ('11', '14', 'GRN-20260909-9809', '14', '1', 'INV-RECV-9911', '2026-09-09 06:05:43');
INSERT INTO `goods_receipts` (`id`, `purchase_order_id`, `receipt_number`, `supplier_id`, `received_by`, `notes`, `received_at`) VALUES ('12', '16', 'GRN-20260909-7062', '16', '1', 'INV-RECV-9911', '2026-09-09 06:06:06');
INSERT INTO `goods_receipts` (`id`, `purchase_order_id`, `receipt_number`, `supplier_id`, `received_by`, `notes`, `received_at`) VALUES ('13', '17', 'GRN-20260909-1332', '17', '1', 'INV-RECV-9911', '2026-09-09 06:06:20');
INSERT INTO `goods_receipts` (`id`, `purchase_order_id`, `receipt_number`, `supplier_id`, `received_by`, `notes`, `received_at`) VALUES ('14', '18', 'GRN-20260909-4900', '18', '1', 'INV-RECV-9911', '2026-09-09 06:06:26');

-- Table structure for `ingredient_categories`
DROP TABLE IF EXISTS `ingredient_categories`;
CREATE TABLE `ingredient_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` int(10) unsigned NOT NULL DEFAULT 1,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for `ingredients`
DROP TABLE IF EXISTS `ingredients`;
CREATE TABLE `ingredients` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` int(10) unsigned NOT NULL DEFAULT 1,
  `name` varchar(100) NOT NULL,
  `sku` varchar(50) DEFAULT NULL,
  `ingredient_code` varchar(50) DEFAULT NULL,
  `category_id` int(10) unsigned DEFAULT NULL,
  `base_unit` varchar(20) NOT NULL DEFAULT 'kg',
  `purchase_unit` varchar(20) NOT NULL DEFAULT 'kg',
  `conversion_factor` decimal(12,4) NOT NULL DEFAULT 1.0000,
  `unit` varchar(20) NOT NULL,
  `current_stock` decimal(12,2) NOT NULL DEFAULT 0.00,
  `min_stock` decimal(12,2) NOT NULL DEFAULT 10.00,
  `reorder_level` decimal(12,2) NOT NULL DEFAULT 10.00,
  `max_stock` decimal(12,2) NOT NULL DEFAULT 100.00,
  `cost_per_unit` decimal(12,2) NOT NULL DEFAULT 0.00,
  `average_cost` decimal(12,2) NOT NULL DEFAULT 0.00,
  `last_purchase_cost` decimal(12,2) NOT NULL DEFAULT 0.00,
  `preferred_supplier_id` int(10) unsigned DEFAULT NULL,
  `status` enum('ACTIVE','INACTIVE') DEFAULT 'ACTIVE',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for `ingredients`
INSERT INTO `ingredients` (`id`, `branch_id`, `name`, `sku`, `ingredient_code`, `category_id`, `base_unit`, `purchase_unit`, `conversion_factor`, `unit`, `current_stock`, `min_stock`, `reorder_level`, `max_stock`, `cost_per_unit`, `average_cost`, `last_purchase_cost`, `preferred_supplier_id`, `status`, `created_at`, `updated_at`) VALUES ('1', '1', 'Premium Basmati Rice Test', 'TEST-RICE-657', 'TEST-RICE-657', NULL, 'kg', 'kg', '1.0000', 'kg', '0.00', '10.00', '10.00', '100.00', '120.00', '120.00', '0.00', NULL, 'ACTIVE', '2026-09-09 06:02:36', '2026-09-09 06:02:36');
INSERT INTO `ingredients` (`id`, `branch_id`, `name`, `sku`, `ingredient_code`, `category_id`, `base_unit`, `purchase_unit`, `conversion_factor`, `unit`, `current_stock`, `min_stock`, `reorder_level`, `max_stock`, `cost_per_unit`, `average_cost`, `last_purchase_cost`, `preferred_supplier_id`, `status`, `created_at`, `updated_at`) VALUES ('3', '1', 'Premium Basmati Rice Test 4366', 'TEST-RICE-4366', 'TEST-RICE-4366', NULL, 'kg', 'kg', '1.0000', 'kg', '20.00', '10.00', '10.00', '100.00', '120.00', '120.00', '0.00', NULL, 'ACTIVE', '2026-09-09 06:02:59', '2026-09-09 06:02:59');
INSERT INTO `ingredients` (`id`, `branch_id`, `name`, `sku`, `ingredient_code`, `category_id`, `base_unit`, `purchase_unit`, `conversion_factor`, `unit`, `current_stock`, `min_stock`, `reorder_level`, `max_stock`, `cost_per_unit`, `average_cost`, `last_purchase_cost`, `preferred_supplier_id`, `status`, `created_at`, `updated_at`) VALUES ('4', '1', 'Premium Basmati Rice Test 4224', 'TEST-RICE-4224', 'TEST-RICE-4224', NULL, 'kg', 'kg', '1.0000', 'kg', '20.00', '10.00', '10.00', '100.00', '120.00', '120.00', '0.00', NULL, 'ACTIVE', '2026-09-09 06:03:28', '2026-09-09 06:03:28');
INSERT INTO `ingredients` (`id`, `branch_id`, `name`, `sku`, `ingredient_code`, `category_id`, `base_unit`, `purchase_unit`, `conversion_factor`, `unit`, `current_stock`, `min_stock`, `reorder_level`, `max_stock`, `cost_per_unit`, `average_cost`, `last_purchase_cost`, `preferred_supplier_id`, `status`, `created_at`, `updated_at`) VALUES ('5', '1', 'Premium Basmati Rice Test 6531', 'TEST-RICE-6531', 'TEST-RICE-6531', NULL, 'kg', 'kg', '1.0000', 'kg', '20.00', '10.00', '10.00', '100.00', '120.00', '120.00', '0.00', NULL, 'ACTIVE', '2026-09-09 06:03:50', '2026-09-09 06:03:50');
INSERT INTO `ingredients` (`id`, `branch_id`, `name`, `sku`, `ingredient_code`, `category_id`, `base_unit`, `purchase_unit`, `conversion_factor`, `unit`, `current_stock`, `min_stock`, `reorder_level`, `max_stock`, `cost_per_unit`, `average_cost`, `last_purchase_cost`, `preferred_supplier_id`, `status`, `created_at`, `updated_at`) VALUES ('6', '1', 'Premium Basmati Rice Test 7985', 'TEST-RICE-7985', 'TEST-RICE-7985', NULL, 'kg', 'kg', '1.0000', 'kg', '70.00', '10.00', '10.00', '100.00', '112.86', '112.86', '110.00', NULL, 'ACTIVE', '2026-09-09 06:04:00', '2026-09-09 06:04:00');
INSERT INTO `ingredients` (`id`, `branch_id`, `name`, `sku`, `ingredient_code`, `category_id`, `base_unit`, `purchase_unit`, `conversion_factor`, `unit`, `current_stock`, `min_stock`, `reorder_level`, `max_stock`, `cost_per_unit`, `average_cost`, `last_purchase_cost`, `preferred_supplier_id`, `status`, `created_at`, `updated_at`) VALUES ('7', '1', 'Premium Basmati Rice Test 6094', 'TEST-RICE-6094', 'TEST-RICE-6094', NULL, 'kg', 'kg', '1.0000', 'kg', '70.00', '10.00', '10.00', '100.00', '112.86', '112.86', '110.00', NULL, 'ACTIVE', '2026-09-09 06:04:08', '2026-09-09 06:04:08');
INSERT INTO `ingredients` (`id`, `branch_id`, `name`, `sku`, `ingredient_code`, `category_id`, `base_unit`, `purchase_unit`, `conversion_factor`, `unit`, `current_stock`, `min_stock`, `reorder_level`, `max_stock`, `cost_per_unit`, `average_cost`, `last_purchase_cost`, `preferred_supplier_id`, `status`, `created_at`, `updated_at`) VALUES ('8', '1', 'Premium Basmati Rice Test 5605', 'TEST-RICE-5605', 'TEST-RICE-5605', NULL, 'kg', 'kg', '1.0000', 'kg', '70.00', '10.00', '10.00', '100.00', '112.86', '112.86', '110.00', NULL, 'ACTIVE', '2026-09-09 06:04:26', '2026-09-09 06:04:26');
INSERT INTO `ingredients` (`id`, `branch_id`, `name`, `sku`, `ingredient_code`, `category_id`, `base_unit`, `purchase_unit`, `conversion_factor`, `unit`, `current_stock`, `min_stock`, `reorder_level`, `max_stock`, `cost_per_unit`, `average_cost`, `last_purchase_cost`, `preferred_supplier_id`, `status`, `created_at`, `updated_at`) VALUES ('9', '1', 'Premium Basmati Rice Test 7960', 'TEST-RICE-7960', 'TEST-RICE-7960', NULL, 'kg', 'kg', '1.0000', 'kg', '70.00', '10.00', '10.00', '100.00', '112.86', '112.86', '110.00', NULL, 'ACTIVE', '2026-09-09 06:04:35', '2026-09-09 06:04:35');
INSERT INTO `ingredients` (`id`, `branch_id`, `name`, `sku`, `ingredient_code`, `category_id`, `base_unit`, `purchase_unit`, `conversion_factor`, `unit`, `current_stock`, `min_stock`, `reorder_level`, `max_stock`, `cost_per_unit`, `average_cost`, `last_purchase_cost`, `preferred_supplier_id`, `status`, `created_at`, `updated_at`) VALUES ('10', '1', 'Premium Basmati Rice Test 3567', 'TEST-RICE-3567', 'TEST-RICE-3567', NULL, 'kg', 'kg', '1.0000', 'kg', '70.00', '10.00', '10.00', '100.00', '112.86', '112.86', '110.00', NULL, 'ACTIVE', '2026-09-09 06:04:43', '2026-09-09 06:04:43');
INSERT INTO `ingredients` (`id`, `branch_id`, `name`, `sku`, `ingredient_code`, `category_id`, `base_unit`, `purchase_unit`, `conversion_factor`, `unit`, `current_stock`, `min_stock`, `reorder_level`, `max_stock`, `cost_per_unit`, `average_cost`, `last_purchase_cost`, `preferred_supplier_id`, `status`, `created_at`, `updated_at`) VALUES ('11', '1', 'Premium Basmati Rice Test 8820', 'TEST-RICE-8820', 'TEST-RICE-8820', NULL, 'kg', 'kg', '1.0000', 'kg', '70.00', '10.00', '10.00', '100.00', '112.86', '112.86', '110.00', NULL, 'ACTIVE', '2026-09-09 06:04:55', '2026-09-09 06:04:55');
INSERT INTO `ingredients` (`id`, `branch_id`, `name`, `sku`, `ingredient_code`, `category_id`, `base_unit`, `purchase_unit`, `conversion_factor`, `unit`, `current_stock`, `min_stock`, `reorder_level`, `max_stock`, `cost_per_unit`, `average_cost`, `last_purchase_cost`, `preferred_supplier_id`, `status`, `created_at`, `updated_at`) VALUES ('12', '1', 'Premium Basmati Rice Test 4705', 'TEST-RICE-4705', 'TEST-RICE-4705', NULL, 'kg', 'kg', '1.0000', 'kg', '70.00', '10.00', '10.00', '100.00', '112.86', '112.86', '110.00', NULL, 'ACTIVE', '2026-09-09 06:05:06', '2026-09-09 06:05:06');
INSERT INTO `ingredients` (`id`, `branch_id`, `name`, `sku`, `ingredient_code`, `category_id`, `base_unit`, `purchase_unit`, `conversion_factor`, `unit`, `current_stock`, `min_stock`, `reorder_level`, `max_stock`, `cost_per_unit`, `average_cost`, `last_purchase_cost`, `preferred_supplier_id`, `status`, `created_at`, `updated_at`) VALUES ('13', '1', 'Premium Basmati Rice Test 5943', 'TEST-RICE-5943', 'TEST-RICE-5943', NULL, 'kg', 'kg', '1.0000', 'kg', '70.00', '10.00', '10.00', '100.00', '112.86', '112.86', '110.00', NULL, 'ACTIVE', '2026-09-09 06:05:15', '2026-09-09 06:05:15');
INSERT INTO `ingredients` (`id`, `branch_id`, `name`, `sku`, `ingredient_code`, `category_id`, `base_unit`, `purchase_unit`, `conversion_factor`, `unit`, `current_stock`, `min_stock`, `reorder_level`, `max_stock`, `cost_per_unit`, `average_cost`, `last_purchase_cost`, `preferred_supplier_id`, `status`, `created_at`, `updated_at`) VALUES ('14', '1', 'Premium Basmati Rice Test 2713', 'TEST-RICE-2713', 'TEST-RICE-2713', NULL, 'kg', 'kg', '1.0000', 'kg', '70.00', '10.00', '10.00', '100.00', '112.86', '112.86', '110.00', NULL, 'ACTIVE', '2026-09-09 06:05:23', '2026-09-09 06:05:23');
INSERT INTO `ingredients` (`id`, `branch_id`, `name`, `sku`, `ingredient_code`, `category_id`, `base_unit`, `purchase_unit`, `conversion_factor`, `unit`, `current_stock`, `min_stock`, `reorder_level`, `max_stock`, `cost_per_unit`, `average_cost`, `last_purchase_cost`, `preferred_supplier_id`, `status`, `created_at`, `updated_at`) VALUES ('15', '1', 'Premium Basmati Rice Test 1556', 'TEST-RICE-1556', 'TEST-RICE-1556', NULL, 'kg', 'kg', '1.0000', 'kg', '70.00', '10.00', '10.00', '100.00', '112.86', '112.86', '110.00', NULL, 'ACTIVE', '2026-09-09 06:05:31', '2026-09-09 06:05:31');
INSERT INTO `ingredients` (`id`, `branch_id`, `name`, `sku`, `ingredient_code`, `category_id`, `base_unit`, `purchase_unit`, `conversion_factor`, `unit`, `current_stock`, `min_stock`, `reorder_level`, `max_stock`, `cost_per_unit`, `average_cost`, `last_purchase_cost`, `preferred_supplier_id`, `status`, `created_at`, `updated_at`) VALUES ('16', '1', 'Premium Basmati Rice Test 3786', 'TEST-RICE-3786', 'TEST-RICE-3786', NULL, 'kg', 'kg', '1.0000', 'kg', '69.40', '10.00', '10.00', '100.00', '112.86', '112.86', '110.00', NULL, 'ACTIVE', '2026-09-09 06:05:43', '2026-09-09 06:05:43');
INSERT INTO `ingredients` (`id`, `branch_id`, `name`, `sku`, `ingredient_code`, `category_id`, `base_unit`, `purchase_unit`, `conversion_factor`, `unit`, `current_stock`, `min_stock`, `reorder_level`, `max_stock`, `cost_per_unit`, `average_cost`, `last_purchase_cost`, `preferred_supplier_id`, `status`, `created_at`, `updated_at`) VALUES ('17', '1', 'Premium Basmati Rice Test 8331', 'TEST-RICE-8331', 'TEST-RICE-8331', NULL, 'kg', 'kg', '1.0000', 'kg', '20.00', '10.00', '10.00', '100.00', '120.00', '120.00', '0.00', NULL, 'ACTIVE', '2026-09-09 06:05:58', '2026-09-09 06:05:58');
INSERT INTO `ingredients` (`id`, `branch_id`, `name`, `sku`, `ingredient_code`, `category_id`, `base_unit`, `purchase_unit`, `conversion_factor`, `unit`, `current_stock`, `min_stock`, `reorder_level`, `max_stock`, `cost_per_unit`, `average_cost`, `last_purchase_cost`, `preferred_supplier_id`, `status`, `created_at`, `updated_at`) VALUES ('18', '1', 'Premium Basmati Rice Test 3318', 'TEST-RICE-3318', 'TEST-RICE-3318', NULL, 'kg', 'kg', '1.0000', 'kg', '69.40', '10.00', '10.00', '100.00', '112.86', '112.86', '110.00', NULL, 'ACTIVE', '2026-09-09 06:06:06', '2026-09-09 06:06:06');
INSERT INTO `ingredients` (`id`, `branch_id`, `name`, `sku`, `ingredient_code`, `category_id`, `base_unit`, `purchase_unit`, `conversion_factor`, `unit`, `current_stock`, `min_stock`, `reorder_level`, `max_stock`, `cost_per_unit`, `average_cost`, `last_purchase_cost`, `preferred_supplier_id`, `status`, `created_at`, `updated_at`) VALUES ('19', '1', 'Premium Basmati Rice Test 4850', 'TEST-RICE-4850', 'TEST-RICE-4850', NULL, 'kg', 'kg', '1.0000', 'kg', '68.50', '10.00', '10.00', '100.00', '112.86', '112.86', '110.00', NULL, 'ACTIVE', '2026-09-09 06:06:20', '2026-09-09 06:06:20');
INSERT INTO `ingredients` (`id`, `branch_id`, `name`, `sku`, `ingredient_code`, `category_id`, `base_unit`, `purchase_unit`, `conversion_factor`, `unit`, `current_stock`, `min_stock`, `reorder_level`, `max_stock`, `cost_per_unit`, `average_cost`, `last_purchase_cost`, `preferred_supplier_id`, `status`, `created_at`, `updated_at`) VALUES ('20', '1', 'Premium Basmati Rice Test 4068', 'TEST-RICE-4068', 'TEST-RICE-4068', NULL, 'kg', 'kg', '1.0000', 'kg', '68.50', '10.00', '10.00', '100.00', '112.86', '112.86', '110.00', NULL, 'ACTIVE', '2026-09-09 06:06:26', '2026-09-09 06:06:27');

-- Table structure for `inventory_locations`
DROP TABLE IF EXISTS `inventory_locations`;
CREATE TABLE `inventory_locations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` int(10) unsigned NOT NULL DEFAULT 1,
  `name` varchar(100) NOT NULL,
  `code` varchar(30) NOT NULL,
  `location_type` enum('MAIN_STORE','KITCHEN_STORE','BAR_STORE','COLD_STORAGE','DRY_STORAGE') DEFAULT 'MAIN_STORE',
  `status` enum('ACTIVE','INACTIVE') DEFAULT 'ACTIVE',
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `inventory_locations`
INSERT INTO `inventory_locations` (`id`, `branch_id`, `name`, `code`, `location_type`, `status`, `created_at`) VALUES ('1', '1', 'Main Central Store', 'LOC-MAIN', 'MAIN_STORE', 'ACTIVE', '2026-09-09 05:58:07');
INSERT INTO `inventory_locations` (`id`, `branch_id`, `name`, `code`, `location_type`, `status`, `created_at`) VALUES ('2', '1', 'Kitchen Operational Store', 'LOC-KITCHEN', 'KITCHEN_STORE', 'ACTIVE', '2026-09-09 05:58:07');

-- Table structure for `inventory_stock`
DROP TABLE IF EXISTS `inventory_stock`;
CREATE TABLE `inventory_stock` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ingredient_id` int(10) unsigned NOT NULL,
  `location_id` int(10) unsigned NOT NULL,
  `quantity_on_hand` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `reserved_quantity` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `average_cost` decimal(12,2) NOT NULL DEFAULT 0.00,
  `last_purchase_cost` decimal(12,2) NOT NULL DEFAULT 0.00,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_ing_location` (`ingredient_id`,`location_id`),
  KEY `location_id` (`location_id`),
  CONSTRAINT `inventory_stock_ibfk_1` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventory_stock_ibfk_2` FOREIGN KEY (`location_id`) REFERENCES `inventory_locations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `inventory_stock`
INSERT INTO `inventory_stock` (`id`, `ingredient_id`, `location_id`, `quantity_on_hand`, `reserved_quantity`, `average_cost`, `last_purchase_cost`, `updated_at`) VALUES ('1', '1', '1', '0.0000', '0.0000', '120.00', '0.00', '2026-09-09 06:02:36');
INSERT INTO `inventory_stock` (`id`, `ingredient_id`, `location_id`, `quantity_on_hand`, `reserved_quantity`, `average_cost`, `last_purchase_cost`, `updated_at`) VALUES ('2', '3', '1', '20.0000', '0.0000', '120.00', '0.00', '2026-09-09 06:02:59');
INSERT INTO `inventory_stock` (`id`, `ingredient_id`, `location_id`, `quantity_on_hand`, `reserved_quantity`, `average_cost`, `last_purchase_cost`, `updated_at`) VALUES ('4', '4', '1', '20.0000', '0.0000', '120.00', '0.00', '2026-09-09 06:03:28');
INSERT INTO `inventory_stock` (`id`, `ingredient_id`, `location_id`, `quantity_on_hand`, `reserved_quantity`, `average_cost`, `last_purchase_cost`, `updated_at`) VALUES ('6', '5', '1', '20.0000', '0.0000', '120.00', '0.00', '2026-09-09 06:03:50');
INSERT INTO `inventory_stock` (`id`, `ingredient_id`, `location_id`, `quantity_on_hand`, `reserved_quantity`, `average_cost`, `last_purchase_cost`, `updated_at`) VALUES ('8', '6', '1', '70.0000', '0.0000', '112.86', '110.00', '2026-09-09 06:04:00');
INSERT INTO `inventory_stock` (`id`, `ingredient_id`, `location_id`, `quantity_on_hand`, `reserved_quantity`, `average_cost`, `last_purchase_cost`, `updated_at`) VALUES ('11', '7', '1', '70.0000', '0.0000', '112.86', '110.00', '2026-09-09 06:04:08');
INSERT INTO `inventory_stock` (`id`, `ingredient_id`, `location_id`, `quantity_on_hand`, `reserved_quantity`, `average_cost`, `last_purchase_cost`, `updated_at`) VALUES ('14', '8', '1', '70.0000', '0.0000', '112.86', '110.00', '2026-09-09 06:04:26');
INSERT INTO `inventory_stock` (`id`, `ingredient_id`, `location_id`, `quantity_on_hand`, `reserved_quantity`, `average_cost`, `last_purchase_cost`, `updated_at`) VALUES ('17', '9', '1', '70.0000', '0.0000', '112.86', '110.00', '2026-09-09 06:04:35');
INSERT INTO `inventory_stock` (`id`, `ingredient_id`, `location_id`, `quantity_on_hand`, `reserved_quantity`, `average_cost`, `last_purchase_cost`, `updated_at`) VALUES ('20', '10', '1', '70.0000', '0.0000', '112.86', '110.00', '2026-09-09 06:04:43');
INSERT INTO `inventory_stock` (`id`, `ingredient_id`, `location_id`, `quantity_on_hand`, `reserved_quantity`, `average_cost`, `last_purchase_cost`, `updated_at`) VALUES ('23', '11', '1', '70.0000', '0.0000', '112.86', '110.00', '2026-09-09 06:04:55');
INSERT INTO `inventory_stock` (`id`, `ingredient_id`, `location_id`, `quantity_on_hand`, `reserved_quantity`, `average_cost`, `last_purchase_cost`, `updated_at`) VALUES ('26', '12', '1', '70.0000', '0.0000', '112.86', '110.00', '2026-09-09 06:05:06');
INSERT INTO `inventory_stock` (`id`, `ingredient_id`, `location_id`, `quantity_on_hand`, `reserved_quantity`, `average_cost`, `last_purchase_cost`, `updated_at`) VALUES ('29', '13', '1', '70.0000', '0.0000', '112.86', '110.00', '2026-09-09 06:05:15');
INSERT INTO `inventory_stock` (`id`, `ingredient_id`, `location_id`, `quantity_on_hand`, `reserved_quantity`, `average_cost`, `last_purchase_cost`, `updated_at`) VALUES ('32', '14', '1', '70.0000', '0.0000', '112.86', '110.00', '2026-09-09 06:05:23');
INSERT INTO `inventory_stock` (`id`, `ingredient_id`, `location_id`, `quantity_on_hand`, `reserved_quantity`, `average_cost`, `last_purchase_cost`, `updated_at`) VALUES ('35', '15', '1', '70.0000', '0.0000', '112.86', '110.00', '2026-09-09 06:05:31');
INSERT INTO `inventory_stock` (`id`, `ingredient_id`, `location_id`, `quantity_on_hand`, `reserved_quantity`, `average_cost`, `last_purchase_cost`, `updated_at`) VALUES ('38', '16', '1', '69.4000', '0.0000', '112.86', '110.00', '2026-09-09 06:05:43');
INSERT INTO `inventory_stock` (`id`, `ingredient_id`, `location_id`, `quantity_on_hand`, `reserved_quantity`, `average_cost`, `last_purchase_cost`, `updated_at`) VALUES ('42', '17', '1', '20.0000', '0.0000', '120.00', '0.00', '2026-09-09 06:05:58');
INSERT INTO `inventory_stock` (`id`, `ingredient_id`, `location_id`, `quantity_on_hand`, `reserved_quantity`, `average_cost`, `last_purchase_cost`, `updated_at`) VALUES ('44', '18', '1', '69.4000', '0.0000', '112.86', '110.00', '2026-09-09 06:06:06');
INSERT INTO `inventory_stock` (`id`, `ingredient_id`, `location_id`, `quantity_on_hand`, `reserved_quantity`, `average_cost`, `last_purchase_cost`, `updated_at`) VALUES ('48', '19', '1', '58.5000', '0.0000', '112.86', '110.00', '2026-09-09 06:06:20');
INSERT INTO `inventory_stock` (`id`, `ingredient_id`, `location_id`, `quantity_on_hand`, `reserved_quantity`, `average_cost`, `last_purchase_cost`, `updated_at`) VALUES ('52', '19', '2', '10.0000', '0.0000', '112.86', '0.00', '2026-09-09 06:06:20');
INSERT INTO `inventory_stock` (`id`, `ingredient_id`, `location_id`, `quantity_on_hand`, `reserved_quantity`, `average_cost`, `last_purchase_cost`, `updated_at`) VALUES ('53', '20', '1', '58.5000', '0.0000', '112.86', '110.00', '2026-09-09 06:06:27');
INSERT INTO `inventory_stock` (`id`, `ingredient_id`, `location_id`, `quantity_on_hand`, `reserved_quantity`, `average_cost`, `last_purchase_cost`, `updated_at`) VALUES ('57', '20', '2', '10.0000', '0.0000', '112.86', '0.00', '2026-09-09 06:06:27');

-- Table structure for `inventory_transactions`
DROP TABLE IF EXISTS `inventory_transactions`;
CREATE TABLE `inventory_transactions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` int(10) unsigned NOT NULL DEFAULT 1,
  `ingredient_id` int(10) unsigned NOT NULL,
  `location_id` int(10) unsigned DEFAULT NULL,
  `type` enum('PURCHASE_RECEIPT','ORDER_CONSUMPTION','REVERSAL','WASTAGE','ADJUSTMENT_IN','ADJUSTMENT_OUT','TRANSFER_IN','TRANSFER_OUT','OPENING_BALANCE','purchase','adjustment','recipe_deduction') NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `unit_cost` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_cost` decimal(12,2) NOT NULL DEFAULT 0.00,
  `balance_before` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `balance_after` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `reference_type` varchar(50) DEFAULT NULL,
  `reference_id` int(10) unsigned DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_invtx_branch_created` (`branch_id`,`created_at`),
  KEY `idx_invtx_ing_type` (`ingredient_id`,`type`,`created_at`),
  KEY `idx_inv_tx_item_loc` (`ingredient_id`,`location_id`,`created_at`),
  CONSTRAINT `inventory_transactions_ibfk_1` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for `inventory_transactions`
INSERT INTO `inventory_transactions` (`id`, `branch_id`, `ingredient_id`, `location_id`, `type`, `quantity`, `unit_cost`, `total_cost`, `balance_before`, `balance_after`, `reference_type`, `reference_id`, `created_by`, `notes`, `created_at`) VALUES ('1', '1', '3', '1', 'ADJUSTMENT_IN', '20.00', '120.00', '2400.00', '0.0000', '20.0000', 'manual_adjustment', NULL, '1', '[Reason: Initial Opening Balance Audit] Opening balance setup', '2026-09-09 06:02:59');
INSERT INTO `inventory_transactions` (`id`, `branch_id`, `ingredient_id`, `location_id`, `type`, `quantity`, `unit_cost`, `total_cost`, `balance_before`, `balance_after`, `reference_type`, `reference_id`, `created_by`, `notes`, `created_at`) VALUES ('2', '1', '4', '1', 'ADJUSTMENT_IN', '20.00', '120.00', '2400.00', '0.0000', '20.0000', 'manual_adjustment', NULL, '1', '[Reason: Initial Opening Balance Audit] Opening balance setup', '2026-09-09 06:03:28');
INSERT INTO `inventory_transactions` (`id`, `branch_id`, `ingredient_id`, `location_id`, `type`, `quantity`, `unit_cost`, `total_cost`, `balance_before`, `balance_after`, `reference_type`, `reference_id`, `created_by`, `notes`, `created_at`) VALUES ('3', '1', '5', '1', 'ADJUSTMENT_IN', '20.00', '120.00', '2400.00', '0.0000', '20.0000', 'manual_adjustment', NULL, '1', '[Reason: Initial Opening Balance Audit] Opening balance setup', '2026-09-09 06:03:50');
INSERT INTO `inventory_transactions` (`id`, `branch_id`, `ingredient_id`, `location_id`, `type`, `quantity`, `unit_cost`, `total_cost`, `balance_before`, `balance_after`, `reference_type`, `reference_id`, `created_by`, `notes`, `created_at`) VALUES ('4', '1', '6', '1', 'ADJUSTMENT_IN', '20.00', '120.00', '2400.00', '0.0000', '20.0000', 'manual_adjustment', NULL, '1', '[Reason: Initial Opening Balance Audit] Opening balance setup', '2026-09-09 06:04:00');
INSERT INTO `inventory_transactions` (`id`, `branch_id`, `ingredient_id`, `location_id`, `type`, `quantity`, `unit_cost`, `total_cost`, `balance_before`, `balance_after`, `reference_type`, `reference_id`, `created_by`, `notes`, `created_at`) VALUES ('5', '1', '6', '1', 'PURCHASE_RECEIPT', '50.00', '110.00', '5500.00', '20.0000', '70.0000', 'goods_receipts', '1', '1', 'Received GRN #GRN-20260909-5183 for PO #PO-20260909-5931', '2026-09-09 06:04:00');
INSERT INTO `inventory_transactions` (`id`, `branch_id`, `ingredient_id`, `location_id`, `type`, `quantity`, `unit_cost`, `total_cost`, `balance_before`, `balance_after`, `reference_type`, `reference_id`, `created_by`, `notes`, `created_at`) VALUES ('6', '1', '7', '1', 'ADJUSTMENT_IN', '20.00', '120.00', '2400.00', '0.0000', '20.0000', 'manual_adjustment', NULL, '1', '[Reason: Initial Opening Balance Audit] Opening balance setup', '2026-09-09 06:04:08');
INSERT INTO `inventory_transactions` (`id`, `branch_id`, `ingredient_id`, `location_id`, `type`, `quantity`, `unit_cost`, `total_cost`, `balance_before`, `balance_after`, `reference_type`, `reference_id`, `created_by`, `notes`, `created_at`) VALUES ('7', '1', '7', '1', 'PURCHASE_RECEIPT', '50.00', '110.00', '5500.00', '20.0000', '70.0000', 'goods_receipts', '2', '1', 'Received GRN #GRN-20260909-5451 for PO #PO-20260909-1769', '2026-09-09 06:04:08');
INSERT INTO `inventory_transactions` (`id`, `branch_id`, `ingredient_id`, `location_id`, `type`, `quantity`, `unit_cost`, `total_cost`, `balance_before`, `balance_after`, `reference_type`, `reference_id`, `created_by`, `notes`, `created_at`) VALUES ('8', '1', '8', '1', 'ADJUSTMENT_IN', '20.00', '120.00', '2400.00', '0.0000', '20.0000', 'manual_adjustment', NULL, '1', '[Reason: Initial Opening Balance Audit] Opening balance setup', '2026-09-09 06:04:26');
INSERT INTO `inventory_transactions` (`id`, `branch_id`, `ingredient_id`, `location_id`, `type`, `quantity`, `unit_cost`, `total_cost`, `balance_before`, `balance_after`, `reference_type`, `reference_id`, `created_by`, `notes`, `created_at`) VALUES ('9', '1', '8', '1', 'PURCHASE_RECEIPT', '50.00', '110.00', '5500.00', '20.0000', '70.0000', 'goods_receipts', '3', '1', 'Received GRN #GRN-20260909-9573 for PO #PO-20260909-8246', '2026-09-09 06:04:26');
INSERT INTO `inventory_transactions` (`id`, `branch_id`, `ingredient_id`, `location_id`, `type`, `quantity`, `unit_cost`, `total_cost`, `balance_before`, `balance_after`, `reference_type`, `reference_id`, `created_by`, `notes`, `created_at`) VALUES ('10', '1', '9', '1', 'ADJUSTMENT_IN', '20.00', '120.00', '2400.00', '0.0000', '20.0000', 'manual_adjustment', NULL, '1', '[Reason: Initial Opening Balance Audit] Opening balance setup', '2026-09-09 06:04:35');
INSERT INTO `inventory_transactions` (`id`, `branch_id`, `ingredient_id`, `location_id`, `type`, `quantity`, `unit_cost`, `total_cost`, `balance_before`, `balance_after`, `reference_type`, `reference_id`, `created_by`, `notes`, `created_at`) VALUES ('11', '1', '9', '1', 'PURCHASE_RECEIPT', '50.00', '110.00', '5500.00', '20.0000', '70.0000', 'goods_receipts', '4', '1', 'Received GRN #GRN-20260909-6174 for PO #PO-20260909-8070', '2026-09-09 06:04:35');
INSERT INTO `inventory_transactions` (`id`, `branch_id`, `ingredient_id`, `location_id`, `type`, `quantity`, `unit_cost`, `total_cost`, `balance_before`, `balance_after`, `reference_type`, `reference_id`, `created_by`, `notes`, `created_at`) VALUES ('12', '1', '10', '1', 'ADJUSTMENT_IN', '20.00', '120.00', '2400.00', '0.0000', '20.0000', 'manual_adjustment', NULL, '1', '[Reason: Initial Opening Balance Audit] Opening balance setup', '2026-09-09 06:04:43');
INSERT INTO `inventory_transactions` (`id`, `branch_id`, `ingredient_id`, `location_id`, `type`, `quantity`, `unit_cost`, `total_cost`, `balance_before`, `balance_after`, `reference_type`, `reference_id`, `created_by`, `notes`, `created_at`) VALUES ('13', '1', '10', '1', 'PURCHASE_RECEIPT', '50.00', '110.00', '5500.00', '20.0000', '70.0000', 'goods_receipts', '5', '1', 'Received GRN #GRN-20260909-8859 for PO #PO-20260909-7475', '2026-09-09 06:04:43');
INSERT INTO `inventory_transactions` (`id`, `branch_id`, `ingredient_id`, `location_id`, `type`, `quantity`, `unit_cost`, `total_cost`, `balance_before`, `balance_after`, `reference_type`, `reference_id`, `created_by`, `notes`, `created_at`) VALUES ('14', '1', '11', '1', 'ADJUSTMENT_IN', '20.00', '120.00', '2400.00', '0.0000', '20.0000', 'manual_adjustment', NULL, '1', '[Reason: Initial Opening Balance Audit] Opening balance setup', '2026-09-09 06:04:55');
INSERT INTO `inventory_transactions` (`id`, `branch_id`, `ingredient_id`, `location_id`, `type`, `quantity`, `unit_cost`, `total_cost`, `balance_before`, `balance_after`, `reference_type`, `reference_id`, `created_by`, `notes`, `created_at`) VALUES ('15', '1', '11', '1', 'PURCHASE_RECEIPT', '50.00', '110.00', '5500.00', '20.0000', '70.0000', 'goods_receipts', '6', '1', 'Received GRN #GRN-20260909-6859 for PO #PO-20260909-2668', '2026-09-09 06:04:55');
INSERT INTO `inventory_transactions` (`id`, `branch_id`, `ingredient_id`, `location_id`, `type`, `quantity`, `unit_cost`, `total_cost`, `balance_before`, `balance_after`, `reference_type`, `reference_id`, `created_by`, `notes`, `created_at`) VALUES ('16', '1', '12', '1', 'ADJUSTMENT_IN', '20.00', '120.00', '2400.00', '0.0000', '20.0000', 'manual_adjustment', NULL, '1', '[Reason: Initial Opening Balance Audit] Opening balance setup', '2026-09-09 06:05:06');
INSERT INTO `inventory_transactions` (`id`, `branch_id`, `ingredient_id`, `location_id`, `type`, `quantity`, `unit_cost`, `total_cost`, `balance_before`, `balance_after`, `reference_type`, `reference_id`, `created_by`, `notes`, `created_at`) VALUES ('17', '1', '12', '1', 'PURCHASE_RECEIPT', '50.00', '110.00', '5500.00', '20.0000', '70.0000', 'goods_receipts', '7', '1', 'Received GRN #GRN-20260909-6300 for PO #PO-20260909-8780', '2026-09-09 06:05:06');
INSERT INTO `inventory_transactions` (`id`, `branch_id`, `ingredient_id`, `location_id`, `type`, `quantity`, `unit_cost`, `total_cost`, `balance_before`, `balance_after`, `reference_type`, `reference_id`, `created_by`, `notes`, `created_at`) VALUES ('18', '1', '13', '1', 'ADJUSTMENT_IN', '20.00', '120.00', '2400.00', '0.0000', '20.0000', 'manual_adjustment', NULL, '1', '[Reason: Initial Opening Balance Audit] Opening balance setup', '2026-09-09 06:05:15');
INSERT INTO `inventory_transactions` (`id`, `branch_id`, `ingredient_id`, `location_id`, `type`, `quantity`, `unit_cost`, `total_cost`, `balance_before`, `balance_after`, `reference_type`, `reference_id`, `created_by`, `notes`, `created_at`) VALUES ('19', '1', '13', '1', 'PURCHASE_RECEIPT', '50.00', '110.00', '5500.00', '20.0000', '70.0000', 'goods_receipts', '8', '1', 'Received GRN #GRN-20260909-7175 for PO #PO-20260909-9955', '2026-09-09 06:05:15');
INSERT INTO `inventory_transactions` (`id`, `branch_id`, `ingredient_id`, `location_id`, `type`, `quantity`, `unit_cost`, `total_cost`, `balance_before`, `balance_after`, `reference_type`, `reference_id`, `created_by`, `notes`, `created_at`) VALUES ('20', '1', '14', '1', 'ADJUSTMENT_IN', '20.00', '120.00', '2400.00', '0.0000', '20.0000', 'manual_adjustment', NULL, '1', '[Reason: Initial Opening Balance Audit] Opening balance setup', '2026-09-09 06:05:23');
INSERT INTO `inventory_transactions` (`id`, `branch_id`, `ingredient_id`, `location_id`, `type`, `quantity`, `unit_cost`, `total_cost`, `balance_before`, `balance_after`, `reference_type`, `reference_id`, `created_by`, `notes`, `created_at`) VALUES ('21', '1', '14', '1', 'PURCHASE_RECEIPT', '50.00', '110.00', '5500.00', '20.0000', '70.0000', 'goods_receipts', '9', '1', 'Received GRN #GRN-20260909-5318 for PO #PO-20260909-8592', '2026-09-09 06:05:23');
INSERT INTO `inventory_transactions` (`id`, `branch_id`, `ingredient_id`, `location_id`, `type`, `quantity`, `unit_cost`, `total_cost`, `balance_before`, `balance_after`, `reference_type`, `reference_id`, `created_by`, `notes`, `created_at`) VALUES ('22', '1', '15', '1', 'ADJUSTMENT_IN', '20.00', '120.00', '2400.00', '0.0000', '20.0000', 'manual_adjustment', NULL, '1', '[Reason: Initial Opening Balance Audit] Opening balance setup', '2026-09-09 06:05:31');
INSERT INTO `inventory_transactions` (`id`, `branch_id`, `ingredient_id`, `location_id`, `type`, `quantity`, `unit_cost`, `total_cost`, `balance_before`, `balance_after`, `reference_type`, `reference_id`, `created_by`, `notes`, `created_at`) VALUES ('23', '1', '15', '1', 'PURCHASE_RECEIPT', '50.00', '110.00', '5500.00', '20.0000', '70.0000', 'goods_receipts', '10', '1', 'Received GRN #GRN-20260909-4955 for PO #PO-20260909-3565', '2026-09-09 06:05:31');
INSERT INTO `inventory_transactions` (`id`, `branch_id`, `ingredient_id`, `location_id`, `type`, `quantity`, `unit_cost`, `total_cost`, `balance_before`, `balance_after`, `reference_type`, `reference_id`, `created_by`, `notes`, `created_at`) VALUES ('24', '1', '16', '1', 'ADJUSTMENT_IN', '20.00', '120.00', '2400.00', '0.0000', '20.0000', 'manual_adjustment', NULL, '1', '[Reason: Initial Opening Balance Audit] Opening balance setup', '2026-09-09 06:05:43');
INSERT INTO `inventory_transactions` (`id`, `branch_id`, `ingredient_id`, `location_id`, `type`, `quantity`, `unit_cost`, `total_cost`, `balance_before`, `balance_after`, `reference_type`, `reference_id`, `created_by`, `notes`, `created_at`) VALUES ('25', '1', '16', '1', 'PURCHASE_RECEIPT', '50.00', '110.00', '5500.00', '20.0000', '70.0000', 'goods_receipts', '11', '1', 'Received GRN #GRN-20260909-9809 for PO #PO-20260909-6227', '2026-09-09 06:05:43');
INSERT INTO `inventory_transactions` (`id`, `branch_id`, `ingredient_id`, `location_id`, `type`, `quantity`, `unit_cost`, `total_cost`, `balance_before`, `balance_after`, `reference_type`, `reference_id`, `created_by`, `notes`, `created_at`) VALUES ('26', '1', '16', '1', 'ORDER_CONSUMPTION', '-0.60', '112.86', '67.72', '70.0000', '69.4000', 'order_items', '38', '1', 'Recipe consumption for Ticket #TCK-TEST-INV-873 (2x Chef Special Burger)', '2026-09-09 06:05:43');
INSERT INTO `inventory_transactions` (`id`, `branch_id`, `ingredient_id`, `location_id`, `type`, `quantity`, `unit_cost`, `total_cost`, `balance_before`, `balance_after`, `reference_type`, `reference_id`, `created_by`, `notes`, `created_at`) VALUES ('27', '1', '17', '1', 'ADJUSTMENT_IN', '20.00', '120.00', '2400.00', '0.0000', '20.0000', 'manual_adjustment', NULL, '1', '[Reason: Initial Opening Balance Audit] Opening balance setup', '2026-09-09 06:05:58');
INSERT INTO `inventory_transactions` (`id`, `branch_id`, `ingredient_id`, `location_id`, `type`, `quantity`, `unit_cost`, `total_cost`, `balance_before`, `balance_after`, `reference_type`, `reference_id`, `created_by`, `notes`, `created_at`) VALUES ('28', '1', '18', '1', 'ADJUSTMENT_IN', '20.00', '120.00', '2400.00', '0.0000', '20.0000', 'manual_adjustment', NULL, '1', '[Reason: Initial Opening Balance Audit] Opening balance setup', '2026-09-09 06:06:06');
INSERT INTO `inventory_transactions` (`id`, `branch_id`, `ingredient_id`, `location_id`, `type`, `quantity`, `unit_cost`, `total_cost`, `balance_before`, `balance_after`, `reference_type`, `reference_id`, `created_by`, `notes`, `created_at`) VALUES ('29', '1', '18', '1', 'PURCHASE_RECEIPT', '50.00', '110.00', '5500.00', '20.0000', '70.0000', 'goods_receipts', '12', '1', 'Received GRN #GRN-20260909-7062 for PO #PO-20260909-5611', '2026-09-09 06:06:06');
INSERT INTO `inventory_transactions` (`id`, `branch_id`, `ingredient_id`, `location_id`, `type`, `quantity`, `unit_cost`, `total_cost`, `balance_before`, `balance_after`, `reference_type`, `reference_id`, `created_by`, `notes`, `created_at`) VALUES ('30', '1', '18', '1', 'ORDER_CONSUMPTION', '-0.60', '112.86', '67.72', '70.0000', '69.4000', 'order_items', '39', '1', 'Recipe consumption for Ticket #TCK-TEST-INV-645 (2x Chef Special Burger)', '2026-09-09 06:06:06');
INSERT INTO `inventory_transactions` (`id`, `branch_id`, `ingredient_id`, `location_id`, `type`, `quantity`, `unit_cost`, `total_cost`, `balance_before`, `balance_after`, `reference_type`, `reference_id`, `created_by`, `notes`, `created_at`) VALUES ('31', '1', '19', '1', 'ADJUSTMENT_IN', '20.00', '120.00', '2400.00', '0.0000', '20.0000', 'manual_adjustment', NULL, '1', '[Reason: Initial Opening Balance Audit] Opening balance setup', '2026-09-09 06:06:20');
INSERT INTO `inventory_transactions` (`id`, `branch_id`, `ingredient_id`, `location_id`, `type`, `quantity`, `unit_cost`, `total_cost`, `balance_before`, `balance_after`, `reference_type`, `reference_id`, `created_by`, `notes`, `created_at`) VALUES ('32', '1', '19', '1', 'PURCHASE_RECEIPT', '50.00', '110.00', '5500.00', '20.0000', '70.0000', 'goods_receipts', '13', '1', 'Received GRN #GRN-20260909-1332 for PO #PO-20260909-6324', '2026-09-09 06:06:20');
INSERT INTO `inventory_transactions` (`id`, `branch_id`, `ingredient_id`, `location_id`, `type`, `quantity`, `unit_cost`, `total_cost`, `balance_before`, `balance_after`, `reference_type`, `reference_id`, `created_by`, `notes`, `created_at`) VALUES ('33', '1', '19', '1', 'ORDER_CONSUMPTION', '-0.60', '112.86', '67.72', '70.0000', '69.4000', 'order_items', '40', '1', 'Recipe consumption for Ticket #TCK-TEST-INV-795 (2x Chef Special Burger)', '2026-09-09 06:06:20');
INSERT INTO `inventory_transactions` (`id`, `branch_id`, `ingredient_id`, `location_id`, `type`, `quantity`, `unit_cost`, `total_cost`, `balance_before`, `balance_after`, `reference_type`, `reference_id`, `created_by`, `notes`, `created_at`) VALUES ('34', '1', '19', '1', 'WASTAGE', '-1.50', '112.86', '169.29', '69.4000', '67.9000', 'wastage_records', '1', '1', '[Wastage Reason: Kitchen Spoilage / Mold] Humid storage damage', '2026-09-09 06:06:20');
INSERT INTO `inventory_transactions` (`id`, `branch_id`, `ingredient_id`, `location_id`, `type`, `quantity`, `unit_cost`, `total_cost`, `balance_before`, `balance_after`, `reference_type`, `reference_id`, `created_by`, `notes`, `created_at`) VALUES ('35', '1', '19', '1', 'TRANSFER_OUT', '-10.00', '112.86', '1128.60', '67.9000', '57.9000', 'stock_transfers', '1', '1', 'Transferred to Location #2 (TRF-20260909-7785)', '2026-09-09 06:06:20');
INSERT INTO `inventory_transactions` (`id`, `branch_id`, `ingredient_id`, `location_id`, `type`, `quantity`, `unit_cost`, `total_cost`, `balance_before`, `balance_after`, `reference_type`, `reference_id`, `created_by`, `notes`, `created_at`) VALUES ('36', '1', '19', '2', 'TRANSFER_IN', '10.00', '112.86', '1128.60', '0.0000', '10.0000', 'stock_transfers', '1', '1', 'Transferred from Location #1 (TRF-20260909-7785)', '2026-09-09 06:06:20');
INSERT INTO `inventory_transactions` (`id`, `branch_id`, `ingredient_id`, `location_id`, `type`, `quantity`, `unit_cost`, `total_cost`, `balance_before`, `balance_after`, `reference_type`, `reference_id`, `created_by`, `notes`, `created_at`) VALUES ('37', '1', '19', '1', 'REVERSAL', '0.60', '112.86', '67.72', '57.9000', '58.5000', 'orders', '34', '1', 'Reversal for Order #34 (Customer returned order)', '2026-09-09 06:06:20');
INSERT INTO `inventory_transactions` (`id`, `branch_id`, `ingredient_id`, `location_id`, `type`, `quantity`, `unit_cost`, `total_cost`, `balance_before`, `balance_after`, `reference_type`, `reference_id`, `created_by`, `notes`, `created_at`) VALUES ('38', '1', '20', '1', 'ADJUSTMENT_IN', '20.00', '120.00', '2400.00', '0.0000', '20.0000', 'manual_adjustment', NULL, '1', '[Reason: Initial Opening Balance Audit] Opening balance setup', '2026-09-09 06:06:26');
INSERT INTO `inventory_transactions` (`id`, `branch_id`, `ingredient_id`, `location_id`, `type`, `quantity`, `unit_cost`, `total_cost`, `balance_before`, `balance_after`, `reference_type`, `reference_id`, `created_by`, `notes`, `created_at`) VALUES ('39', '1', '20', '1', 'PURCHASE_RECEIPT', '50.00', '110.00', '5500.00', '20.0000', '70.0000', 'goods_receipts', '14', '1', 'Received GRN #GRN-20260909-4900 for PO #PO-20260909-7205', '2026-09-09 06:06:26');
INSERT INTO `inventory_transactions` (`id`, `branch_id`, `ingredient_id`, `location_id`, `type`, `quantity`, `unit_cost`, `total_cost`, `balance_before`, `balance_after`, `reference_type`, `reference_id`, `created_by`, `notes`, `created_at`) VALUES ('40', '1', '20', '1', 'ORDER_CONSUMPTION', '-0.60', '112.86', '67.72', '70.0000', '69.4000', 'order_items', '41', '1', 'Recipe consumption for Ticket #TCK-TEST-INV-837 (2x Chef Special Burger)', '2026-09-09 06:06:27');
INSERT INTO `inventory_transactions` (`id`, `branch_id`, `ingredient_id`, `location_id`, `type`, `quantity`, `unit_cost`, `total_cost`, `balance_before`, `balance_after`, `reference_type`, `reference_id`, `created_by`, `notes`, `created_at`) VALUES ('41', '1', '20', '1', 'WASTAGE', '-1.50', '112.86', '169.29', '69.4000', '67.9000', 'wastage_records', '2', '1', '[Wastage Reason: Kitchen Spoilage / Mold] Humid storage damage', '2026-09-09 06:06:27');
INSERT INTO `inventory_transactions` (`id`, `branch_id`, `ingredient_id`, `location_id`, `type`, `quantity`, `unit_cost`, `total_cost`, `balance_before`, `balance_after`, `reference_type`, `reference_id`, `created_by`, `notes`, `created_at`) VALUES ('42', '1', '20', '1', 'TRANSFER_OUT', '-10.00', '112.86', '1128.60', '67.9000', '57.9000', 'stock_transfers', '2', '1', 'Transferred to Location #2 (TRF-20260909-2944)', '2026-09-09 06:06:27');
INSERT INTO `inventory_transactions` (`id`, `branch_id`, `ingredient_id`, `location_id`, `type`, `quantity`, `unit_cost`, `total_cost`, `balance_before`, `balance_after`, `reference_type`, `reference_id`, `created_by`, `notes`, `created_at`) VALUES ('43', '1', '20', '2', 'TRANSFER_IN', '10.00', '112.86', '1128.60', '0.0000', '10.0000', 'stock_transfers', '2', '1', 'Transferred from Location #1 (TRF-20260909-2944)', '2026-09-09 06:06:27');
INSERT INTO `inventory_transactions` (`id`, `branch_id`, `ingredient_id`, `location_id`, `type`, `quantity`, `unit_cost`, `total_cost`, `balance_before`, `balance_after`, `reference_type`, `reference_id`, `created_by`, `notes`, `created_at`) VALUES ('44', '1', '20', '1', 'REVERSAL', '0.60', '112.86', '67.72', '57.9000', '58.5000', 'orders', '35', '1', 'Reversal for Order #35 (Customer returned order)', '2026-09-09 06:06:27');

-- Table structure for `invoices`
DROP TABLE IF EXISTS `invoices`;
CREATE TABLE `invoices` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_number` varchar(40) NOT NULL,
  `branch_id` int(10) unsigned NOT NULL DEFAULT 1,
  `order_id` int(10) unsigned NOT NULL,
  `dining_session_id` int(10) unsigned DEFAULT NULL,
  `customer_id` int(10) unsigned DEFAULT NULL,
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `tax` decimal(12,2) NOT NULL DEFAULT 0.00,
  `service_charge` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `paid_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `outstanding_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status` enum('UNPAID','PARTIAL','PAID','CANCELLED') DEFAULT 'UNPAID',
  `invoice_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoice_number` (`invoice_number`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `invoices`
INSERT INTO `invoices` (`id`, `invoice_number`, `branch_id`, `order_id`, `dining_session_id`, `customer_id`, `subtotal`, `discount`, `tax`, `service_charge`, `total`, `paid_amount`, `outstanding_amount`, `status`, `invoice_data`, `created_at`, `updated_at`) VALUES ('1', 'INV-2026-562019', '1', '13', NULL, NULL, '500.00', '0.00', '25.00', '0.00', '525.00', '525.00', '0.00', 'PAID', '{\"invoice_number\":\"INV-2026-562019\",\"order_number\":\"TEST-ORD-1788911203\",\"branch_name\":\"Main Outlet\",\"table_number\":\"N\\/A\",\"customer_name\":\"Guest\",\"waiter_name\":\"System Administrator\",\"order_type\":\"DINE_IN\",\"order_status\":\"SERVED\",\"payment_status\":\"PAID\",\"items\":[{\"name\":\"Test Item 100\",\"quantity\":2,\"unit_price\":\"250.00\",\"subtotal\":\"500.00\"}],\"financials\":{\"subtotal\":500,\"discount\":0,\"tax\":25,\"service_charge\":0,\"grand_total\":525,\"gross_paid\":525,\"total_refunded\":0,\"net_paid\":525,\"outstanding_balance\":0},\"timestamp\":\"2026-09-08 23:46:43\"}', '2026-09-09 05:46:43', '2026-09-09 05:46:43');
INSERT INTO `invoices` (`id`, `invoice_number`, `branch_id`, `order_id`, `dining_session_id`, `customer_id`, `subtotal`, `discount`, `tax`, `service_charge`, `total`, `paid_amount`, `outstanding_amount`, `status`, `invoice_data`, `created_at`, `updated_at`) VALUES ('2', 'INV-2026-190507', '1', '14', NULL, NULL, '500.00', '0.00', '25.00', '0.00', '525.00', '525.00', '0.00', 'PAID', '{\"invoice_number\":\"INV-2026-190507\",\"order_number\":\"TEST-ORD-1788911215\",\"branch_name\":\"Main Outlet\",\"table_number\":\"N\\/A\",\"customer_name\":\"Guest\",\"waiter_name\":\"System Administrator\",\"order_type\":\"DINE_IN\",\"order_status\":\"SERVED\",\"payment_status\":\"PAID\",\"items\":[{\"name\":\"Test Item 100\",\"quantity\":2,\"unit_price\":\"250.00\",\"subtotal\":\"500.00\"}],\"financials\":{\"subtotal\":500,\"discount\":0,\"tax\":25,\"service_charge\":0,\"grand_total\":525,\"gross_paid\":525,\"total_refunded\":0,\"net_paid\":525,\"outstanding_balance\":0},\"timestamp\":\"2026-09-08 23:46:55\"}', '2026-09-09 05:46:55', '2026-09-09 05:46:55');
INSERT INTO `invoices` (`id`, `invoice_number`, `branch_id`, `order_id`, `dining_session_id`, `customer_id`, `subtotal`, `discount`, `tax`, `service_charge`, `total`, `paid_amount`, `outstanding_amount`, `status`, `invoice_data`, `created_at`, `updated_at`) VALUES ('3', 'INV-2026-868908', '1', '15', NULL, NULL, '500.00', '0.00', '25.00', '0.00', '525.00', '525.00', '0.00', 'PAID', '{\"invoice_number\":\"INV-2026-868908\",\"order_number\":\"TEST-ORD-1788911222\",\"branch_name\":\"Main Outlet\",\"table_number\":\"N\\/A\",\"customer_name\":\"Guest\",\"waiter_name\":\"System Administrator\",\"order_type\":\"DINE_IN\",\"order_status\":\"SERVED\",\"payment_status\":\"PAID\",\"items\":[{\"name\":\"Test Item 100\",\"quantity\":2,\"unit_price\":\"250.00\",\"subtotal\":\"500.00\"}],\"financials\":{\"subtotal\":500,\"discount\":0,\"tax\":25,\"service_charge\":0,\"grand_total\":525,\"gross_paid\":525,\"total_refunded\":0,\"net_paid\":525,\"outstanding_balance\":0},\"timestamp\":\"2026-09-08 23:47:02\"}', '2026-09-09 05:47:02', '2026-09-09 05:47:02');

-- Table structure for `login_attempts`
DROP TABLE IF EXISTS `login_attempts`;
CREATE TABLE `login_attempts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `identifier` varchar(120) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `attempted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_rate_limit` (`ip_address`,`identifier`,`attempted_at`),
  KEY `idx_login_ip_time` (`ip_address`,`attempted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for `loyalty_accounts`
DROP TABLE IF EXISTS `loyalty_accounts`;
CREATE TABLE `loyalty_accounts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int(10) unsigned NOT NULL,
  `branch_id` int(10) unsigned DEFAULT NULL,
  `points_balance` decimal(12,2) NOT NULL DEFAULT 0.00,
  `lifetime_earned` decimal(12,2) NOT NULL DEFAULT 0.00,
  `lifetime_redeemed` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status` enum('ACTIVE','SUSPENDED') DEFAULT 'ACTIVE',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_loyalty_cust` (`customer_id`),
  KEY `idx_loyalty_branch` (`branch_id`),
  CONSTRAINT `fk_loyalty_cust` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `loyalty_accounts`
INSERT INTO `loyalty_accounts` (`id`, `customer_id`, `branch_id`, `points_balance`, `lifetime_earned`, `lifetime_redeemed`, `status`, `created_at`, `updated_at`) VALUES ('1', '1', '1', '0.00', '0.00', '0.00', 'ACTIVE', '2026-09-09 06:52:08', NULL);
INSERT INTO `loyalty_accounts` (`id`, `customer_id`, `branch_id`, `points_balance`, `lifetime_earned`, `lifetime_redeemed`, `status`, `created_at`, `updated_at`) VALUES ('2', '2', '1', '0.00', '0.00', '0.00', 'ACTIVE', '2026-09-09 06:52:16', NULL);
INSERT INTO `loyalty_accounts` (`id`, `customer_id`, `branch_id`, `points_balance`, `lifetime_earned`, `lifetime_redeemed`, `status`, `created_at`, `updated_at`) VALUES ('3', '3', '1', '0.00', '0.00', '0.00', 'ACTIVE', '2026-09-09 06:52:41', NULL);
INSERT INTO `loyalty_accounts` (`id`, `customer_id`, `branch_id`, `points_balance`, `lifetime_earned`, `lifetime_redeemed`, `status`, `created_at`, `updated_at`) VALUES ('4', '4', '1', '52.40', '2.40', '0.00', 'ACTIVE', '2026-09-09 06:52:51', '2026-09-09 06:52:51');
INSERT INTO `loyalty_accounts` (`id`, `customer_id`, `branch_id`, `points_balance`, `lifetime_earned`, `lifetime_redeemed`, `status`, `created_at`, `updated_at`) VALUES ('5', '5', '1', '52.40', '2.40', '0.00', 'ACTIVE', '2026-09-09 06:53:00', '2026-09-09 06:53:01');
INSERT INTO `loyalty_accounts` (`id`, `customer_id`, `branch_id`, `points_balance`, `lifetime_earned`, `lifetime_redeemed`, `status`, `created_at`, `updated_at`) VALUES ('6', '6', '1', '52.40', '2.40', '0.00', 'ACTIVE', '2026-09-09 06:53:19', '2026-09-09 06:53:19');
INSERT INTO `loyalty_accounts` (`id`, `customer_id`, `branch_id`, `points_balance`, `lifetime_earned`, `lifetime_redeemed`, `status`, `created_at`, `updated_at`) VALUES ('7', '7', '1', '52.40', '2.40', '0.00', 'ACTIVE', '2026-09-09 06:53:45', '2026-09-09 06:53:45');
INSERT INTO `loyalty_accounts` (`id`, `customer_id`, `branch_id`, `points_balance`, `lifetime_earned`, `lifetime_redeemed`, `status`, `created_at`, `updated_at`) VALUES ('8', '8', '1', '0.00', '0.00', '0.00', 'ACTIVE', '2026-09-09 06:53:45', NULL);
INSERT INTO `loyalty_accounts` (`id`, `customer_id`, `branch_id`, `points_balance`, `lifetime_earned`, `lifetime_redeemed`, `status`, `created_at`, `updated_at`) VALUES ('9', '9', '1', '52.40', '2.40', '0.00', 'ACTIVE', '2026-09-09 06:53:54', '2026-09-09 06:53:54');
INSERT INTO `loyalty_accounts` (`id`, `customer_id`, `branch_id`, `points_balance`, `lifetime_earned`, `lifetime_redeemed`, `status`, `created_at`, `updated_at`) VALUES ('10', '10', '1', '0.00', '0.00', '0.00', 'ACTIVE', '2026-09-09 06:53:54', NULL);
INSERT INTO `loyalty_accounts` (`id`, `customer_id`, `branch_id`, `points_balance`, `lifetime_earned`, `lifetime_redeemed`, `status`, `created_at`, `updated_at`) VALUES ('11', '11', '1', '52.40', '2.40', '0.00', 'ACTIVE', '2026-09-09 06:54:03', '2026-09-09 06:54:03');
INSERT INTO `loyalty_accounts` (`id`, `customer_id`, `branch_id`, `points_balance`, `lifetime_earned`, `lifetime_redeemed`, `status`, `created_at`, `updated_at`) VALUES ('12', '12', '1', '0.00', '0.00', '0.00', 'ACTIVE', '2026-09-09 06:54:03', NULL);

-- Table structure for `loyalty_transactions`
DROP TABLE IF EXISTS `loyalty_transactions`;
CREATE TABLE `loyalty_transactions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int(10) unsigned NOT NULL,
  `branch_id` int(10) unsigned DEFAULT NULL,
  `order_id` int(10) unsigned DEFAULT NULL,
  `type` enum('EARN','REDEEM','ADJUST','EXPIRE','REVERSE','BONUS') NOT NULL,
  `points` decimal(12,2) NOT NULL,
  `balance_before` decimal(12,2) NOT NULL,
  `balance_after` decimal(12,2) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_l_tx_cust` (`customer_id`),
  KEY `idx_l_tx_branch` (`branch_id`),
  KEY `idx_l_tx_order` (`order_id`),
  KEY `idx_l_tx_type` (`type`),
  KEY `idx_loyalty_tx_cust_type` (`customer_id`,`type`,`created_at`),
  CONSTRAINT `fk_l_tx_cust` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `loyalty_transactions`
INSERT INTO `loyalty_transactions` (`id`, `customer_id`, `branch_id`, `order_id`, `type`, `points`, `balance_before`, `balance_after`, `reason`, `created_by`, `created_at`) VALUES ('1', '4', '1', '36', 'EARN', '2.40', '0.00', '2.40', 'Earned points for Order #36', '1', '2026-09-09 06:52:51');
INSERT INTO `loyalty_transactions` (`id`, `customer_id`, `branch_id`, `order_id`, `type`, `points`, `balance_before`, `balance_after`, `reason`, `created_by`, `created_at`) VALUES ('2', '4', '1', NULL, 'ADJUST', '50.00', '2.40', '52.40', 'Manual adjustment: Automated test bonus credit', '1', '2026-09-09 06:52:51');
INSERT INTO `loyalty_transactions` (`id`, `customer_id`, `branch_id`, `order_id`, `type`, `points`, `balance_before`, `balance_after`, `reason`, `created_by`, `created_at`) VALUES ('3', '5', '1', '37', 'EARN', '2.40', '0.00', '2.40', 'Earned points for Order #37', '1', '2026-09-09 06:53:01');
INSERT INTO `loyalty_transactions` (`id`, `customer_id`, `branch_id`, `order_id`, `type`, `points`, `balance_before`, `balance_after`, `reason`, `created_by`, `created_at`) VALUES ('4', '5', '1', NULL, 'ADJUST', '50.00', '2.40', '52.40', 'Manual adjustment: Automated test bonus credit', '1', '2026-09-09 06:53:01');
INSERT INTO `loyalty_transactions` (`id`, `customer_id`, `branch_id`, `order_id`, `type`, `points`, `balance_before`, `balance_after`, `reason`, `created_by`, `created_at`) VALUES ('5', '6', '1', '38', 'EARN', '2.40', '0.00', '2.40', 'Earned points for Order #38', '1', '2026-09-09 06:53:19');
INSERT INTO `loyalty_transactions` (`id`, `customer_id`, `branch_id`, `order_id`, `type`, `points`, `balance_before`, `balance_after`, `reason`, `created_by`, `created_at`) VALUES ('6', '6', '1', NULL, 'ADJUST', '50.00', '2.40', '52.40', 'Manual adjustment: Automated test bonus credit', '1', '2026-09-09 06:53:19');
INSERT INTO `loyalty_transactions` (`id`, `customer_id`, `branch_id`, `order_id`, `type`, `points`, `balance_before`, `balance_after`, `reason`, `created_by`, `created_at`) VALUES ('7', '7', '1', '39', 'EARN', '2.40', '0.00', '2.40', 'Earned points for Order #39', '1', '2026-09-09 06:53:45');
INSERT INTO `loyalty_transactions` (`id`, `customer_id`, `branch_id`, `order_id`, `type`, `points`, `balance_before`, `balance_after`, `reason`, `created_by`, `created_at`) VALUES ('8', '7', '1', NULL, 'ADJUST', '50.00', '2.40', '52.40', 'Manual adjustment: Automated test bonus credit', '1', '2026-09-09 06:53:45');
INSERT INTO `loyalty_transactions` (`id`, `customer_id`, `branch_id`, `order_id`, `type`, `points`, `balance_before`, `balance_after`, `reason`, `created_by`, `created_at`) VALUES ('9', '9', '1', '41', 'EARN', '2.40', '0.00', '2.40', 'Earned points for Order #41', '1', '2026-09-09 06:53:54');
INSERT INTO `loyalty_transactions` (`id`, `customer_id`, `branch_id`, `order_id`, `type`, `points`, `balance_before`, `balance_after`, `reason`, `created_by`, `created_at`) VALUES ('10', '9', '1', NULL, 'ADJUST', '50.00', '2.40', '52.40', 'Manual adjustment: Automated test bonus credit', '1', '2026-09-09 06:53:54');
INSERT INTO `loyalty_transactions` (`id`, `customer_id`, `branch_id`, `order_id`, `type`, `points`, `balance_before`, `balance_after`, `reason`, `created_by`, `created_at`) VALUES ('11', '11', '1', '43', 'EARN', '2.40', '0.00', '2.40', 'Earned points for Order #43', '1', '2026-09-09 06:54:03');
INSERT INTO `loyalty_transactions` (`id`, `customer_id`, `branch_id`, `order_id`, `type`, `points`, `balance_before`, `balance_after`, `reason`, `created_by`, `created_at`) VALUES ('12', '11', '1', NULL, 'ADJUST', '50.00', '2.40', '52.40', 'Manual adjustment: Automated test bonus credit', '1', '2026-09-09 06:54:03');

-- Table structure for `menus`
DROP TABLE IF EXISTS `menus`;
CREATE TABLE `menus` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` int(10) unsigned NOT NULL DEFAULT 1,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `status` enum('ACTIVE','INACTIVE') DEFAULT 'ACTIVE',
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `branch_id` (`branch_id`),
  CONSTRAINT `menus_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for `modifier_groups`
DROP TABLE IF EXISTS `modifier_groups`;
CREATE TABLE `modifier_groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `min_selection` int(11) DEFAULT 0,
  `max_selection` int(11) DEFAULT 1,
  `is_required` tinyint(1) DEFAULT 0,
  `status` enum('ACTIVE','INACTIVE') DEFAULT 'ACTIVE',
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for `modifiers`
DROP TABLE IF EXISTS `modifiers`;
CREATE TABLE `modifiers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `modifier_group_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(80) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `price` decimal(12,2) DEFAULT 0.00,
  `status` enum('ACTIVE','INACTIVE') DEFAULT 'ACTIVE',
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for `order_item_consumptions`
DROP TABLE IF EXISTS `order_item_consumptions`;
CREATE TABLE `order_item_consumptions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_item_id` int(10) unsigned NOT NULL,
  `order_ticket_id` int(10) unsigned DEFAULT NULL,
  `recipe_id` int(10) unsigned DEFAULT NULL,
  `status` enum('CONSUMED','REVERSED') DEFAULT 'CONSUMED',
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_item_ticket` (`order_item_id`,`order_ticket_id`),
  CONSTRAINT `order_item_consumptions_ibfk_1` FOREIGN KEY (`order_item_id`) REFERENCES `order_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `order_item_consumptions`
INSERT INTO `order_item_consumptions` (`id`, `order_item_id`, `order_ticket_id`, `recipe_id`, `status`, `created_at`) VALUES ('1', '38', '28', '7', 'CONSUMED', '2026-09-09 06:05:43');
INSERT INTO `order_item_consumptions` (`id`, `order_item_id`, `order_ticket_id`, `recipe_id`, `status`, `created_at`) VALUES ('2', '39', '29', '8', 'CONSUMED', '2026-09-09 06:06:06');
INSERT INTO `order_item_consumptions` (`id`, `order_item_id`, `order_ticket_id`, `recipe_id`, `status`, `created_at`) VALUES ('3', '40', '30', '9', 'REVERSED', '2026-09-09 06:06:20');
INSERT INTO `order_item_consumptions` (`id`, `order_item_id`, `order_ticket_id`, `recipe_id`, `status`, `created_at`) VALUES ('4', '41', '31', '10', 'REVERSED', '2026-09-09 06:06:27');

-- Table structure for `order_item_modifiers`
DROP TABLE IF EXISTS `order_item_modifiers`;
CREATE TABLE `order_item_modifiers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_item_id` int(10) unsigned NOT NULL,
  `modifier_id` int(10) unsigned DEFAULT NULL,
  `modifier_name` varchar(80) NOT NULL,
  `unit_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `order_item_id` (`order_item_id`),
  CONSTRAINT `order_item_modifiers_ibfk_1` FOREIGN KEY (`order_item_id`) REFERENCES `order_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for `order_items`
DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned DEFAULT NULL,
  `station_id` int(10) unsigned DEFAULT NULL,
  `variant_id` int(10) unsigned DEFAULT NULL,
  `counter_id` int(10) unsigned NOT NULL,
  `item_name` varchar(120) NOT NULL,
  `variant_name` varchar(80) DEFAULT NULL,
  `sku` varchar(50) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `unit_price` decimal(12,2) NOT NULL,
  `modifier_total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `subtotal` decimal(12,2) NOT NULL,
  `status` enum('PENDING','ROUTED','PREPARING','READY','SERVED','CANCELLED') DEFAULT 'ROUTED',
  `notes` varchar(255) DEFAULT NULL,
  `routing_status` enum('UNROUTED','ROUTED','IN_PROGRESS','READY','SERVED','CANCELLED') DEFAULT 'UNROUTED',
  `routed_at` datetime DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_order_items_order_id` (`order_id`),
  KEY `counter_id` (`counter_id`),
  KEY `idx_order_items_order_prod` (`order_id`,`product_id`),
  KEY `idx_item_ord_status` (`order_id`,`status`),
  KEY `idx_item_product` (`product_id`,`status`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`counter_id`) REFERENCES `stations` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for `order_items`
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `station_id`, `variant_id`, `counter_id`, `item_name`, `variant_name`, `sku`, `quantity`, `unit_price`, `modifier_total`, `subtotal`, `status`, `notes`, `routing_status`, `routed_at`, `updated_at`) VALUES ('1', '3', '4', '1', NULL, '1', 'Chef Special Burger', NULL, 'TST-BGR', '1', '12.00', '0.00', '12.00', 'ROUTED', 'Prep note for Chef Special Burger', 'ROUTED', '2026-09-09 05:40:50', '2026-09-09 05:40:50');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `station_id`, `variant_id`, `counter_id`, `item_name`, `variant_name`, `sku`, `quantity`, `unit_price`, `modifier_total`, `subtotal`, `status`, `notes`, `routing_status`, `routed_at`, `updated_at`) VALUES ('2', '3', '5', '2', NULL, '2', 'Iced Americano', NULL, 'TST-COF', '1', '4.50', '0.00', '4.50', 'ROUTED', 'Prep note for Iced Americano', 'ROUTED', '2026-09-09 05:40:50', '2026-09-09 05:40:50');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `station_id`, `variant_id`, `counter_id`, `item_name`, `variant_name`, `sku`, `quantity`, `unit_price`, `modifier_total`, `subtotal`, `status`, `notes`, `routing_status`, `routed_at`, `updated_at`) VALUES ('3', '3', '6', '4', NULL, '4', 'Molten Chocolate Cake', NULL, 'TST-CAK', '1', '7.50', '0.00', '7.50', 'ROUTED', 'Prep note for Molten Chocolate Cake', 'ROUTED', '2026-09-09 05:40:50', '2026-09-09 05:40:50');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `station_id`, `variant_id`, `counter_id`, `item_name`, `variant_name`, `sku`, `quantity`, `unit_price`, `modifier_total`, `subtotal`, `status`, `notes`, `routing_status`, `routed_at`, `updated_at`) VALUES ('4', '4', '4', '1', NULL, '1', 'Chef Special Burger', NULL, 'TST-BGR', '1', '12.00', '0.00', '12.00', 'ROUTED', 'Prep note for Chef Special Burger', 'ROUTED', '2026-09-09 05:41:01', '2026-09-09 05:41:01');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `station_id`, `variant_id`, `counter_id`, `item_name`, `variant_name`, `sku`, `quantity`, `unit_price`, `modifier_total`, `subtotal`, `status`, `notes`, `routing_status`, `routed_at`, `updated_at`) VALUES ('5', '4', '5', '2', NULL, '2', 'Iced Americano', NULL, 'TST-COF', '1', '4.50', '0.00', '4.50', 'ROUTED', 'Prep note for Iced Americano', 'ROUTED', '2026-09-09 05:41:01', '2026-09-09 05:41:01');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `station_id`, `variant_id`, `counter_id`, `item_name`, `variant_name`, `sku`, `quantity`, `unit_price`, `modifier_total`, `subtotal`, `status`, `notes`, `routing_status`, `routed_at`, `updated_at`) VALUES ('6', '4', '6', '4', NULL, '4', 'Molten Chocolate Cake', NULL, 'TST-CAK', '1', '7.50', '0.00', '7.50', 'ROUTED', 'Prep note for Molten Chocolate Cake', 'ROUTED', '2026-09-09 05:41:01', '2026-09-09 05:41:01');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `station_id`, `variant_id`, `counter_id`, `item_name`, `variant_name`, `sku`, `quantity`, `unit_price`, `modifier_total`, `subtotal`, `status`, `notes`, `routing_status`, `routed_at`, `updated_at`) VALUES ('7', '5', '4', '1', NULL, '1', 'Chef Special Burger', NULL, 'TST-BGR', '1', '12.00', '0.00', '12.00', 'ROUTED', 'Prep note for Chef Special Burger', 'ROUTED', '2026-09-09 05:41:14', '2026-09-09 05:41:14');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `station_id`, `variant_id`, `counter_id`, `item_name`, `variant_name`, `sku`, `quantity`, `unit_price`, `modifier_total`, `subtotal`, `status`, `notes`, `routing_status`, `routed_at`, `updated_at`) VALUES ('8', '5', '5', '2', NULL, '2', 'Iced Americano', NULL, 'TST-COF', '1', '4.50', '0.00', '4.50', 'ROUTED', 'Prep note for Iced Americano', 'ROUTED', '2026-09-09 05:41:14', '2026-09-09 05:41:14');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `station_id`, `variant_id`, `counter_id`, `item_name`, `variant_name`, `sku`, `quantity`, `unit_price`, `modifier_total`, `subtotal`, `status`, `notes`, `routing_status`, `routed_at`, `updated_at`) VALUES ('9', '5', '6', '4', NULL, '4', 'Molten Chocolate Cake', NULL, 'TST-CAK', '1', '7.50', '0.00', '7.50', 'ROUTED', 'Prep note for Molten Chocolate Cake', 'ROUTED', '2026-09-09 05:41:14', '2026-09-09 05:41:14');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `station_id`, `variant_id`, `counter_id`, `item_name`, `variant_name`, `sku`, `quantity`, `unit_price`, `modifier_total`, `subtotal`, `status`, `notes`, `routing_status`, `routed_at`, `updated_at`) VALUES ('10', '6', '4', '1', NULL, '1', 'Chef Special Burger', NULL, 'TST-BGR', '1', '12.00', '0.00', '12.00', 'ROUTED', 'Prep note for Chef Special Burger', 'ROUTED', '2026-09-09 05:41:27', '2026-09-09 05:41:27');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `station_id`, `variant_id`, `counter_id`, `item_name`, `variant_name`, `sku`, `quantity`, `unit_price`, `modifier_total`, `subtotal`, `status`, `notes`, `routing_status`, `routed_at`, `updated_at`) VALUES ('11', '6', '5', '2', NULL, '2', 'Iced Americano', NULL, 'TST-COF', '1', '4.50', '0.00', '4.50', 'ROUTED', 'Prep note for Iced Americano', 'ROUTED', '2026-09-09 05:41:27', '2026-09-09 05:41:27');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `station_id`, `variant_id`, `counter_id`, `item_name`, `variant_name`, `sku`, `quantity`, `unit_price`, `modifier_total`, `subtotal`, `status`, `notes`, `routing_status`, `routed_at`, `updated_at`) VALUES ('12', '6', '6', '4', NULL, '4', 'Molten Chocolate Cake', NULL, 'TST-CAK', '1', '7.50', '0.00', '7.50', 'ROUTED', 'Prep note for Molten Chocolate Cake', 'ROUTED', '2026-09-09 05:41:27', '2026-09-09 05:41:27');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `station_id`, `variant_id`, `counter_id`, `item_name`, `variant_name`, `sku`, `quantity`, `unit_price`, `modifier_total`, `subtotal`, `status`, `notes`, `routing_status`, `routed_at`, `updated_at`) VALUES ('13', '7', '4', '1', NULL, '1', 'Chef Special Burger', NULL, 'TST-BGR', '1', '12.00', '0.00', '12.00', 'ROUTED', 'Prep note for Chef Special Burger', 'ROUTED', '2026-09-09 05:41:47', '2026-09-09 05:41:47');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `station_id`, `variant_id`, `counter_id`, `item_name`, `variant_name`, `sku`, `quantity`, `unit_price`, `modifier_total`, `subtotal`, `status`, `notes`, `routing_status`, `routed_at`, `updated_at`) VALUES ('14', '7', '5', '2', NULL, '2', 'Iced Americano', NULL, 'TST-COF', '1', '4.50', '0.00', '4.50', 'ROUTED', 'Prep note for Iced Americano', 'ROUTED', '2026-09-09 05:41:47', '2026-09-09 05:41:47');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `station_id`, `variant_id`, `counter_id`, `item_name`, `variant_name`, `sku`, `quantity`, `unit_price`, `modifier_total`, `subtotal`, `status`, `notes`, `routing_status`, `routed_at`, `updated_at`) VALUES ('15', '7', '6', '4', NULL, '4', 'Molten Chocolate Cake', NULL, 'TST-CAK', '1', '7.50', '0.00', '7.50', 'ROUTED', 'Prep note for Molten Chocolate Cake', 'ROUTED', '2026-09-09 05:41:47', '2026-09-09 05:41:47');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `station_id`, `variant_id`, `counter_id`, `item_name`, `variant_name`, `sku`, `quantity`, `unit_price`, `modifier_total`, `subtotal`, `status`, `notes`, `routing_status`, `routed_at`, `updated_at`) VALUES ('16', '9', '1', NULL, NULL, '1', 'Test Item 100', NULL, NULL, '2', '250.00', '0.00', '500.00', 'SERVED', NULL, 'UNROUTED', NULL, '2026-09-09 05:45:25');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `station_id`, `variant_id`, `counter_id`, `item_name`, `variant_name`, `sku`, `quantity`, `unit_price`, `modifier_total`, `subtotal`, `status`, `notes`, `routing_status`, `routed_at`, `updated_at`) VALUES ('17', '10', '1', NULL, NULL, '1', 'Test Item 100', NULL, NULL, '2', '250.00', '0.00', '500.00', 'SERVED', NULL, 'UNROUTED', NULL, '2026-09-09 05:45:42');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `station_id`, `variant_id`, `counter_id`, `item_name`, `variant_name`, `sku`, `quantity`, `unit_price`, `modifier_total`, `subtotal`, `status`, `notes`, `routing_status`, `routed_at`, `updated_at`) VALUES ('18', '11', '1', NULL, NULL, '1', 'Test Item 100', NULL, NULL, '2', '250.00', '0.00', '500.00', 'SERVED', NULL, 'UNROUTED', NULL, '2026-09-09 05:45:54');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `station_id`, `variant_id`, `counter_id`, `item_name`, `variant_name`, `sku`, `quantity`, `unit_price`, `modifier_total`, `subtotal`, `status`, `notes`, `routing_status`, `routed_at`, `updated_at`) VALUES ('19', '12', '1', NULL, NULL, '1', 'Test Item 100', NULL, NULL, '2', '250.00', '0.00', '500.00', 'SERVED', NULL, 'UNROUTED', NULL, '2026-09-09 05:46:28');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `station_id`, `variant_id`, `counter_id`, `item_name`, `variant_name`, `sku`, `quantity`, `unit_price`, `modifier_total`, `subtotal`, `status`, `notes`, `routing_status`, `routed_at`, `updated_at`) VALUES ('20', '13', '1', NULL, NULL, '1', 'Test Item 100', NULL, NULL, '2', '250.00', '0.00', '500.00', 'SERVED', NULL, 'UNROUTED', NULL, '2026-09-09 05:46:43');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `station_id`, `variant_id`, `counter_id`, `item_name`, `variant_name`, `sku`, `quantity`, `unit_price`, `modifier_total`, `subtotal`, `status`, `notes`, `routing_status`, `routed_at`, `updated_at`) VALUES ('21', '14', '1', NULL, NULL, '1', 'Test Item 100', NULL, NULL, '2', '250.00', '0.00', '500.00', 'SERVED', NULL, 'UNROUTED', NULL, '2026-09-09 05:46:55');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `station_id`, `variant_id`, `counter_id`, `item_name`, `variant_name`, `sku`, `quantity`, `unit_price`, `modifier_total`, `subtotal`, `status`, `notes`, `routing_status`, `routed_at`, `updated_at`) VALUES ('22', '15', '1', NULL, NULL, '1', 'Test Item 100', NULL, NULL, '2', '250.00', '0.00', '500.00', 'SERVED', NULL, 'UNROUTED', NULL, '2026-09-09 05:47:02');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `station_id`, `variant_id`, `counter_id`, `item_name`, `variant_name`, `sku`, `quantity`, `unit_price`, `modifier_total`, `subtotal`, `status`, `notes`, `routing_status`, `routed_at`, `updated_at`) VALUES ('23', '16', '1', NULL, NULL, '1', 'Mutton Kacchi Feast', NULL, NULL, '2', '500.00', '0.00', '1000.00', 'SERVED', NULL, 'UNROUTED', NULL, '2026-09-09 05:51:12');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `station_id`, `variant_id`, `counter_id`, `item_name`, `variant_name`, `sku`, `quantity`, `unit_price`, `modifier_total`, `subtotal`, `status`, `notes`, `routing_status`, `routed_at`, `updated_at`) VALUES ('24', '17', '1', NULL, NULL, '1', 'Mutton Kacchi Feast', NULL, NULL, '2', '500.00', '0.00', '1000.00', 'SERVED', NULL, 'UNROUTED', NULL, '2026-09-09 05:51:21');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `station_id`, `variant_id`, `counter_id`, `item_name`, `variant_name`, `sku`, `quantity`, `unit_price`, `modifier_total`, `subtotal`, `status`, `notes`, `routing_status`, `routed_at`, `updated_at`) VALUES ('25', '18', '1', NULL, NULL, '1', 'Mutton Kacchi Feast', NULL, NULL, '2', '500.00', '0.00', '1000.00', 'SERVED', NULL, 'UNROUTED', NULL, '2026-09-09 05:51:31');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `station_id`, `variant_id`, `counter_id`, `item_name`, `variant_name`, `sku`, `quantity`, `unit_price`, `modifier_total`, `subtotal`, `status`, `notes`, `routing_status`, `routed_at`, `updated_at`) VALUES ('26', '19', '1', NULL, NULL, '1', 'Mutton Kacchi Feast', NULL, NULL, '2', '500.00', '0.00', '1000.00', 'SERVED', NULL, 'UNROUTED', NULL, '2026-09-09 05:51:40');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `station_id`, `variant_id`, `counter_id`, `item_name`, `variant_name`, `sku`, `quantity`, `unit_price`, `modifier_total`, `subtotal`, `status`, `notes`, `routing_status`, `routed_at`, `updated_at`) VALUES ('27', '20', '1', NULL, NULL, '1', 'Mutton Kacchi Feast', NULL, NULL, '2', '500.00', '0.00', '1000.00', 'SERVED', NULL, 'UNROUTED', NULL, '2026-09-09 05:51:48');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `station_id`, `variant_id`, `counter_id`, `item_name`, `variant_name`, `sku`, `quantity`, `unit_price`, `modifier_total`, `subtotal`, `status`, `notes`, `routing_status`, `routed_at`, `updated_at`) VALUES ('28', '24', '4', '1', NULL, '1', 'Smokey BBQ Burger', NULL, NULL, '2', '350.00', '0.00', '700.00', 'ROUTED', NULL, 'ROUTED', '2026-09-09 05:55:33', '2026-09-09 05:55:33');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `station_id`, `variant_id`, `counter_id`, `item_name`, `variant_name`, `sku`, `quantity`, `unit_price`, `modifier_total`, `subtotal`, `status`, `notes`, `routing_status`, `routed_at`, `updated_at`) VALUES ('29', '24', '5', '2', NULL, '1', 'Fresh Lemonade', NULL, NULL, '2', '150.00', '0.00', '300.00', 'ROUTED', NULL, 'ROUTED', '2026-09-09 05:55:33', '2026-09-09 05:55:33');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `station_id`, `variant_id`, `counter_id`, `item_name`, `variant_name`, `sku`, `quantity`, `unit_price`, `modifier_total`, `subtotal`, `status`, `notes`, `routing_status`, `routed_at`, `updated_at`) VALUES ('30', '25', '4', '1', NULL, '1', 'Smokey BBQ Burger', NULL, NULL, '2', '350.00', '0.00', '700.00', 'ROUTED', NULL, 'ROUTED', '2026-09-09 05:55:46', '2026-09-09 05:55:46');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `station_id`, `variant_id`, `counter_id`, `item_name`, `variant_name`, `sku`, `quantity`, `unit_price`, `modifier_total`, `subtotal`, `status`, `notes`, `routing_status`, `routed_at`, `updated_at`) VALUES ('31', '25', '5', '2', NULL, '1', 'Fresh Lemonade', NULL, NULL, '2', '150.00', '0.00', '300.00', 'ROUTED', NULL, 'ROUTED', '2026-09-09 05:55:46', '2026-09-09 05:55:46');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `station_id`, `variant_id`, `counter_id`, `item_name`, `variant_name`, `sku`, `quantity`, `unit_price`, `modifier_total`, `subtotal`, `status`, `notes`, `routing_status`, `routed_at`, `updated_at`) VALUES ('32', '26', '4', '1', NULL, '1', 'Smokey BBQ Burger', NULL, NULL, '2', '350.00', '0.00', '700.00', 'ROUTED', NULL, 'ROUTED', '2026-09-09 05:56:00', '2026-09-09 05:56:00');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `station_id`, `variant_id`, `counter_id`, `item_name`, `variant_name`, `sku`, `quantity`, `unit_price`, `modifier_total`, `subtotal`, `status`, `notes`, `routing_status`, `routed_at`, `updated_at`) VALUES ('33', '26', '5', '2', NULL, '1', 'Fresh Lemonade', NULL, NULL, '2', '150.00', '0.00', '300.00', 'ROUTED', NULL, 'ROUTED', '2026-09-09 05:56:00', '2026-09-09 05:56:00');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `station_id`, `variant_id`, `counter_id`, `item_name`, `variant_name`, `sku`, `quantity`, `unit_price`, `modifier_total`, `subtotal`, `status`, `notes`, `routing_status`, `routed_at`, `updated_at`) VALUES ('34', '27', '4', '1', NULL, '1', 'Smokey BBQ Burger', NULL, NULL, '2', '350.00', '0.00', '700.00', 'ROUTED', NULL, 'ROUTED', '2026-09-09 05:56:07', '2026-09-09 05:56:07');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `station_id`, `variant_id`, `counter_id`, `item_name`, `variant_name`, `sku`, `quantity`, `unit_price`, `modifier_total`, `subtotal`, `status`, `notes`, `routing_status`, `routed_at`, `updated_at`) VALUES ('35', '27', '5', '2', NULL, '1', 'Fresh Lemonade', NULL, NULL, '2', '150.00', '0.00', '300.00', 'ROUTED', NULL, 'ROUTED', '2026-09-09 05:56:07', '2026-09-09 05:56:07');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `station_id`, `variant_id`, `counter_id`, `item_name`, `variant_name`, `sku`, `quantity`, `unit_price`, `modifier_total`, `subtotal`, `status`, `notes`, `routing_status`, `routed_at`, `updated_at`) VALUES ('36', '30', '4', NULL, NULL, '1', 'Chef Special Burger', NULL, NULL, '2', '12.00', '0.00', '24.00', 'PREPARING', NULL, 'UNROUTED', NULL, '2026-09-09 06:05:23');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `station_id`, `variant_id`, `counter_id`, `item_name`, `variant_name`, `sku`, `quantity`, `unit_price`, `modifier_total`, `subtotal`, `status`, `notes`, `routing_status`, `routed_at`, `updated_at`) VALUES ('37', '31', '4', NULL, NULL, '1', 'Chef Special Burger', NULL, NULL, '2', '12.00', '0.00', '24.00', 'PREPARING', NULL, 'UNROUTED', NULL, '2026-09-09 06:05:31');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `station_id`, `variant_id`, `counter_id`, `item_name`, `variant_name`, `sku`, `quantity`, `unit_price`, `modifier_total`, `subtotal`, `status`, `notes`, `routing_status`, `routed_at`, `updated_at`) VALUES ('38', '32', '4', NULL, NULL, '1', 'Chef Special Burger', NULL, NULL, '2', '12.00', '0.00', '24.00', 'PREPARING', NULL, 'UNROUTED', NULL, '2026-09-09 06:05:43');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `station_id`, `variant_id`, `counter_id`, `item_name`, `variant_name`, `sku`, `quantity`, `unit_price`, `modifier_total`, `subtotal`, `status`, `notes`, `routing_status`, `routed_at`, `updated_at`) VALUES ('39', '33', '4', NULL, NULL, '1', 'Chef Special Burger', NULL, NULL, '2', '12.00', '0.00', '24.00', 'PREPARING', NULL, 'UNROUTED', NULL, '2026-09-09 06:06:06');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `station_id`, `variant_id`, `counter_id`, `item_name`, `variant_name`, `sku`, `quantity`, `unit_price`, `modifier_total`, `subtotal`, `status`, `notes`, `routing_status`, `routed_at`, `updated_at`) VALUES ('40', '34', '4', NULL, NULL, '1', 'Chef Special Burger', NULL, NULL, '2', '12.00', '0.00', '24.00', 'PREPARING', NULL, 'UNROUTED', NULL, '2026-09-09 06:06:20');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `station_id`, `variant_id`, `counter_id`, `item_name`, `variant_name`, `sku`, `quantity`, `unit_price`, `modifier_total`, `subtotal`, `status`, `notes`, `routing_status`, `routed_at`, `updated_at`) VALUES ('41', '35', '4', NULL, NULL, '1', 'Chef Special Burger', NULL, NULL, '2', '12.00', '0.00', '24.00', 'PREPARING', NULL, 'UNROUTED', NULL, '2026-09-09 06:06:27');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `station_id`, `variant_id`, `counter_id`, `item_name`, `variant_name`, `sku`, `quantity`, `unit_price`, `modifier_total`, `subtotal`, `status`, `notes`, `routing_status`, `routed_at`, `updated_at`) VALUES ('42', '36', '4', NULL, NULL, '1', 'Chef Special Burger', NULL, 'TST-BGR', '2', '12.00', '0.00', '24.00', 'ROUTED', NULL, 'UNROUTED', NULL, '2026-09-09 06:52:51');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `station_id`, `variant_id`, `counter_id`, `item_name`, `variant_name`, `sku`, `quantity`, `unit_price`, `modifier_total`, `subtotal`, `status`, `notes`, `routing_status`, `routed_at`, `updated_at`) VALUES ('43', '37', '4', NULL, NULL, '1', 'Chef Special Burger', NULL, 'TST-BGR', '2', '12.00', '0.00', '24.00', 'ROUTED', NULL, 'UNROUTED', NULL, '2026-09-09 06:53:01');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `station_id`, `variant_id`, `counter_id`, `item_name`, `variant_name`, `sku`, `quantity`, `unit_price`, `modifier_total`, `subtotal`, `status`, `notes`, `routing_status`, `routed_at`, `updated_at`) VALUES ('44', '38', '4', NULL, NULL, '1', 'Chef Special Burger', NULL, 'TST-BGR', '2', '12.00', '0.00', '24.00', 'ROUTED', NULL, 'UNROUTED', NULL, '2026-09-09 06:53:19');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `station_id`, `variant_id`, `counter_id`, `item_name`, `variant_name`, `sku`, `quantity`, `unit_price`, `modifier_total`, `subtotal`, `status`, `notes`, `routing_status`, `routed_at`, `updated_at`) VALUES ('45', '39', '4', NULL, NULL, '1', 'Chef Special Burger', NULL, 'TST-BGR', '2', '12.00', '0.00', '24.00', 'ROUTED', NULL, 'UNROUTED', NULL, '2026-09-09 06:53:45');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `station_id`, `variant_id`, `counter_id`, `item_name`, `variant_name`, `sku`, `quantity`, `unit_price`, `modifier_total`, `subtotal`, `status`, `notes`, `routing_status`, `routed_at`, `updated_at`) VALUES ('46', '40', '4', NULL, NULL, '1', 'Chef Special Burger', NULL, 'TST-BGR', '1', '12.00', '0.00', '12.00', 'ROUTED', NULL, 'UNROUTED', NULL, '2026-09-09 06:53:45');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `station_id`, `variant_id`, `counter_id`, `item_name`, `variant_name`, `sku`, `quantity`, `unit_price`, `modifier_total`, `subtotal`, `status`, `notes`, `routing_status`, `routed_at`, `updated_at`) VALUES ('47', '41', '4', NULL, NULL, '1', 'Chef Special Burger', NULL, 'TST-BGR', '2', '12.00', '0.00', '24.00', 'ROUTED', NULL, 'UNROUTED', NULL, '2026-09-09 06:53:54');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `station_id`, `variant_id`, `counter_id`, `item_name`, `variant_name`, `sku`, `quantity`, `unit_price`, `modifier_total`, `subtotal`, `status`, `notes`, `routing_status`, `routed_at`, `updated_at`) VALUES ('48', '42', '4', '1', NULL, '1', 'Chef Special Burger', NULL, 'TST-BGR', '1', '12.00', '0.00', '12.00', 'ROUTED', NULL, 'ROUTED', '2026-09-09 06:53:54', '2026-09-09 06:53:54');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `station_id`, `variant_id`, `counter_id`, `item_name`, `variant_name`, `sku`, `quantity`, `unit_price`, `modifier_total`, `subtotal`, `status`, `notes`, `routing_status`, `routed_at`, `updated_at`) VALUES ('49', '43', '4', NULL, NULL, '1', 'Chef Special Burger', NULL, 'TST-BGR', '2', '12.00', '0.00', '24.00', 'ROUTED', NULL, 'UNROUTED', NULL, '2026-09-09 06:54:03');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `station_id`, `variant_id`, `counter_id`, `item_name`, `variant_name`, `sku`, `quantity`, `unit_price`, `modifier_total`, `subtotal`, `status`, `notes`, `routing_status`, `routed_at`, `updated_at`) VALUES ('50', '44', '4', '1', NULL, '1', 'Chef Special Burger', NULL, 'TST-BGR', '1', '12.00', '0.00', '12.00', 'ROUTED', NULL, 'ROUTED', '2026-09-09 06:54:03', '2026-09-09 06:54:03');

-- Table structure for `order_routes`
DROP TABLE IF EXISTS `order_routes`;
CREATE TABLE `order_routes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL,
  `station_id` int(10) unsigned NOT NULL,
  `route_status` enum('PENDING','SENT','ACKNOWLEDGED','IN_PROGRESS','READY','COMPLETED','CANCELLED','FAILED') DEFAULT 'SENT',
  `routed_by` int(10) unsigned DEFAULT NULL,
  `routed_at` datetime DEFAULT current_timestamp(),
  `received_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `routing_mode` enum('AUTOMATIC','MANUAL','HYBRID') DEFAULT 'AUTOMATIC',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_order_station` (`order_id`,`station_id`),
  KEY `station_id` (`station_id`),
  CONSTRAINT `order_routes_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_routes_ibfk_2` FOREIGN KEY (`station_id`) REFERENCES `stations` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for `order_routes`
INSERT INTO `order_routes` (`id`, `order_id`, `station_id`, `route_status`, `routed_by`, `routed_at`, `received_at`, `completed_at`, `notes`, `routing_mode`, `created_at`) VALUES ('1', '3', '1', 'SENT', '1', '2026-09-09 05:40:50', NULL, NULL, NULL, 'AUTOMATIC', '2026-09-09 05:40:50');
INSERT INTO `order_routes` (`id`, `order_id`, `station_id`, `route_status`, `routed_by`, `routed_at`, `received_at`, `completed_at`, `notes`, `routing_mode`, `created_at`) VALUES ('2', '3', '2', 'SENT', '1', '2026-09-09 05:40:50', NULL, NULL, NULL, 'AUTOMATIC', '2026-09-09 05:40:50');
INSERT INTO `order_routes` (`id`, `order_id`, `station_id`, `route_status`, `routed_by`, `routed_at`, `received_at`, `completed_at`, `notes`, `routing_mode`, `created_at`) VALUES ('3', '3', '4', 'SENT', '1', '2026-09-09 05:40:50', NULL, NULL, NULL, 'AUTOMATIC', '2026-09-09 05:40:50');
INSERT INTO `order_routes` (`id`, `order_id`, `station_id`, `route_status`, `routed_by`, `routed_at`, `received_at`, `completed_at`, `notes`, `routing_mode`, `created_at`) VALUES ('4', '4', '1', 'SENT', '1', '2026-09-09 05:41:01', NULL, NULL, NULL, 'AUTOMATIC', '2026-09-09 05:41:01');
INSERT INTO `order_routes` (`id`, `order_id`, `station_id`, `route_status`, `routed_by`, `routed_at`, `received_at`, `completed_at`, `notes`, `routing_mode`, `created_at`) VALUES ('5', '4', '2', 'SENT', '1', '2026-09-09 05:41:01', NULL, NULL, NULL, 'AUTOMATIC', '2026-09-09 05:41:01');
INSERT INTO `order_routes` (`id`, `order_id`, `station_id`, `route_status`, `routed_by`, `routed_at`, `received_at`, `completed_at`, `notes`, `routing_mode`, `created_at`) VALUES ('6', '4', '4', 'SENT', '1', '2026-09-09 05:41:01', NULL, NULL, NULL, 'AUTOMATIC', '2026-09-09 05:41:01');
INSERT INTO `order_routes` (`id`, `order_id`, `station_id`, `route_status`, `routed_by`, `routed_at`, `received_at`, `completed_at`, `notes`, `routing_mode`, `created_at`) VALUES ('7', '5', '1', 'SENT', '1', '2026-09-09 05:41:14', NULL, NULL, NULL, 'AUTOMATIC', '2026-09-09 05:41:14');
INSERT INTO `order_routes` (`id`, `order_id`, `station_id`, `route_status`, `routed_by`, `routed_at`, `received_at`, `completed_at`, `notes`, `routing_mode`, `created_at`) VALUES ('8', '5', '2', 'SENT', '1', '2026-09-09 05:41:14', NULL, NULL, NULL, 'AUTOMATIC', '2026-09-09 05:41:14');
INSERT INTO `order_routes` (`id`, `order_id`, `station_id`, `route_status`, `routed_by`, `routed_at`, `received_at`, `completed_at`, `notes`, `routing_mode`, `created_at`) VALUES ('9', '5', '4', 'SENT', '1', '2026-09-09 05:41:14', NULL, NULL, NULL, 'AUTOMATIC', '2026-09-09 05:41:14');
INSERT INTO `order_routes` (`id`, `order_id`, `station_id`, `route_status`, `routed_by`, `routed_at`, `received_at`, `completed_at`, `notes`, `routing_mode`, `created_at`) VALUES ('10', '6', '1', 'SENT', '1', '2026-09-09 05:41:27', NULL, NULL, NULL, 'AUTOMATIC', '2026-09-09 05:41:27');
INSERT INTO `order_routes` (`id`, `order_id`, `station_id`, `route_status`, `routed_by`, `routed_at`, `received_at`, `completed_at`, `notes`, `routing_mode`, `created_at`) VALUES ('11', '6', '2', 'SENT', '1', '2026-09-09 05:41:27', NULL, NULL, NULL, 'AUTOMATIC', '2026-09-09 05:41:27');
INSERT INTO `order_routes` (`id`, `order_id`, `station_id`, `route_status`, `routed_by`, `routed_at`, `received_at`, `completed_at`, `notes`, `routing_mode`, `created_at`) VALUES ('12', '6', '4', 'SENT', '1', '2026-09-09 05:41:27', NULL, NULL, NULL, 'AUTOMATIC', '2026-09-09 05:41:27');
INSERT INTO `order_routes` (`id`, `order_id`, `station_id`, `route_status`, `routed_by`, `routed_at`, `received_at`, `completed_at`, `notes`, `routing_mode`, `created_at`) VALUES ('13', '7', '1', 'SENT', '1', '2026-09-09 05:41:47', NULL, NULL, NULL, 'AUTOMATIC', '2026-09-09 05:41:47');
INSERT INTO `order_routes` (`id`, `order_id`, `station_id`, `route_status`, `routed_by`, `routed_at`, `received_at`, `completed_at`, `notes`, `routing_mode`, `created_at`) VALUES ('14', '7', '2', 'SENT', '1', '2026-09-09 05:41:47', NULL, NULL, NULL, 'AUTOMATIC', '2026-09-09 05:41:47');
INSERT INTO `order_routes` (`id`, `order_id`, `station_id`, `route_status`, `routed_by`, `routed_at`, `received_at`, `completed_at`, `notes`, `routing_mode`, `created_at`) VALUES ('15', '7', '4', 'SENT', '1', '2026-09-09 05:41:47', NULL, NULL, NULL, 'AUTOMATIC', '2026-09-09 05:41:47');
INSERT INTO `order_routes` (`id`, `order_id`, `station_id`, `route_status`, `routed_by`, `routed_at`, `received_at`, `completed_at`, `notes`, `routing_mode`, `created_at`) VALUES ('16', '24', '1', 'SENT', '1', '2026-09-09 05:55:33', NULL, NULL, NULL, 'AUTOMATIC', '2026-09-09 05:55:33');
INSERT INTO `order_routes` (`id`, `order_id`, `station_id`, `route_status`, `routed_by`, `routed_at`, `received_at`, `completed_at`, `notes`, `routing_mode`, `created_at`) VALUES ('17', '24', '2', 'SENT', '1', '2026-09-09 05:55:33', NULL, NULL, NULL, 'AUTOMATIC', '2026-09-09 05:55:33');
INSERT INTO `order_routes` (`id`, `order_id`, `station_id`, `route_status`, `routed_by`, `routed_at`, `received_at`, `completed_at`, `notes`, `routing_mode`, `created_at`) VALUES ('18', '25', '1', 'SENT', '1', '2026-09-09 05:55:46', NULL, NULL, NULL, 'AUTOMATIC', '2026-09-09 05:55:46');
INSERT INTO `order_routes` (`id`, `order_id`, `station_id`, `route_status`, `routed_by`, `routed_at`, `received_at`, `completed_at`, `notes`, `routing_mode`, `created_at`) VALUES ('19', '25', '2', 'SENT', '1', '2026-09-09 05:55:46', NULL, NULL, NULL, 'AUTOMATIC', '2026-09-09 05:55:46');
INSERT INTO `order_routes` (`id`, `order_id`, `station_id`, `route_status`, `routed_by`, `routed_at`, `received_at`, `completed_at`, `notes`, `routing_mode`, `created_at`) VALUES ('20', '26', '1', 'SENT', '1', '2026-09-09 05:56:00', NULL, NULL, NULL, 'AUTOMATIC', '2026-09-09 05:56:00');
INSERT INTO `order_routes` (`id`, `order_id`, `station_id`, `route_status`, `routed_by`, `routed_at`, `received_at`, `completed_at`, `notes`, `routing_mode`, `created_at`) VALUES ('21', '26', '2', 'SENT', '1', '2026-09-09 05:56:00', NULL, NULL, NULL, 'AUTOMATIC', '2026-09-09 05:56:00');
INSERT INTO `order_routes` (`id`, `order_id`, `station_id`, `route_status`, `routed_by`, `routed_at`, `received_at`, `completed_at`, `notes`, `routing_mode`, `created_at`) VALUES ('22', '27', '1', 'SENT', '1', '2026-09-09 05:56:07', NULL, NULL, NULL, 'AUTOMATIC', '2026-09-09 05:56:07');
INSERT INTO `order_routes` (`id`, `order_id`, `station_id`, `route_status`, `routed_by`, `routed_at`, `received_at`, `completed_at`, `notes`, `routing_mode`, `created_at`) VALUES ('23', '27', '2', 'SENT', '1', '2026-09-09 05:56:07', NULL, NULL, NULL, 'AUTOMATIC', '2026-09-09 05:56:07');
INSERT INTO `order_routes` (`id`, `order_id`, `station_id`, `route_status`, `routed_by`, `routed_at`, `received_at`, `completed_at`, `notes`, `routing_mode`, `created_at`) VALUES ('24', '42', '1', 'SENT', '1', '2026-09-09 06:53:54', NULL, NULL, NULL, 'AUTOMATIC', '2026-09-09 06:53:54');
INSERT INTO `order_routes` (`id`, `order_id`, `station_id`, `route_status`, `routed_by`, `routed_at`, `received_at`, `completed_at`, `notes`, `routing_mode`, `created_at`) VALUES ('25', '44', '1', 'SENT', '1', '2026-09-09 06:54:03', NULL, NULL, NULL, 'AUTOMATIC', '2026-09-09 06:54:03');

-- Table structure for `order_status_history`
DROP TABLE IF EXISTS `order_status_history`;
CREATE TABLE `order_status_history` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL,
  `previous_status` varchar(50) DEFAULT NULL,
  `new_status` varchar(50) NOT NULL,
  `changed_by_user_id` int(10) unsigned NOT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `order_status_history_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for `order_status_history`
INSERT INTO `order_status_history` (`id`, `order_id`, `previous_status`, `new_status`, `changed_by_user_id`, `notes`, `created_at`) VALUES ('1', '2', NULL, 'DRAFT', '1', 'Draft order initialized', '2026-09-09 05:40:44');
INSERT INTO `order_status_history` (`id`, `order_id`, `previous_status`, `new_status`, `changed_by_user_id`, `notes`, `created_at`) VALUES ('2', '3', NULL, 'DRAFT', '1', 'Draft order initialized', '2026-09-09 05:40:50');
INSERT INTO `order_status_history` (`id`, `order_id`, `previous_status`, `new_status`, `changed_by_user_id`, `notes`, `created_at`) VALUES ('3', '3', 'DRAFT', 'SUBMITTED', '1', 'Order submitted for kitchen & counter routing', '2026-09-09 05:40:50');
INSERT INTO `order_status_history` (`id`, `order_id`, `previous_status`, `new_status`, `changed_by_user_id`, `notes`, `created_at`) VALUES ('4', '4', NULL, 'DRAFT', '1', 'Draft order initialized', '2026-09-09 05:41:01');
INSERT INTO `order_status_history` (`id`, `order_id`, `previous_status`, `new_status`, `changed_by_user_id`, `notes`, `created_at`) VALUES ('5', '4', 'DRAFT', 'SUBMITTED', '1', 'Order submitted for kitchen & counter routing', '2026-09-09 05:41:01');
INSERT INTO `order_status_history` (`id`, `order_id`, `previous_status`, `new_status`, `changed_by_user_id`, `notes`, `created_at`) VALUES ('6', '5', NULL, 'DRAFT', '1', 'Draft order initialized', '2026-09-09 05:41:14');
INSERT INTO `order_status_history` (`id`, `order_id`, `previous_status`, `new_status`, `changed_by_user_id`, `notes`, `created_at`) VALUES ('7', '5', 'DRAFT', 'SUBMITTED', '1', 'Order submitted for kitchen & counter routing', '2026-09-09 05:41:14');
INSERT INTO `order_status_history` (`id`, `order_id`, `previous_status`, `new_status`, `changed_by_user_id`, `notes`, `created_at`) VALUES ('8', '6', NULL, 'DRAFT', '1', 'Draft order initialized', '2026-09-09 05:41:27');
INSERT INTO `order_status_history` (`id`, `order_id`, `previous_status`, `new_status`, `changed_by_user_id`, `notes`, `created_at`) VALUES ('9', '6', 'DRAFT', 'SUBMITTED', '1', 'Order submitted for kitchen & counter routing', '2026-09-09 05:41:27');
INSERT INTO `order_status_history` (`id`, `order_id`, `previous_status`, `new_status`, `changed_by_user_id`, `notes`, `created_at`) VALUES ('10', '7', NULL, 'DRAFT', '1', 'Draft order initialized', '2026-09-09 05:41:47');
INSERT INTO `order_status_history` (`id`, `order_id`, `previous_status`, `new_status`, `changed_by_user_id`, `notes`, `created_at`) VALUES ('11', '7', 'DRAFT', 'SUBMITTED', '1', 'Order submitted for kitchen & counter routing', '2026-09-09 05:41:47');
INSERT INTO `order_status_history` (`id`, `order_id`, `previous_status`, `new_status`, `changed_by_user_id`, `notes`, `created_at`) VALUES ('12', '36', NULL, 'DRAFT', '1', 'Draft order initialized', '2026-09-09 06:52:51');
INSERT INTO `order_status_history` (`id`, `order_id`, `previous_status`, `new_status`, `changed_by_user_id`, `notes`, `created_at`) VALUES ('13', '36', 'DRAFT', 'SUBMITTED', '1', 'Order submitted for kitchen & counter routing', '2026-09-09 06:52:51');
INSERT INTO `order_status_history` (`id`, `order_id`, `previous_status`, `new_status`, `changed_by_user_id`, `notes`, `created_at`) VALUES ('14', '36', 'SUBMITTED', 'COMPLETED', '1', 'Status advanced to COMPLETED', '2026-09-09 06:52:51');
INSERT INTO `order_status_history` (`id`, `order_id`, `previous_status`, `new_status`, `changed_by_user_id`, `notes`, `created_at`) VALUES ('15', '37', NULL, 'DRAFT', '1', 'Draft order initialized', '2026-09-09 06:53:01');
INSERT INTO `order_status_history` (`id`, `order_id`, `previous_status`, `new_status`, `changed_by_user_id`, `notes`, `created_at`) VALUES ('16', '37', 'DRAFT', 'SUBMITTED', '1', 'Order submitted for kitchen & counter routing', '2026-09-09 06:53:01');
INSERT INTO `order_status_history` (`id`, `order_id`, `previous_status`, `new_status`, `changed_by_user_id`, `notes`, `created_at`) VALUES ('17', '37', 'SUBMITTED', 'COMPLETED', '1', 'Status advanced to COMPLETED', '2026-09-09 06:53:01');
INSERT INTO `order_status_history` (`id`, `order_id`, `previous_status`, `new_status`, `changed_by_user_id`, `notes`, `created_at`) VALUES ('18', '38', NULL, 'DRAFT', '1', 'Draft order initialized', '2026-09-09 06:53:19');
INSERT INTO `order_status_history` (`id`, `order_id`, `previous_status`, `new_status`, `changed_by_user_id`, `notes`, `created_at`) VALUES ('19', '38', 'DRAFT', 'SUBMITTED', '1', 'Order submitted for kitchen & counter routing', '2026-09-09 06:53:19');
INSERT INTO `order_status_history` (`id`, `order_id`, `previous_status`, `new_status`, `changed_by_user_id`, `notes`, `created_at`) VALUES ('20', '38', 'SUBMITTED', 'COMPLETED', '1', 'Status advanced to COMPLETED', '2026-09-09 06:53:19');
INSERT INTO `order_status_history` (`id`, `order_id`, `previous_status`, `new_status`, `changed_by_user_id`, `notes`, `created_at`) VALUES ('21', '39', NULL, 'DRAFT', '1', 'Draft order initialized', '2026-09-09 06:53:45');
INSERT INTO `order_status_history` (`id`, `order_id`, `previous_status`, `new_status`, `changed_by_user_id`, `notes`, `created_at`) VALUES ('22', '39', 'DRAFT', 'SUBMITTED', '1', 'Order submitted for kitchen & counter routing', '2026-09-09 06:53:45');
INSERT INTO `order_status_history` (`id`, `order_id`, `previous_status`, `new_status`, `changed_by_user_id`, `notes`, `created_at`) VALUES ('23', '39', 'SUBMITTED', 'COMPLETED', '1', 'Status advanced to COMPLETED', '2026-09-09 06:53:45');
INSERT INTO `order_status_history` (`id`, `order_id`, `previous_status`, `new_status`, `changed_by_user_id`, `notes`, `created_at`) VALUES ('24', '40', NULL, 'DRAFT', '1', 'Draft order initialized', '2026-09-09 06:53:45');
INSERT INTO `order_status_history` (`id`, `order_id`, `previous_status`, `new_status`, `changed_by_user_id`, `notes`, `created_at`) VALUES ('25', '40', 'DRAFT', 'SUBMITTED', '1', 'Order submitted for kitchen & counter routing', '2026-09-09 06:53:45');
INSERT INTO `order_status_history` (`id`, `order_id`, `previous_status`, `new_status`, `changed_by_user_id`, `notes`, `created_at`) VALUES ('26', '41', NULL, 'DRAFT', '1', 'Draft order initialized', '2026-09-09 06:53:54');
INSERT INTO `order_status_history` (`id`, `order_id`, `previous_status`, `new_status`, `changed_by_user_id`, `notes`, `created_at`) VALUES ('27', '41', 'DRAFT', 'SUBMITTED', '1', 'Order submitted for kitchen & counter routing', '2026-09-09 06:53:54');
INSERT INTO `order_status_history` (`id`, `order_id`, `previous_status`, `new_status`, `changed_by_user_id`, `notes`, `created_at`) VALUES ('28', '41', 'SUBMITTED', 'COMPLETED', '1', 'Status advanced to COMPLETED', '2026-09-09 06:53:54');
INSERT INTO `order_status_history` (`id`, `order_id`, `previous_status`, `new_status`, `changed_by_user_id`, `notes`, `created_at`) VALUES ('29', '42', NULL, 'DRAFT', '1', 'Draft order initialized', '2026-09-09 06:53:54');
INSERT INTO `order_status_history` (`id`, `order_id`, `previous_status`, `new_status`, `changed_by_user_id`, `notes`, `created_at`) VALUES ('30', '42', 'DRAFT', 'SUBMITTED', '1', 'Order submitted for kitchen & counter routing', '2026-09-09 06:53:54');
INSERT INTO `order_status_history` (`id`, `order_id`, `previous_status`, `new_status`, `changed_by_user_id`, `notes`, `created_at`) VALUES ('31', '43', NULL, 'DRAFT', '1', 'Draft order initialized', '2026-09-09 06:54:03');
INSERT INTO `order_status_history` (`id`, `order_id`, `previous_status`, `new_status`, `changed_by_user_id`, `notes`, `created_at`) VALUES ('32', '43', 'DRAFT', 'SUBMITTED', '1', 'Order submitted for kitchen & counter routing', '2026-09-09 06:54:03');
INSERT INTO `order_status_history` (`id`, `order_id`, `previous_status`, `new_status`, `changed_by_user_id`, `notes`, `created_at`) VALUES ('33', '43', 'SUBMITTED', 'COMPLETED', '1', 'Status advanced to COMPLETED', '2026-09-09 06:54:03');
INSERT INTO `order_status_history` (`id`, `order_id`, `previous_status`, `new_status`, `changed_by_user_id`, `notes`, `created_at`) VALUES ('34', '44', NULL, 'DRAFT', '1', 'Draft order initialized', '2026-09-09 06:54:03');
INSERT INTO `order_status_history` (`id`, `order_id`, `previous_status`, `new_status`, `changed_by_user_id`, `notes`, `created_at`) VALUES ('35', '44', 'DRAFT', 'SUBMITTED', '1', 'Order submitted for kitchen & counter routing', '2026-09-09 06:54:03');

-- Table structure for `order_ticket_items`
DROP TABLE IF EXISTS `order_ticket_items`;
CREATE TABLE `order_ticket_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_ticket_id` int(10) unsigned NOT NULL,
  `order_item_id` int(10) unsigned NOT NULL,
  `product_name` varchar(150) NOT NULL,
  `variant_name` varchar(80) DEFAULT NULL,
  `quantity` int(10) unsigned NOT NULL DEFAULT 1,
  `modifiers_snapshot` text DEFAULT NULL,
  `special_instructions` text DEFAULT NULL,
  `item_status` enum('NEW','PREPARING','READY','SERVED','CANCELLED') DEFAULT 'NEW',
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `order_ticket_id` (`order_ticket_id`),
  KEY `order_item_id` (`order_item_id`),
  CONSTRAINT `order_ticket_items_ibfk_1` FOREIGN KEY (`order_ticket_id`) REFERENCES `order_tickets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_ticket_items_ibfk_2` FOREIGN KEY (`order_item_id`) REFERENCES `order_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `order_ticket_items`
INSERT INTO `order_ticket_items` (`id`, `order_ticket_id`, `order_item_id`, `product_name`, `variant_name`, `quantity`, `modifiers_snapshot`, `special_instructions`, `item_status`, `created_at`) VALUES ('1', '1', '1', 'Chef Special Burger', NULL, '1', NULL, 'Prep note for Chef Special Burger', 'NEW', '2026-09-09 05:40:50');
INSERT INTO `order_ticket_items` (`id`, `order_ticket_id`, `order_item_id`, `product_name`, `variant_name`, `quantity`, `modifiers_snapshot`, `special_instructions`, `item_status`, `created_at`) VALUES ('2', '2', '2', 'Iced Americano', NULL, '1', NULL, 'Prep note for Iced Americano', 'NEW', '2026-09-09 05:40:50');
INSERT INTO `order_ticket_items` (`id`, `order_ticket_id`, `order_item_id`, `product_name`, `variant_name`, `quantity`, `modifiers_snapshot`, `special_instructions`, `item_status`, `created_at`) VALUES ('3', '3', '3', 'Molten Chocolate Cake', NULL, '1', NULL, 'Prep note for Molten Chocolate Cake', 'NEW', '2026-09-09 05:40:50');
INSERT INTO `order_ticket_items` (`id`, `order_ticket_id`, `order_item_id`, `product_name`, `variant_name`, `quantity`, `modifiers_snapshot`, `special_instructions`, `item_status`, `created_at`) VALUES ('4', '4', '4', 'Chef Special Burger', NULL, '1', NULL, 'Prep note for Chef Special Burger', 'NEW', '2026-09-09 05:41:01');
INSERT INTO `order_ticket_items` (`id`, `order_ticket_id`, `order_item_id`, `product_name`, `variant_name`, `quantity`, `modifiers_snapshot`, `special_instructions`, `item_status`, `created_at`) VALUES ('5', '5', '5', 'Iced Americano', NULL, '1', NULL, 'Prep note for Iced Americano', 'NEW', '2026-09-09 05:41:01');
INSERT INTO `order_ticket_items` (`id`, `order_ticket_id`, `order_item_id`, `product_name`, `variant_name`, `quantity`, `modifiers_snapshot`, `special_instructions`, `item_status`, `created_at`) VALUES ('6', '6', '6', 'Molten Chocolate Cake', NULL, '1', NULL, 'Prep note for Molten Chocolate Cake', 'NEW', '2026-09-09 05:41:01');
INSERT INTO `order_ticket_items` (`id`, `order_ticket_id`, `order_item_id`, `product_name`, `variant_name`, `quantity`, `modifiers_snapshot`, `special_instructions`, `item_status`, `created_at`) VALUES ('7', '7', '7', 'Chef Special Burger', NULL, '1', NULL, 'Prep note for Chef Special Burger', 'NEW', '2026-09-09 05:41:14');
INSERT INTO `order_ticket_items` (`id`, `order_ticket_id`, `order_item_id`, `product_name`, `variant_name`, `quantity`, `modifiers_snapshot`, `special_instructions`, `item_status`, `created_at`) VALUES ('8', '8', '8', 'Iced Americano', NULL, '1', NULL, 'Prep note for Iced Americano', 'NEW', '2026-09-09 05:41:14');
INSERT INTO `order_ticket_items` (`id`, `order_ticket_id`, `order_item_id`, `product_name`, `variant_name`, `quantity`, `modifiers_snapshot`, `special_instructions`, `item_status`, `created_at`) VALUES ('9', '9', '9', 'Molten Chocolate Cake', NULL, '1', NULL, 'Prep note for Molten Chocolate Cake', 'NEW', '2026-09-09 05:41:14');
INSERT INTO `order_ticket_items` (`id`, `order_ticket_id`, `order_item_id`, `product_name`, `variant_name`, `quantity`, `modifiers_snapshot`, `special_instructions`, `item_status`, `created_at`) VALUES ('10', '10', '10', 'Chef Special Burger', NULL, '1', NULL, 'Prep note for Chef Special Burger', 'NEW', '2026-09-09 05:41:27');
INSERT INTO `order_ticket_items` (`id`, `order_ticket_id`, `order_item_id`, `product_name`, `variant_name`, `quantity`, `modifiers_snapshot`, `special_instructions`, `item_status`, `created_at`) VALUES ('11', '11', '11', 'Iced Americano', NULL, '1', NULL, 'Prep note for Iced Americano', 'NEW', '2026-09-09 05:41:27');
INSERT INTO `order_ticket_items` (`id`, `order_ticket_id`, `order_item_id`, `product_name`, `variant_name`, `quantity`, `modifiers_snapshot`, `special_instructions`, `item_status`, `created_at`) VALUES ('12', '12', '12', 'Molten Chocolate Cake', NULL, '1', NULL, 'Prep note for Molten Chocolate Cake', 'NEW', '2026-09-09 05:41:27');
INSERT INTO `order_ticket_items` (`id`, `order_ticket_id`, `order_item_id`, `product_name`, `variant_name`, `quantity`, `modifiers_snapshot`, `special_instructions`, `item_status`, `created_at`) VALUES ('13', '13', '13', 'Chef Special Burger', NULL, '1', NULL, 'Prep note for Chef Special Burger', 'READY', '2026-09-09 05:41:47');
INSERT INTO `order_ticket_items` (`id`, `order_ticket_id`, `order_item_id`, `product_name`, `variant_name`, `quantity`, `modifiers_snapshot`, `special_instructions`, `item_status`, `created_at`) VALUES ('14', '14', '14', 'Iced Americano', NULL, '1', NULL, 'Prep note for Iced Americano', 'NEW', '2026-09-09 05:41:47');
INSERT INTO `order_ticket_items` (`id`, `order_ticket_id`, `order_item_id`, `product_name`, `variant_name`, `quantity`, `modifiers_snapshot`, `special_instructions`, `item_status`, `created_at`) VALUES ('15', '15', '15', 'Molten Chocolate Cake', NULL, '1', NULL, 'Prep note for Molten Chocolate Cake', 'NEW', '2026-09-09 05:41:47');
INSERT INTO `order_ticket_items` (`id`, `order_ticket_id`, `order_item_id`, `product_name`, `variant_name`, `quantity`, `modifiers_snapshot`, `special_instructions`, `item_status`, `created_at`) VALUES ('16', '16', '28', 'Chef Special Burger', NULL, '2', NULL, NULL, 'PREPARING', '2026-09-09 05:55:33');
INSERT INTO `order_ticket_items` (`id`, `order_ticket_id`, `order_item_id`, `product_name`, `variant_name`, `quantity`, `modifiers_snapshot`, `special_instructions`, `item_status`, `created_at`) VALUES ('17', '17', '29', 'Iced Americano', NULL, '2', NULL, NULL, 'NEW', '2026-09-09 05:55:33');
INSERT INTO `order_ticket_items` (`id`, `order_ticket_id`, `order_item_id`, `product_name`, `variant_name`, `quantity`, `modifiers_snapshot`, `special_instructions`, `item_status`, `created_at`) VALUES ('18', '18', '30', 'Chef Special Burger', NULL, '2', NULL, NULL, 'PREPARING', '2026-09-09 05:55:46');
INSERT INTO `order_ticket_items` (`id`, `order_ticket_id`, `order_item_id`, `product_name`, `variant_name`, `quantity`, `modifiers_snapshot`, `special_instructions`, `item_status`, `created_at`) VALUES ('19', '19', '31', 'Iced Americano', NULL, '2', NULL, NULL, 'NEW', '2026-09-09 05:55:46');
INSERT INTO `order_ticket_items` (`id`, `order_ticket_id`, `order_item_id`, `product_name`, `variant_name`, `quantity`, `modifiers_snapshot`, `special_instructions`, `item_status`, `created_at`) VALUES ('20', '20', '30', 'Chef Special Burger', NULL, '2', NULL, '[RE-FIRE #1] Burned Patty / Kitchen remake', 'NEW', '2026-09-09 05:55:46');
INSERT INTO `order_ticket_items` (`id`, `order_ticket_id`, `order_item_id`, `product_name`, `variant_name`, `quantity`, `modifiers_snapshot`, `special_instructions`, `item_status`, `created_at`) VALUES ('21', '21', '32', 'Chef Special Burger', NULL, '2', NULL, NULL, 'PREPARING', '2026-09-09 05:56:00');
INSERT INTO `order_ticket_items` (`id`, `order_ticket_id`, `order_item_id`, `product_name`, `variant_name`, `quantity`, `modifiers_snapshot`, `special_instructions`, `item_status`, `created_at`) VALUES ('22', '22', '33', 'Iced Americano', NULL, '2', NULL, NULL, 'CANCELLED', '2026-09-09 05:56:00');
INSERT INTO `order_ticket_items` (`id`, `order_ticket_id`, `order_item_id`, `product_name`, `variant_name`, `quantity`, `modifiers_snapshot`, `special_instructions`, `item_status`, `created_at`) VALUES ('23', '23', '32', 'Chef Special Burger', NULL, '2', NULL, '[RE-FIRE #1] Burned Patty / Kitchen remake', 'READY', '2026-09-09 05:56:00');
INSERT INTO `order_ticket_items` (`id`, `order_ticket_id`, `order_item_id`, `product_name`, `variant_name`, `quantity`, `modifiers_snapshot`, `special_instructions`, `item_status`, `created_at`) VALUES ('24', '24', '34', 'Chef Special Burger', NULL, '2', NULL, NULL, 'READY', '2026-09-09 05:56:07');
INSERT INTO `order_ticket_items` (`id`, `order_ticket_id`, `order_item_id`, `product_name`, `variant_name`, `quantity`, `modifiers_snapshot`, `special_instructions`, `item_status`, `created_at`) VALUES ('25', '25', '35', 'Iced Americano', NULL, '2', NULL, NULL, 'CANCELLED', '2026-09-09 05:56:07');
INSERT INTO `order_ticket_items` (`id`, `order_ticket_id`, `order_item_id`, `product_name`, `variant_name`, `quantity`, `modifiers_snapshot`, `special_instructions`, `item_status`, `created_at`) VALUES ('26', '26', '34', 'Chef Special Burger', NULL, '2', NULL, '[RE-FIRE #1] Burned Patty / Kitchen remake', 'READY', '2026-09-09 05:56:07');
INSERT INTO `order_ticket_items` (`id`, `order_ticket_id`, `order_item_id`, `product_name`, `variant_name`, `quantity`, `modifiers_snapshot`, `special_instructions`, `item_status`, `created_at`) VALUES ('27', '28', '38', 'Chef Special Burger', NULL, '2', NULL, NULL, 'PREPARING', '2026-09-09 06:05:43');
INSERT INTO `order_ticket_items` (`id`, `order_ticket_id`, `order_item_id`, `product_name`, `variant_name`, `quantity`, `modifiers_snapshot`, `special_instructions`, `item_status`, `created_at`) VALUES ('28', '29', '39', 'Chef Special Burger', NULL, '2', NULL, NULL, 'PREPARING', '2026-09-09 06:06:06');
INSERT INTO `order_ticket_items` (`id`, `order_ticket_id`, `order_item_id`, `product_name`, `variant_name`, `quantity`, `modifiers_snapshot`, `special_instructions`, `item_status`, `created_at`) VALUES ('29', '30', '40', 'Chef Special Burger', NULL, '2', NULL, NULL, 'PREPARING', '2026-09-09 06:06:20');
INSERT INTO `order_ticket_items` (`id`, `order_ticket_id`, `order_item_id`, `product_name`, `variant_name`, `quantity`, `modifiers_snapshot`, `special_instructions`, `item_status`, `created_at`) VALUES ('30', '31', '41', 'Chef Special Burger', NULL, '2', NULL, NULL, 'PREPARING', '2026-09-09 06:06:27');
INSERT INTO `order_ticket_items` (`id`, `order_ticket_id`, `order_item_id`, `product_name`, `variant_name`, `quantity`, `modifiers_snapshot`, `special_instructions`, `item_status`, `created_at`) VALUES ('31', '32', '48', 'Chef Special Burger', NULL, '1', NULL, NULL, 'NEW', '2026-09-09 06:53:54');
INSERT INTO `order_ticket_items` (`id`, `order_ticket_id`, `order_item_id`, `product_name`, `variant_name`, `quantity`, `modifiers_snapshot`, `special_instructions`, `item_status`, `created_at`) VALUES ('32', '33', '50', 'Chef Special Burger', NULL, '1', NULL, NULL, 'NEW', '2026-09-09 06:54:03');

-- Table structure for `order_ticket_status_history`
DROP TABLE IF EXISTS `order_ticket_status_history`;
CREATE TABLE `order_ticket_status_history` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_ticket_id` int(10) unsigned NOT NULL,
  `from_status` varchar(30) DEFAULT NULL,
  `to_status` varchar(30) NOT NULL,
  `changed_by_user_id` int(10) unsigned DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_ticket_history` (`order_ticket_id`,`created_at`),
  CONSTRAINT `order_ticket_status_history_ibfk_1` FOREIGN KEY (`order_ticket_id`) REFERENCES `order_tickets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `order_ticket_status_history`
INSERT INTO `order_ticket_status_history` (`id`, `order_ticket_id`, `from_status`, `to_status`, `changed_by_user_id`, `reason`, `created_at`) VALUES ('1', '16', 'NEW', 'PREPARING', '1', 'Transition NEW -> PREPARING', '2026-09-09 05:55:33');
INSERT INTO `order_ticket_status_history` (`id`, `order_ticket_id`, `from_status`, `to_status`, `changed_by_user_id`, `reason`, `created_at`) VALUES ('2', '16', 'PREPARING', 'READY', '1', 'Transition PREPARING -> READY', '2026-09-09 05:55:33');
INSERT INTO `order_ticket_status_history` (`id`, `order_ticket_id`, `from_status`, `to_status`, `changed_by_user_id`, `reason`, `created_at`) VALUES ('3', '16', 'READY', 'PREPARING', '1', 'Ticket recalled: Kitchen pass recall for extra sauce', '2026-09-09 05:55:33');
INSERT INTO `order_ticket_status_history` (`id`, `order_ticket_id`, `from_status`, `to_status`, `changed_by_user_id`, `reason`, `created_at`) VALUES ('4', '18', 'NEW', 'PREPARING', '1', 'Transition NEW -> PREPARING', '2026-09-09 05:55:46');
INSERT INTO `order_ticket_status_history` (`id`, `order_ticket_id`, `from_status`, `to_status`, `changed_by_user_id`, `reason`, `created_at`) VALUES ('5', '18', 'PREPARING', 'READY', '1', 'Transition PREPARING -> READY', '2026-09-09 05:55:46');
INSERT INTO `order_ticket_status_history` (`id`, `order_ticket_id`, `from_status`, `to_status`, `changed_by_user_id`, `reason`, `created_at`) VALUES ('6', '18', 'READY', 'PREPARING', '1', 'Ticket recalled: Kitchen pass recall for extra sauce', '2026-09-09 05:55:46');
INSERT INTO `order_ticket_status_history` (`id`, `order_ticket_id`, `from_status`, `to_status`, `changed_by_user_id`, `reason`, `created_at`) VALUES ('7', '18', 'PREPARING', 'PREPARING', '1', 'Re-fire requested. Generated ticket #TKT-ST-KITCHEN-REFIRE-20260908-8080 (Reason: Burned Patty / Kitchen remake)', '2026-09-09 05:55:46');
INSERT INTO `order_ticket_status_history` (`id`, `order_ticket_id`, `from_status`, `to_status`, `changed_by_user_id`, `reason`, `created_at`) VALUES ('8', '20', 'NONE', 'NEW', '1', 'Re-fired from ticket #TKT-ST-KITCHEN-20260908-6375 (Reason: Burned Patty / Kitchen remake)', '2026-09-09 05:55:46');
INSERT INTO `order_ticket_status_history` (`id`, `order_ticket_id`, `from_status`, `to_status`, `changed_by_user_id`, `reason`, `created_at`) VALUES ('9', '21', 'NEW', 'PREPARING', '1', 'Transition NEW -> PREPARING', '2026-09-09 05:56:00');
INSERT INTO `order_ticket_status_history` (`id`, `order_ticket_id`, `from_status`, `to_status`, `changed_by_user_id`, `reason`, `created_at`) VALUES ('10', '21', 'PREPARING', 'READY', '1', 'Transition PREPARING -> READY', '2026-09-09 05:56:00');
INSERT INTO `order_ticket_status_history` (`id`, `order_ticket_id`, `from_status`, `to_status`, `changed_by_user_id`, `reason`, `created_at`) VALUES ('11', '21', 'READY', 'PREPARING', '1', 'Ticket recalled: Kitchen pass recall for extra sauce', '2026-09-09 05:56:00');
INSERT INTO `order_ticket_status_history` (`id`, `order_ticket_id`, `from_status`, `to_status`, `changed_by_user_id`, `reason`, `created_at`) VALUES ('12', '21', 'PREPARING', 'PREPARING', '1', 'Re-fire requested. Generated ticket #TKT-ST-KITCHEN-REFIRE-20260908-2413 (Reason: Burned Patty / Kitchen remake)', '2026-09-09 05:56:00');
INSERT INTO `order_ticket_status_history` (`id`, `order_ticket_id`, `from_status`, `to_status`, `changed_by_user_id`, `reason`, `created_at`) VALUES ('13', '23', 'NONE', 'NEW', '1', 'Re-fired from ticket #TKT-ST-KITCHEN-20260908-8797 (Reason: Burned Patty / Kitchen remake)', '2026-09-09 05:56:00');
INSERT INTO `order_ticket_status_history` (`id`, `order_ticket_id`, `from_status`, `to_status`, `changed_by_user_id`, `reason`, `created_at`) VALUES ('14', '22', 'NEW', 'CANCELLED', '1', 'Ticket cancelled: Customer changed mind', '2026-09-09 05:56:00');
INSERT INTO `order_ticket_status_history` (`id`, `order_ticket_id`, `from_status`, `to_status`, `changed_by_user_id`, `reason`, `created_at`) VALUES ('15', '23', 'NEW', 'PREPARING', '1', 'Transition NEW -> PREPARING', '2026-09-09 05:56:00');
INSERT INTO `order_ticket_status_history` (`id`, `order_ticket_id`, `from_status`, `to_status`, `changed_by_user_id`, `reason`, `created_at`) VALUES ('16', '23', 'PREPARING', 'READY', '1', 'Transition PREPARING -> READY', '2026-09-09 05:56:00');
INSERT INTO `order_ticket_status_history` (`id`, `order_ticket_id`, `from_status`, `to_status`, `changed_by_user_id`, `reason`, `created_at`) VALUES ('17', '24', 'NEW', 'PREPARING', '1', 'Transition NEW -> PREPARING', '2026-09-09 05:56:07');
INSERT INTO `order_ticket_status_history` (`id`, `order_ticket_id`, `from_status`, `to_status`, `changed_by_user_id`, `reason`, `created_at`) VALUES ('18', '24', 'PREPARING', 'READY', '1', 'Transition PREPARING -> READY', '2026-09-09 05:56:07');
INSERT INTO `order_ticket_status_history` (`id`, `order_ticket_id`, `from_status`, `to_status`, `changed_by_user_id`, `reason`, `created_at`) VALUES ('19', '24', 'READY', 'PREPARING', '1', 'Ticket recalled: Kitchen pass recall for extra sauce', '2026-09-09 05:56:07');
INSERT INTO `order_ticket_status_history` (`id`, `order_ticket_id`, `from_status`, `to_status`, `changed_by_user_id`, `reason`, `created_at`) VALUES ('20', '24', 'PREPARING', 'PREPARING', '1', 'Re-fire requested. Generated ticket #TKT-ST-KITCHEN-REFIRE-20260908-6963 (Reason: Burned Patty / Kitchen remake)', '2026-09-09 05:56:07');
INSERT INTO `order_ticket_status_history` (`id`, `order_ticket_id`, `from_status`, `to_status`, `changed_by_user_id`, `reason`, `created_at`) VALUES ('21', '26', 'NONE', 'NEW', '1', 'Re-fired from ticket #TKT-ST-KITCHEN-20260908-5549 (Reason: Burned Patty / Kitchen remake)', '2026-09-09 05:56:07');
INSERT INTO `order_ticket_status_history` (`id`, `order_ticket_id`, `from_status`, `to_status`, `changed_by_user_id`, `reason`, `created_at`) VALUES ('22', '25', 'NEW', 'CANCELLED', '1', 'Ticket cancelled: Customer changed mind', '2026-09-09 05:56:07');
INSERT INTO `order_ticket_status_history` (`id`, `order_ticket_id`, `from_status`, `to_status`, `changed_by_user_id`, `reason`, `created_at`) VALUES ('23', '24', 'PREPARING', 'READY', '1', 'Transition PREPARING -> READY', '2026-09-09 05:56:07');
INSERT INTO `order_ticket_status_history` (`id`, `order_ticket_id`, `from_status`, `to_status`, `changed_by_user_id`, `reason`, `created_at`) VALUES ('24', '26', 'NEW', 'PREPARING', '1', 'Transition NEW -> PREPARING', '2026-09-09 05:56:07');
INSERT INTO `order_ticket_status_history` (`id`, `order_ticket_id`, `from_status`, `to_status`, `changed_by_user_id`, `reason`, `created_at`) VALUES ('25', '26', 'PREPARING', 'READY', '1', 'Transition PREPARING -> READY', '2026-09-09 05:56:07');

-- Table structure for `order_tickets`
DROP TABLE IF EXISTS `order_tickets`;
CREATE TABLE `order_tickets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL,
  `order_route_id` int(10) unsigned DEFAULT NULL,
  `station_id` int(10) unsigned NOT NULL,
  `ticket_number` varchar(50) NOT NULL,
  `status` enum('NEW','PREPARING','READY','SERVED','CANCELLED') DEFAULT 'NEW',
  `priority` enum('NORMAL','HIGH','URGENT') DEFAULT 'NORMAL',
  `created_by` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `started_at` datetime DEFAULT NULL,
  `ready_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `refire_count` int(10) unsigned DEFAULT 0,
  `original_ticket_id` int(10) unsigned DEFAULT NULL,
  `recalled_at` datetime DEFAULT NULL,
  `recalled_by` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_ticket_number` (`ticket_number`),
  KEY `order_id` (`order_id`),
  KEY `idx_ticket_original` (`original_ticket_id`),
  KEY `idx_queue_lookup` (`station_id`,`status`,`priority`,`created_at`),
  KEY `idx_tickets_station_created` (`station_id`,`created_at`),
  KEY `idx_tickets_status_created` (`status`,`created_at`),
  CONSTRAINT `order_tickets_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_tickets_ibfk_2` FOREIGN KEY (`station_id`) REFERENCES `stations` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for `order_tickets`
INSERT INTO `order_tickets` (`id`, `order_id`, `order_route_id`, `station_id`, `ticket_number`, `status`, `priority`, `created_by`, `created_at`, `started_at`, `ready_at`, `completed_at`, `refire_count`, `original_ticket_id`, `recalled_at`, `recalled_by`) VALUES ('1', '3', '1', '1', 'TKT-ST-KITCHEN-20260909-8488', 'NEW', 'NORMAL', '1', '2026-09-09 05:40:50', NULL, NULL, NULL, '0', NULL, NULL, NULL);
INSERT INTO `order_tickets` (`id`, `order_id`, `order_route_id`, `station_id`, `ticket_number`, `status`, `priority`, `created_by`, `created_at`, `started_at`, `ready_at`, `completed_at`, `refire_count`, `original_ticket_id`, `recalled_at`, `recalled_by`) VALUES ('2', '3', '2', '2', 'TKT-ST-BAR-20260909-2643', 'NEW', 'NORMAL', '1', '2026-09-09 05:40:50', NULL, NULL, NULL, '0', NULL, NULL, NULL);
INSERT INTO `order_tickets` (`id`, `order_id`, `order_route_id`, `station_id`, `ticket_number`, `status`, `priority`, `created_by`, `created_at`, `started_at`, `ready_at`, `completed_at`, `refire_count`, `original_ticket_id`, `recalled_at`, `recalled_by`) VALUES ('3', '3', '3', '4', 'TKT-ST-DESSERT-20260909-8556', 'NEW', 'NORMAL', '1', '2026-09-09 05:40:50', NULL, NULL, NULL, '0', NULL, NULL, NULL);
INSERT INTO `order_tickets` (`id`, `order_id`, `order_route_id`, `station_id`, `ticket_number`, `status`, `priority`, `created_by`, `created_at`, `started_at`, `ready_at`, `completed_at`, `refire_count`, `original_ticket_id`, `recalled_at`, `recalled_by`) VALUES ('4', '4', '4', '1', 'TKT-ST-KITCHEN-20260909-8262', 'NEW', 'NORMAL', '1', '2026-09-09 05:41:01', NULL, NULL, NULL, '0', NULL, NULL, NULL);
INSERT INTO `order_tickets` (`id`, `order_id`, `order_route_id`, `station_id`, `ticket_number`, `status`, `priority`, `created_by`, `created_at`, `started_at`, `ready_at`, `completed_at`, `refire_count`, `original_ticket_id`, `recalled_at`, `recalled_by`) VALUES ('5', '4', '5', '2', 'TKT-ST-BAR-20260909-5408', 'NEW', 'NORMAL', '1', '2026-09-09 05:41:01', NULL, NULL, NULL, '0', NULL, NULL, NULL);
INSERT INTO `order_tickets` (`id`, `order_id`, `order_route_id`, `station_id`, `ticket_number`, `status`, `priority`, `created_by`, `created_at`, `started_at`, `ready_at`, `completed_at`, `refire_count`, `original_ticket_id`, `recalled_at`, `recalled_by`) VALUES ('6', '4', '6', '4', 'TKT-ST-DESSERT-20260909-9145', 'NEW', 'NORMAL', '1', '2026-09-09 05:41:01', NULL, NULL, NULL, '0', NULL, NULL, NULL);
INSERT INTO `order_tickets` (`id`, `order_id`, `order_route_id`, `station_id`, `ticket_number`, `status`, `priority`, `created_by`, `created_at`, `started_at`, `ready_at`, `completed_at`, `refire_count`, `original_ticket_id`, `recalled_at`, `recalled_by`) VALUES ('7', '5', '7', '1', 'TKT-ST-KITCHEN-20260909-9975', 'NEW', 'NORMAL', '1', '2026-09-09 05:41:14', NULL, NULL, NULL, '0', NULL, NULL, NULL);
INSERT INTO `order_tickets` (`id`, `order_id`, `order_route_id`, `station_id`, `ticket_number`, `status`, `priority`, `created_by`, `created_at`, `started_at`, `ready_at`, `completed_at`, `refire_count`, `original_ticket_id`, `recalled_at`, `recalled_by`) VALUES ('8', '5', '8', '2', 'TKT-ST-BAR-20260909-9399', 'NEW', 'NORMAL', '1', '2026-09-09 05:41:14', NULL, NULL, NULL, '0', NULL, NULL, NULL);
INSERT INTO `order_tickets` (`id`, `order_id`, `order_route_id`, `station_id`, `ticket_number`, `status`, `priority`, `created_by`, `created_at`, `started_at`, `ready_at`, `completed_at`, `refire_count`, `original_ticket_id`, `recalled_at`, `recalled_by`) VALUES ('9', '5', '9', '4', 'TKT-ST-DESSERT-20260909-9686', 'NEW', 'NORMAL', '1', '2026-09-09 05:41:14', NULL, NULL, NULL, '0', NULL, NULL, NULL);
INSERT INTO `order_tickets` (`id`, `order_id`, `order_route_id`, `station_id`, `ticket_number`, `status`, `priority`, `created_by`, `created_at`, `started_at`, `ready_at`, `completed_at`, `refire_count`, `original_ticket_id`, `recalled_at`, `recalled_by`) VALUES ('10', '6', '10', '1', 'TKT-ST-KITCHEN-20260909-8157', 'NEW', 'NORMAL', '1', '2026-09-09 05:41:27', NULL, NULL, NULL, '0', NULL, NULL, NULL);
INSERT INTO `order_tickets` (`id`, `order_id`, `order_route_id`, `station_id`, `ticket_number`, `status`, `priority`, `created_by`, `created_at`, `started_at`, `ready_at`, `completed_at`, `refire_count`, `original_ticket_id`, `recalled_at`, `recalled_by`) VALUES ('11', '6', '11', '2', 'TKT-ST-BAR-20260909-9364', 'NEW', 'NORMAL', '1', '2026-09-09 05:41:27', NULL, NULL, NULL, '0', NULL, NULL, NULL);
INSERT INTO `order_tickets` (`id`, `order_id`, `order_route_id`, `station_id`, `ticket_number`, `status`, `priority`, `created_by`, `created_at`, `started_at`, `ready_at`, `completed_at`, `refire_count`, `original_ticket_id`, `recalled_at`, `recalled_by`) VALUES ('12', '6', '12', '4', 'TKT-ST-DESSERT-20260909-2702', 'NEW', 'NORMAL', '1', '2026-09-09 05:41:27', NULL, NULL, NULL, '0', NULL, NULL, NULL);
INSERT INTO `order_tickets` (`id`, `order_id`, `order_route_id`, `station_id`, `ticket_number`, `status`, `priority`, `created_by`, `created_at`, `started_at`, `ready_at`, `completed_at`, `refire_count`, `original_ticket_id`, `recalled_at`, `recalled_by`) VALUES ('13', '7', '13', '1', 'TKT-ST-KITCHEN-20260909-7103', 'READY', 'NORMAL', '1', '2026-09-09 05:41:47', '2026-09-09 05:41:47', '2026-09-09 05:41:47', NULL, '0', NULL, NULL, NULL);
INSERT INTO `order_tickets` (`id`, `order_id`, `order_route_id`, `station_id`, `ticket_number`, `status`, `priority`, `created_by`, `created_at`, `started_at`, `ready_at`, `completed_at`, `refire_count`, `original_ticket_id`, `recalled_at`, `recalled_by`) VALUES ('14', '7', '14', '2', 'TKT-ST-BAR-20260909-2914', 'NEW', 'NORMAL', '1', '2026-09-09 05:41:47', NULL, NULL, NULL, '0', NULL, NULL, NULL);
INSERT INTO `order_tickets` (`id`, `order_id`, `order_route_id`, `station_id`, `ticket_number`, `status`, `priority`, `created_by`, `created_at`, `started_at`, `ready_at`, `completed_at`, `refire_count`, `original_ticket_id`, `recalled_at`, `recalled_by`) VALUES ('15', '7', '15', '4', 'TKT-ST-DESSERT-20260909-4142', 'NEW', 'NORMAL', '1', '2026-09-09 05:41:47', NULL, NULL, NULL, '0', NULL, NULL, NULL);
INSERT INTO `order_tickets` (`id`, `order_id`, `order_route_id`, `station_id`, `ticket_number`, `status`, `priority`, `created_by`, `created_at`, `started_at`, `ready_at`, `completed_at`, `refire_count`, `original_ticket_id`, `recalled_at`, `recalled_by`) VALUES ('16', '24', '16', '1', 'TKT-ST-KITCHEN-20260908-2680', 'PREPARING', 'HIGH', '1', '2026-09-09 05:55:33', '2026-09-09 05:55:33', '2026-09-09 05:55:33', NULL, '0', NULL, '2026-09-09 05:55:33', '1');
INSERT INTO `order_tickets` (`id`, `order_id`, `order_route_id`, `station_id`, `ticket_number`, `status`, `priority`, `created_by`, `created_at`, `started_at`, `ready_at`, `completed_at`, `refire_count`, `original_ticket_id`, `recalled_at`, `recalled_by`) VALUES ('17', '24', '17', '2', 'TKT-ST-BAR-20260908-6581', 'NEW', 'HIGH', '1', '2026-09-09 05:55:33', NULL, NULL, NULL, '0', NULL, NULL, NULL);
INSERT INTO `order_tickets` (`id`, `order_id`, `order_route_id`, `station_id`, `ticket_number`, `status`, `priority`, `created_by`, `created_at`, `started_at`, `ready_at`, `completed_at`, `refire_count`, `original_ticket_id`, `recalled_at`, `recalled_by`) VALUES ('18', '25', '18', '1', 'TKT-ST-KITCHEN-20260908-6375', 'PREPARING', 'HIGH', '1', '2026-09-09 05:55:46', '2026-09-09 05:55:46', '2026-09-09 05:55:46', NULL, '1', NULL, '2026-09-09 05:55:46', '1');
INSERT INTO `order_tickets` (`id`, `order_id`, `order_route_id`, `station_id`, `ticket_number`, `status`, `priority`, `created_by`, `created_at`, `started_at`, `ready_at`, `completed_at`, `refire_count`, `original_ticket_id`, `recalled_at`, `recalled_by`) VALUES ('19', '25', '19', '2', 'TKT-ST-BAR-20260908-4015', 'NEW', 'HIGH', '1', '2026-09-09 05:55:46', NULL, NULL, NULL, '0', NULL, NULL, NULL);
INSERT INTO `order_tickets` (`id`, `order_id`, `order_route_id`, `station_id`, `ticket_number`, `status`, `priority`, `created_by`, `created_at`, `started_at`, `ready_at`, `completed_at`, `refire_count`, `original_ticket_id`, `recalled_at`, `recalled_by`) VALUES ('20', '25', '18', '1', 'TKT-ST-KITCHEN-REFIRE-20260908-8080', 'NEW', 'URGENT', '1', '2026-09-09 05:55:46', NULL, NULL, NULL, '1', '18', NULL, NULL);
INSERT INTO `order_tickets` (`id`, `order_id`, `order_route_id`, `station_id`, `ticket_number`, `status`, `priority`, `created_by`, `created_at`, `started_at`, `ready_at`, `completed_at`, `refire_count`, `original_ticket_id`, `recalled_at`, `recalled_by`) VALUES ('21', '26', '20', '1', 'TKT-ST-KITCHEN-20260908-8797', 'PREPARING', 'HIGH', '1', '2026-09-09 05:56:00', '2026-09-09 05:56:00', '2026-09-09 05:56:00', NULL, '1', NULL, '2026-09-09 05:56:00', '1');
INSERT INTO `order_tickets` (`id`, `order_id`, `order_route_id`, `station_id`, `ticket_number`, `status`, `priority`, `created_by`, `created_at`, `started_at`, `ready_at`, `completed_at`, `refire_count`, `original_ticket_id`, `recalled_at`, `recalled_by`) VALUES ('22', '26', '21', '2', 'TKT-ST-BAR-20260908-1119', 'CANCELLED', 'HIGH', '1', '2026-09-09 05:56:00', NULL, NULL, NULL, '0', NULL, NULL, NULL);
INSERT INTO `order_tickets` (`id`, `order_id`, `order_route_id`, `station_id`, `ticket_number`, `status`, `priority`, `created_by`, `created_at`, `started_at`, `ready_at`, `completed_at`, `refire_count`, `original_ticket_id`, `recalled_at`, `recalled_by`) VALUES ('23', '26', '20', '1', 'TKT-ST-KITCHEN-REFIRE-20260908-2413', 'READY', 'URGENT', '1', '2026-09-09 05:56:00', '2026-09-09 05:56:00', '2026-09-09 05:56:00', NULL, '1', '21', NULL, NULL);
INSERT INTO `order_tickets` (`id`, `order_id`, `order_route_id`, `station_id`, `ticket_number`, `status`, `priority`, `created_by`, `created_at`, `started_at`, `ready_at`, `completed_at`, `refire_count`, `original_ticket_id`, `recalled_at`, `recalled_by`) VALUES ('24', '27', '22', '1', 'TKT-ST-KITCHEN-20260908-5549', 'READY', 'HIGH', '1', '2026-09-09 05:56:07', '2026-09-09 05:56:07', '2026-09-09 05:56:07', NULL, '1', NULL, '2026-09-09 05:56:07', '1');
INSERT INTO `order_tickets` (`id`, `order_id`, `order_route_id`, `station_id`, `ticket_number`, `status`, `priority`, `created_by`, `created_at`, `started_at`, `ready_at`, `completed_at`, `refire_count`, `original_ticket_id`, `recalled_at`, `recalled_by`) VALUES ('25', '27', '23', '2', 'TKT-ST-BAR-20260908-3903', 'CANCELLED', 'HIGH', '1', '2026-09-09 05:56:07', NULL, NULL, NULL, '0', NULL, NULL, NULL);
INSERT INTO `order_tickets` (`id`, `order_id`, `order_route_id`, `station_id`, `ticket_number`, `status`, `priority`, `created_by`, `created_at`, `started_at`, `ready_at`, `completed_at`, `refire_count`, `original_ticket_id`, `recalled_at`, `recalled_by`) VALUES ('26', '27', '22', '1', 'TKT-ST-KITCHEN-REFIRE-20260908-6963', 'READY', 'URGENT', '1', '2026-09-09 05:56:07', '2026-09-09 05:56:07', '2026-09-09 05:56:07', NULL, '1', '24', NULL, NULL);
INSERT INTO `order_tickets` (`id`, `order_id`, `order_route_id`, `station_id`, `ticket_number`, `status`, `priority`, `created_by`, `created_at`, `started_at`, `ready_at`, `completed_at`, `refire_count`, `original_ticket_id`, `recalled_at`, `recalled_by`) VALUES ('27', '31', NULL, '1', 'TCK-TEST-INV-642', 'PREPARING', 'NORMAL', NULL, '2026-09-09 06:05:31', NULL, NULL, NULL, '0', NULL, NULL, NULL);
INSERT INTO `order_tickets` (`id`, `order_id`, `order_route_id`, `station_id`, `ticket_number`, `status`, `priority`, `created_by`, `created_at`, `started_at`, `ready_at`, `completed_at`, `refire_count`, `original_ticket_id`, `recalled_at`, `recalled_by`) VALUES ('28', '32', NULL, '1', 'TCK-TEST-INV-873', 'PREPARING', 'NORMAL', NULL, '2026-09-09 06:05:43', NULL, NULL, NULL, '0', NULL, NULL, NULL);
INSERT INTO `order_tickets` (`id`, `order_id`, `order_route_id`, `station_id`, `ticket_number`, `status`, `priority`, `created_by`, `created_at`, `started_at`, `ready_at`, `completed_at`, `refire_count`, `original_ticket_id`, `recalled_at`, `recalled_by`) VALUES ('29', '33', NULL, '1', 'TCK-TEST-INV-645', 'PREPARING', 'NORMAL', NULL, '2026-09-09 06:06:06', NULL, NULL, NULL, '0', NULL, NULL, NULL);
INSERT INTO `order_tickets` (`id`, `order_id`, `order_route_id`, `station_id`, `ticket_number`, `status`, `priority`, `created_by`, `created_at`, `started_at`, `ready_at`, `completed_at`, `refire_count`, `original_ticket_id`, `recalled_at`, `recalled_by`) VALUES ('30', '34', NULL, '1', 'TCK-TEST-INV-795', 'PREPARING', 'NORMAL', NULL, '2026-09-09 06:06:20', NULL, NULL, NULL, '0', NULL, NULL, NULL);
INSERT INTO `order_tickets` (`id`, `order_id`, `order_route_id`, `station_id`, `ticket_number`, `status`, `priority`, `created_by`, `created_at`, `started_at`, `ready_at`, `completed_at`, `refire_count`, `original_ticket_id`, `recalled_at`, `recalled_by`) VALUES ('31', '35', NULL, '1', 'TCK-TEST-INV-837', 'PREPARING', 'NORMAL', NULL, '2026-09-09 06:06:27', NULL, NULL, NULL, '0', NULL, NULL, NULL);
INSERT INTO `order_tickets` (`id`, `order_id`, `order_route_id`, `station_id`, `ticket_number`, `status`, `priority`, `created_by`, `created_at`, `started_at`, `ready_at`, `completed_at`, `refire_count`, `original_ticket_id`, `recalled_at`, `recalled_by`) VALUES ('32', '42', '24', '1', 'TKT-ST-KITCHEN-20260909-4125', 'NEW', 'NORMAL', '1', '2026-09-09 06:53:54', NULL, NULL, NULL, '0', NULL, NULL, NULL);
INSERT INTO `order_tickets` (`id`, `order_id`, `order_route_id`, `station_id`, `ticket_number`, `status`, `priority`, `created_by`, `created_at`, `started_at`, `ready_at`, `completed_at`, `refire_count`, `original_ticket_id`, `recalled_at`, `recalled_by`) VALUES ('33', '44', '25', '1', 'TKT-ST-KITCHEN-20260909-9564', 'NEW', 'NORMAL', '1', '2026-09-09 06:54:03', NULL, NULL, NULL, '0', NULL, NULL, NULL);

-- Table structure for `orders`
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_number` varchar(40) NOT NULL,
  `branch_id` int(10) unsigned NOT NULL DEFAULT 1,
  `dining_session_id` int(10) unsigned DEFAULT NULL,
  `table_id` int(10) unsigned DEFAULT NULL,
  `customer_id` int(10) unsigned DEFAULT NULL,
  `taken_by_user_id` int(10) unsigned NOT NULL,
  `served_by_user_id` int(10) unsigned DEFAULT NULL,
  `order_type` enum('DINE_IN','TAKEAWAY','DELIVERY') DEFAULT 'DINE_IN',
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `tax` decimal(12,2) NOT NULL DEFAULT 0.00,
  `service_charge` decimal(12,2) NOT NULL DEFAULT 0.00,
  `delivery_charge` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `payment_status` enum('UNPAID','PARTIAL','PAID','REFUNDED') DEFAULT 'UNPAID',
  `order_status` enum('DRAFT','SUBMITTED','WAITING_PAYMENT','CONFIRMED','ROUTED','PREPARING','READY','SERVED','COMPLETED','CANCELLED','REFUNDED') DEFAULT 'SUBMITTED',
  `priority` enum('NORMAL','HIGH','URGENT') DEFAULT 'NORMAL',
  `notes` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `completed_at` timestamp NULL DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_number` (`order_number`),
  KEY `idx_order_number` (`order_number`),
  KEY `idx_order_created_at` (`created_at`),
  KEY `idx_order_status` (`order_status`),
  KEY `table_id` (`table_id`),
  KEY `taken_by_user_id` (`taken_by_user_id`),
  KEY `idx_orders_branch_status_date` (`branch_id`,`order_status`,`created_at`),
  KEY `idx_orders_session` (`dining_session_id`),
  KEY `idx_orders_branch_created` (`branch_id`,`created_at`),
  KEY `idx_orders_branch_status` (`branch_id`,`order_status`,`created_at`),
  KEY `idx_orders_pay_status` (`branch_id`,`payment_status`,`created_at`),
  KEY `idx_ord_branch_status` (`branch_id`,`order_status`),
  KEY `idx_ord_cust_pay` (`customer_id`,`payment_status`),
  KEY `idx_ord_session` (`dining_session_id`,`order_status`),
  KEY `idx_ord_created_status` (`created_at`,`order_status`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`dining_session_id`) REFERENCES `dining_sessions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`table_id`) REFERENCES `restaurant_tables` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`taken_by_user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for `orders`
INSERT INTO `orders` (`id`, `order_number`, `branch_id`, `dining_session_id`, `table_id`, `customer_id`, `taken_by_user_id`, `served_by_user_id`, `order_type`, `subtotal`, `discount`, `tax`, `service_charge`, `delivery_charge`, `total`, `payment_status`, `order_status`, `priority`, `notes`, `created_at`, `updated_at`, `completed_at`, `cancelled_at`) VALUES ('2', 'ORD-B1-20260909-0001', '1', NULL, NULL, NULL, '1', NULL, 'DINE_IN', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', 'UNPAID', 'DRAFT', 'NORMAL', 'Test multi-station order', '2026-09-09 05:40:44', '2026-09-09 05:40:44', NULL, NULL);
INSERT INTO `orders` (`id`, `order_number`, `branch_id`, `dining_session_id`, `table_id`, `customer_id`, `taken_by_user_id`, `served_by_user_id`, `order_type`, `subtotal`, `discount`, `tax`, `service_charge`, `delivery_charge`, `total`, `payment_status`, `order_status`, `priority`, `notes`, `created_at`, `updated_at`, `completed_at`, `cancelled_at`) VALUES ('3', 'ORD-B1-20260909-0002', '1', NULL, NULL, NULL, '1', NULL, 'DINE_IN', '24.00', '0.00', '1.20', '0.00', '0.00', '25.20', 'UNPAID', 'ROUTED', 'NORMAL', 'Test multi-station order', '2026-09-09 05:40:50', '2026-09-09 05:40:50', NULL, NULL);
INSERT INTO `orders` (`id`, `order_number`, `branch_id`, `dining_session_id`, `table_id`, `customer_id`, `taken_by_user_id`, `served_by_user_id`, `order_type`, `subtotal`, `discount`, `tax`, `service_charge`, `delivery_charge`, `total`, `payment_status`, `order_status`, `priority`, `notes`, `created_at`, `updated_at`, `completed_at`, `cancelled_at`) VALUES ('4', 'ORD-B1-20260909-0003', '1', NULL, NULL, NULL, '1', NULL, 'DINE_IN', '24.00', '0.00', '1.20', '0.00', '0.00', '25.20', 'UNPAID', 'ROUTED', 'NORMAL', 'Test multi-station order', '2026-09-09 05:41:01', '2026-09-09 05:41:01', NULL, NULL);
INSERT INTO `orders` (`id`, `order_number`, `branch_id`, `dining_session_id`, `table_id`, `customer_id`, `taken_by_user_id`, `served_by_user_id`, `order_type`, `subtotal`, `discount`, `tax`, `service_charge`, `delivery_charge`, `total`, `payment_status`, `order_status`, `priority`, `notes`, `created_at`, `updated_at`, `completed_at`, `cancelled_at`) VALUES ('5', 'ORD-B1-20260909-0004', '1', NULL, NULL, NULL, '1', NULL, 'DINE_IN', '24.00', '0.00', '1.20', '0.00', '0.00', '25.20', 'UNPAID', 'ROUTED', 'NORMAL', 'Test multi-station order', '2026-09-09 05:41:14', '2026-09-09 05:41:14', NULL, NULL);
INSERT INTO `orders` (`id`, `order_number`, `branch_id`, `dining_session_id`, `table_id`, `customer_id`, `taken_by_user_id`, `served_by_user_id`, `order_type`, `subtotal`, `discount`, `tax`, `service_charge`, `delivery_charge`, `total`, `payment_status`, `order_status`, `priority`, `notes`, `created_at`, `updated_at`, `completed_at`, `cancelled_at`) VALUES ('6', 'ORD-B1-20260909-0005', '1', NULL, NULL, NULL, '1', NULL, 'DINE_IN', '24.00', '0.00', '1.20', '0.00', '0.00', '25.20', 'UNPAID', 'ROUTED', 'NORMAL', 'Test multi-station order', '2026-09-09 05:41:27', '2026-09-09 05:41:27', NULL, NULL);
INSERT INTO `orders` (`id`, `order_number`, `branch_id`, `dining_session_id`, `table_id`, `customer_id`, `taken_by_user_id`, `served_by_user_id`, `order_type`, `subtotal`, `discount`, `tax`, `service_charge`, `delivery_charge`, `total`, `payment_status`, `order_status`, `priority`, `notes`, `created_at`, `updated_at`, `completed_at`, `cancelled_at`) VALUES ('7', 'ORD-B1-20260909-0006', '1', NULL, NULL, NULL, '1', NULL, 'DINE_IN', '24.00', '0.00', '1.20', '0.00', '0.00', '25.20', 'UNPAID', 'PREPARING', 'NORMAL', 'Test multi-station order', '2026-09-09 05:41:47', '2026-09-09 05:41:47', NULL, NULL);
INSERT INTO `orders` (`id`, `order_number`, `branch_id`, `dining_session_id`, `table_id`, `customer_id`, `taken_by_user_id`, `served_by_user_id`, `order_type`, `subtotal`, `discount`, `tax`, `service_charge`, `delivery_charge`, `total`, `payment_status`, `order_status`, `priority`, `notes`, `created_at`, `updated_at`, `completed_at`, `cancelled_at`) VALUES ('8', 'TEST-ORD-1788911115', '1', NULL, NULL, NULL, '1', NULL, 'DINE_IN', '500.00', '0.00', '25.00', '0.00', '0.00', '525.00', 'UNPAID', 'SERVED', 'NORMAL', NULL, '2026-09-09 05:45:15', '2026-09-09 05:45:15', NULL, NULL);
INSERT INTO `orders` (`id`, `order_number`, `branch_id`, `dining_session_id`, `table_id`, `customer_id`, `taken_by_user_id`, `served_by_user_id`, `order_type`, `subtotal`, `discount`, `tax`, `service_charge`, `delivery_charge`, `total`, `payment_status`, `order_status`, `priority`, `notes`, `created_at`, `updated_at`, `completed_at`, `cancelled_at`) VALUES ('9', 'TEST-ORD-1788911125', '1', NULL, NULL, NULL, '1', NULL, 'DINE_IN', '500.00', '0.00', '25.00', '0.00', '0.00', '525.00', 'UNPAID', 'SERVED', 'NORMAL', NULL, '2026-09-09 05:45:25', '2026-09-09 05:45:25', NULL, NULL);
INSERT INTO `orders` (`id`, `order_number`, `branch_id`, `dining_session_id`, `table_id`, `customer_id`, `taken_by_user_id`, `served_by_user_id`, `order_type`, `subtotal`, `discount`, `tax`, `service_charge`, `delivery_charge`, `total`, `payment_status`, `order_status`, `priority`, `notes`, `created_at`, `updated_at`, `completed_at`, `cancelled_at`) VALUES ('10', 'TEST-ORD-1788911142', '1', NULL, NULL, NULL, '1', NULL, 'DINE_IN', '500.00', '0.00', '25.00', '0.00', '0.00', '525.00', 'UNPAID', 'SERVED', 'NORMAL', NULL, '2026-09-09 05:45:42', '2026-09-09 05:45:42', NULL, NULL);
INSERT INTO `orders` (`id`, `order_number`, `branch_id`, `dining_session_id`, `table_id`, `customer_id`, `taken_by_user_id`, `served_by_user_id`, `order_type`, `subtotal`, `discount`, `tax`, `service_charge`, `delivery_charge`, `total`, `payment_status`, `order_status`, `priority`, `notes`, `created_at`, `updated_at`, `completed_at`, `cancelled_at`) VALUES ('11', 'TEST-ORD-1788911154', '1', NULL, NULL, NULL, '1', NULL, 'DINE_IN', '500.00', '0.00', '25.00', '0.00', '0.00', '525.00', 'UNPAID', 'SERVED', 'NORMAL', NULL, '2026-09-09 05:45:54', '2026-09-09 05:45:54', NULL, NULL);
INSERT INTO `orders` (`id`, `order_number`, `branch_id`, `dining_session_id`, `table_id`, `customer_id`, `taken_by_user_id`, `served_by_user_id`, `order_type`, `subtotal`, `discount`, `tax`, `service_charge`, `delivery_charge`, `total`, `payment_status`, `order_status`, `priority`, `notes`, `created_at`, `updated_at`, `completed_at`, `cancelled_at`) VALUES ('12', 'TEST-ORD-1788911188', '1', NULL, NULL, NULL, '1', NULL, 'DINE_IN', '500.00', '0.00', '25.00', '0.00', '0.00', '525.00', 'PAID', 'SERVED', 'NORMAL', NULL, '2026-09-09 05:46:28', '2026-09-09 05:46:28', NULL, NULL);
INSERT INTO `orders` (`id`, `order_number`, `branch_id`, `dining_session_id`, `table_id`, `customer_id`, `taken_by_user_id`, `served_by_user_id`, `order_type`, `subtotal`, `discount`, `tax`, `service_charge`, `delivery_charge`, `total`, `payment_status`, `order_status`, `priority`, `notes`, `created_at`, `updated_at`, `completed_at`, `cancelled_at`) VALUES ('13', 'TEST-ORD-1788911203', '1', NULL, NULL, NULL, '1', NULL, 'DINE_IN', '500.00', '0.00', '25.00', '0.00', '0.00', '525.00', 'PAID', 'SERVED', 'NORMAL', NULL, '2026-09-09 05:46:43', '2026-09-09 05:46:43', NULL, NULL);
INSERT INTO `orders` (`id`, `order_number`, `branch_id`, `dining_session_id`, `table_id`, `customer_id`, `taken_by_user_id`, `served_by_user_id`, `order_type`, `subtotal`, `discount`, `tax`, `service_charge`, `delivery_charge`, `total`, `payment_status`, `order_status`, `priority`, `notes`, `created_at`, `updated_at`, `completed_at`, `cancelled_at`) VALUES ('14', 'TEST-ORD-1788911215', '1', NULL, NULL, NULL, '1', NULL, 'DINE_IN', '500.00', '0.00', '25.00', '0.00', '0.00', '525.00', 'PAID', 'SERVED', 'NORMAL', NULL, '2026-09-09 05:46:55', '2026-09-09 05:46:55', NULL, NULL);
INSERT INTO `orders` (`id`, `order_number`, `branch_id`, `dining_session_id`, `table_id`, `customer_id`, `taken_by_user_id`, `served_by_user_id`, `order_type`, `subtotal`, `discount`, `tax`, `service_charge`, `delivery_charge`, `total`, `payment_status`, `order_status`, `priority`, `notes`, `created_at`, `updated_at`, `completed_at`, `cancelled_at`) VALUES ('15', 'TEST-ORD-1788911222', '1', NULL, NULL, NULL, '1', NULL, 'DINE_IN', '500.00', '0.00', '25.00', '0.00', '0.00', '525.00', 'PARTIAL', 'SERVED', 'NORMAL', NULL, '2026-09-09 05:47:02', '2026-09-09 05:47:02', NULL, NULL);
INSERT INTO `orders` (`id`, `order_number`, `branch_id`, `dining_session_id`, `table_id`, `customer_id`, `taken_by_user_id`, `served_by_user_id`, `order_type`, `subtotal`, `discount`, `tax`, `service_charge`, `delivery_charge`, `total`, `payment_status`, `order_status`, `priority`, `notes`, `created_at`, `updated_at`, `completed_at`, `cancelled_at`) VALUES ('16', 'WTR-ORD-1788911472', '1', NULL, '2', NULL, '1', NULL, 'DINE_IN', '1000.00', '0.00', '50.00', '0.00', '0.00', '1050.00', 'UNPAID', 'SERVED', 'NORMAL', NULL, '2026-09-09 05:51:12', '2026-09-09 05:51:12', NULL, NULL);
INSERT INTO `orders` (`id`, `order_number`, `branch_id`, `dining_session_id`, `table_id`, `customer_id`, `taken_by_user_id`, `served_by_user_id`, `order_type`, `subtotal`, `discount`, `tax`, `service_charge`, `delivery_charge`, `total`, `payment_status`, `order_status`, `priority`, `notes`, `created_at`, `updated_at`, `completed_at`, `cancelled_at`) VALUES ('17', 'WTR-ORD-1788911481', '1', NULL, '2', NULL, '1', NULL, 'DINE_IN', '1000.00', '0.00', '50.00', '0.00', '0.00', '1050.00', 'PARTIAL', 'SERVED', 'NORMAL', NULL, '2026-09-09 05:51:21', '2026-09-09 05:51:21', NULL, NULL);
INSERT INTO `orders` (`id`, `order_number`, `branch_id`, `dining_session_id`, `table_id`, `customer_id`, `taken_by_user_id`, `served_by_user_id`, `order_type`, `subtotal`, `discount`, `tax`, `service_charge`, `delivery_charge`, `total`, `payment_status`, `order_status`, `priority`, `notes`, `created_at`, `updated_at`, `completed_at`, `cancelled_at`) VALUES ('18', 'WTR-ORD-1788911491', '1', NULL, '2', NULL, '1', NULL, 'DINE_IN', '1000.00', '0.00', '50.00', '0.00', '0.00', '1050.00', 'PARTIAL', 'SERVED', 'NORMAL', NULL, '2026-09-09 05:51:31', '2026-09-09 05:51:31', NULL, NULL);
INSERT INTO `orders` (`id`, `order_number`, `branch_id`, `dining_session_id`, `table_id`, `customer_id`, `taken_by_user_id`, `served_by_user_id`, `order_type`, `subtotal`, `discount`, `tax`, `service_charge`, `delivery_charge`, `total`, `payment_status`, `order_status`, `priority`, `notes`, `created_at`, `updated_at`, `completed_at`, `cancelled_at`) VALUES ('19', 'WTR-ORD-1788911500', '1', NULL, '2', NULL, '1', NULL, 'DINE_IN', '1000.00', '0.00', '50.00', '0.00', '0.00', '1050.00', 'PARTIAL', 'SERVED', 'NORMAL', NULL, '2026-09-09 05:51:40', '2026-09-09 05:51:40', NULL, NULL);
INSERT INTO `orders` (`id`, `order_number`, `branch_id`, `dining_session_id`, `table_id`, `customer_id`, `taken_by_user_id`, `served_by_user_id`, `order_type`, `subtotal`, `discount`, `tax`, `service_charge`, `delivery_charge`, `total`, `payment_status`, `order_status`, `priority`, `notes`, `created_at`, `updated_at`, `completed_at`, `cancelled_at`) VALUES ('20', 'WTR-ORD-1788911508', '1', NULL, '2', NULL, '1', NULL, 'DINE_IN', '1000.00', '0.00', '50.00', '0.00', '0.00', '1050.00', 'PARTIAL', 'SERVED', 'NORMAL', NULL, '2026-09-09 05:51:48', '2026-09-09 05:51:48', NULL, NULL);
INSERT INTO `orders` (`id`, `order_number`, `branch_id`, `dining_session_id`, `table_id`, `customer_id`, `taken_by_user_id`, `served_by_user_id`, `order_type`, `subtotal`, `discount`, `tax`, `service_charge`, `delivery_charge`, `total`, `payment_status`, `order_status`, `priority`, `notes`, `created_at`, `updated_at`, `completed_at`, `cancelled_at`) VALUES ('22', 'TEST-ORD-KDS-1788911718', '1', NULL, '2', NULL, '1', NULL, 'DINE_IN', '1000.00', '0.00', '50.00', '0.00', '0.00', '1050.00', 'UNPAID', 'SUBMITTED', 'HIGH', NULL, '2026-09-09 05:55:18', '2026-09-09 05:55:18', NULL, NULL);
INSERT INTO `orders` (`id`, `order_number`, `branch_id`, `dining_session_id`, `table_id`, `customer_id`, `taken_by_user_id`, `served_by_user_id`, `order_type`, `subtotal`, `discount`, `tax`, `service_charge`, `delivery_charge`, `total`, `payment_status`, `order_status`, `priority`, `notes`, `created_at`, `updated_at`, `completed_at`, `cancelled_at`) VALUES ('23', 'TEST-ORD-KDS-1788911725', '1', NULL, '2', NULL, '1', NULL, 'DINE_IN', '1000.00', '0.00', '50.00', '0.00', '0.00', '1050.00', 'UNPAID', 'SUBMITTED', 'HIGH', NULL, '2026-09-09 05:55:25', '2026-09-09 05:55:25', NULL, NULL);
INSERT INTO `orders` (`id`, `order_number`, `branch_id`, `dining_session_id`, `table_id`, `customer_id`, `taken_by_user_id`, `served_by_user_id`, `order_type`, `subtotal`, `discount`, `tax`, `service_charge`, `delivery_charge`, `total`, `payment_status`, `order_status`, `priority`, `notes`, `created_at`, `updated_at`, `completed_at`, `cancelled_at`) VALUES ('24', 'TEST-ORD-KDS-1788911733', '1', NULL, '2', NULL, '1', NULL, 'DINE_IN', '1000.00', '0.00', '50.00', '0.00', '0.00', '1050.00', 'UNPAID', 'PREPARING', 'HIGH', NULL, '2026-09-09 05:55:33', '2026-09-09 05:55:33', NULL, NULL);
INSERT INTO `orders` (`id`, `order_number`, `branch_id`, `dining_session_id`, `table_id`, `customer_id`, `taken_by_user_id`, `served_by_user_id`, `order_type`, `subtotal`, `discount`, `tax`, `service_charge`, `delivery_charge`, `total`, `payment_status`, `order_status`, `priority`, `notes`, `created_at`, `updated_at`, `completed_at`, `cancelled_at`) VALUES ('25', 'TEST-ORD-KDS-1788911745', '1', NULL, '2', NULL, '1', NULL, 'DINE_IN', '1000.00', '0.00', '50.00', '0.00', '0.00', '1050.00', 'UNPAID', 'PREPARING', 'HIGH', NULL, '2026-09-09 05:55:45', '2026-09-09 05:55:46', NULL, NULL);
INSERT INTO `orders` (`id`, `order_number`, `branch_id`, `dining_session_id`, `table_id`, `customer_id`, `taken_by_user_id`, `served_by_user_id`, `order_type`, `subtotal`, `discount`, `tax`, `service_charge`, `delivery_charge`, `total`, `payment_status`, `order_status`, `priority`, `notes`, `created_at`, `updated_at`, `completed_at`, `cancelled_at`) VALUES ('26', 'TEST-ORD-KDS-1788911760', '1', NULL, '2', NULL, '1', NULL, 'DINE_IN', '1000.00', '0.00', '50.00', '0.00', '0.00', '1050.00', 'UNPAID', 'PREPARING', 'HIGH', NULL, '2026-09-09 05:56:00', '2026-09-09 05:56:00', NULL, NULL);
INSERT INTO `orders` (`id`, `order_number`, `branch_id`, `dining_session_id`, `table_id`, `customer_id`, `taken_by_user_id`, `served_by_user_id`, `order_type`, `subtotal`, `discount`, `tax`, `service_charge`, `delivery_charge`, `total`, `payment_status`, `order_status`, `priority`, `notes`, `created_at`, `updated_at`, `completed_at`, `cancelled_at`) VALUES ('27', 'TEST-ORD-KDS-1788911767', '1', NULL, '2', NULL, '1', NULL, 'DINE_IN', '1000.00', '0.00', '50.00', '0.00', '0.00', '1050.00', 'UNPAID', 'READY', 'HIGH', NULL, '2026-09-09 05:56:07', '2026-09-09 05:56:07', NULL, NULL);
INSERT INTO `orders` (`id`, `order_number`, `branch_id`, `dining_session_id`, `table_id`, `customer_id`, `taken_by_user_id`, `served_by_user_id`, `order_type`, `subtotal`, `discount`, `tax`, `service_charge`, `delivery_charge`, `total`, `payment_status`, `order_status`, `priority`, `notes`, `created_at`, `updated_at`, `completed_at`, `cancelled_at`) VALUES ('28', 'ORD-TEST-INV-666', '1', NULL, NULL, NULL, '1', NULL, 'DINE_IN', '0.00', '0.00', '0.00', '0.00', '0.00', '12.00', 'UNPAID', 'PREPARING', 'NORMAL', NULL, '2026-09-09 06:05:06', '2026-09-09 06:05:06', NULL, NULL);
INSERT INTO `orders` (`id`, `order_number`, `branch_id`, `dining_session_id`, `table_id`, `customer_id`, `taken_by_user_id`, `served_by_user_id`, `order_type`, `subtotal`, `discount`, `tax`, `service_charge`, `delivery_charge`, `total`, `payment_status`, `order_status`, `priority`, `notes`, `created_at`, `updated_at`, `completed_at`, `cancelled_at`) VALUES ('29', 'ORD-TEST-INV-885', '1', NULL, NULL, NULL, '1', NULL, 'DINE_IN', '0.00', '0.00', '0.00', '0.00', '0.00', '12.00', 'UNPAID', 'PREPARING', 'NORMAL', NULL, '2026-09-09 06:05:15', '2026-09-09 06:05:15', NULL, NULL);
INSERT INTO `orders` (`id`, `order_number`, `branch_id`, `dining_session_id`, `table_id`, `customer_id`, `taken_by_user_id`, `served_by_user_id`, `order_type`, `subtotal`, `discount`, `tax`, `service_charge`, `delivery_charge`, `total`, `payment_status`, `order_status`, `priority`, `notes`, `created_at`, `updated_at`, `completed_at`, `cancelled_at`) VALUES ('30', 'ORD-TEST-INV-343', '1', NULL, NULL, NULL, '1', NULL, 'DINE_IN', '0.00', '0.00', '0.00', '0.00', '0.00', '12.00', 'UNPAID', 'PREPARING', 'NORMAL', NULL, '2026-09-09 06:05:23', '2026-09-09 06:05:23', NULL, NULL);
INSERT INTO `orders` (`id`, `order_number`, `branch_id`, `dining_session_id`, `table_id`, `customer_id`, `taken_by_user_id`, `served_by_user_id`, `order_type`, `subtotal`, `discount`, `tax`, `service_charge`, `delivery_charge`, `total`, `payment_status`, `order_status`, `priority`, `notes`, `created_at`, `updated_at`, `completed_at`, `cancelled_at`) VALUES ('31', 'ORD-TEST-INV-334', '1', NULL, NULL, NULL, '1', NULL, 'DINE_IN', '0.00', '0.00', '0.00', '0.00', '0.00', '12.00', 'UNPAID', 'PREPARING', 'NORMAL', NULL, '2026-09-09 06:05:31', '2026-09-09 06:05:31', NULL, NULL);
INSERT INTO `orders` (`id`, `order_number`, `branch_id`, `dining_session_id`, `table_id`, `customer_id`, `taken_by_user_id`, `served_by_user_id`, `order_type`, `subtotal`, `discount`, `tax`, `service_charge`, `delivery_charge`, `total`, `payment_status`, `order_status`, `priority`, `notes`, `created_at`, `updated_at`, `completed_at`, `cancelled_at`) VALUES ('32', 'ORD-TEST-INV-764', '1', NULL, NULL, NULL, '1', NULL, 'DINE_IN', '0.00', '0.00', '0.00', '0.00', '0.00', '12.00', 'UNPAID', 'PREPARING', 'NORMAL', NULL, '2026-09-09 06:05:43', '2026-09-09 06:05:43', NULL, NULL);
INSERT INTO `orders` (`id`, `order_number`, `branch_id`, `dining_session_id`, `table_id`, `customer_id`, `taken_by_user_id`, `served_by_user_id`, `order_type`, `subtotal`, `discount`, `tax`, `service_charge`, `delivery_charge`, `total`, `payment_status`, `order_status`, `priority`, `notes`, `created_at`, `updated_at`, `completed_at`, `cancelled_at`) VALUES ('33', 'ORD-TEST-INV-464', '1', NULL, NULL, NULL, '1', NULL, 'DINE_IN', '0.00', '0.00', '0.00', '0.00', '0.00', '12.00', 'UNPAID', 'PREPARING', 'NORMAL', NULL, '2026-09-09 06:06:06', '2026-09-09 06:06:06', NULL, NULL);
INSERT INTO `orders` (`id`, `order_number`, `branch_id`, `dining_session_id`, `table_id`, `customer_id`, `taken_by_user_id`, `served_by_user_id`, `order_type`, `subtotal`, `discount`, `tax`, `service_charge`, `delivery_charge`, `total`, `payment_status`, `order_status`, `priority`, `notes`, `created_at`, `updated_at`, `completed_at`, `cancelled_at`) VALUES ('34', 'ORD-TEST-INV-208', '1', NULL, NULL, NULL, '1', NULL, 'DINE_IN', '0.00', '0.00', '0.00', '0.00', '0.00', '12.00', 'UNPAID', 'PREPARING', 'NORMAL', NULL, '2026-09-09 06:06:20', '2026-09-09 06:06:20', NULL, NULL);
INSERT INTO `orders` (`id`, `order_number`, `branch_id`, `dining_session_id`, `table_id`, `customer_id`, `taken_by_user_id`, `served_by_user_id`, `order_type`, `subtotal`, `discount`, `tax`, `service_charge`, `delivery_charge`, `total`, `payment_status`, `order_status`, `priority`, `notes`, `created_at`, `updated_at`, `completed_at`, `cancelled_at`) VALUES ('35', 'ORD-TEST-INV-681', '1', NULL, NULL, NULL, '1', NULL, 'DINE_IN', '0.00', '0.00', '0.00', '0.00', '0.00', '12.00', 'UNPAID', 'PREPARING', 'NORMAL', NULL, '2026-09-09 06:06:27', '2026-09-09 06:06:27', NULL, NULL);
INSERT INTO `orders` (`id`, `order_number`, `branch_id`, `dining_session_id`, `table_id`, `customer_id`, `taken_by_user_id`, `served_by_user_id`, `order_type`, `subtotal`, `discount`, `tax`, `service_charge`, `delivery_charge`, `total`, `payment_status`, `order_status`, `priority`, `notes`, `created_at`, `updated_at`, `completed_at`, `cancelled_at`) VALUES ('36', 'ORD-B1-20260909-0007', '1', NULL, NULL, '4', '1', NULL, 'TAKEAWAY', '24.00', '0.00', '1.20', '0.00', '0.00', '25.20', 'UNPAID', 'COMPLETED', 'NORMAL', NULL, '2026-09-09 06:52:51', '2026-09-09 06:52:51', '2026-09-09 00:52:51', NULL);
INSERT INTO `orders` (`id`, `order_number`, `branch_id`, `dining_session_id`, `table_id`, `customer_id`, `taken_by_user_id`, `served_by_user_id`, `order_type`, `subtotal`, `discount`, `tax`, `service_charge`, `delivery_charge`, `total`, `payment_status`, `order_status`, `priority`, `notes`, `created_at`, `updated_at`, `completed_at`, `cancelled_at`) VALUES ('37', 'ORD-B1-20260909-0008', '1', NULL, NULL, '5', '1', NULL, 'TAKEAWAY', '24.00', '0.00', '1.20', '0.00', '0.00', '25.20', 'UNPAID', 'COMPLETED', 'NORMAL', NULL, '2026-09-09 06:53:01', '2026-09-09 06:53:01', '2026-09-09 00:53:01', NULL);
INSERT INTO `orders` (`id`, `order_number`, `branch_id`, `dining_session_id`, `table_id`, `customer_id`, `taken_by_user_id`, `served_by_user_id`, `order_type`, `subtotal`, `discount`, `tax`, `service_charge`, `delivery_charge`, `total`, `payment_status`, `order_status`, `priority`, `notes`, `created_at`, `updated_at`, `completed_at`, `cancelled_at`) VALUES ('38', 'ORD-B1-20260909-0009', '1', NULL, NULL, '6', '1', NULL, 'TAKEAWAY', '24.00', '0.00', '1.20', '0.00', '0.00', '25.20', 'UNPAID', 'COMPLETED', 'NORMAL', NULL, '2026-09-09 06:53:19', '2026-09-09 06:53:19', '2026-09-09 00:53:19', NULL);
INSERT INTO `orders` (`id`, `order_number`, `branch_id`, `dining_session_id`, `table_id`, `customer_id`, `taken_by_user_id`, `served_by_user_id`, `order_type`, `subtotal`, `discount`, `tax`, `service_charge`, `delivery_charge`, `total`, `payment_status`, `order_status`, `priority`, `notes`, `created_at`, `updated_at`, `completed_at`, `cancelled_at`) VALUES ('39', 'ORD-B1-20260909-0010', '1', NULL, NULL, '7', '1', NULL, 'TAKEAWAY', '24.00', '0.00', '1.20', '0.00', '0.00', '25.20', 'UNPAID', 'COMPLETED', 'NORMAL', NULL, '2026-09-09 06:53:45', '2026-09-09 06:53:45', '2026-09-09 00:53:45', NULL);
INSERT INTO `orders` (`id`, `order_number`, `branch_id`, `dining_session_id`, `table_id`, `customer_id`, `taken_by_user_id`, `served_by_user_id`, `order_type`, `subtotal`, `discount`, `tax`, `service_charge`, `delivery_charge`, `total`, `payment_status`, `order_status`, `priority`, `notes`, `created_at`, `updated_at`, `completed_at`, `cancelled_at`) VALUES ('40', 'ORD-B1-20260909-0011', '1', '1', '2', '8', '1', NULL, 'DINE_IN', '12.00', '0.00', '0.60', '0.00', '0.00', '12.60', 'UNPAID', 'SUBMITTED', 'NORMAL', 'Automated QR test order', '2026-09-09 06:53:45', '2026-09-09 06:53:45', NULL, NULL);
INSERT INTO `orders` (`id`, `order_number`, `branch_id`, `dining_session_id`, `table_id`, `customer_id`, `taken_by_user_id`, `served_by_user_id`, `order_type`, `subtotal`, `discount`, `tax`, `service_charge`, `delivery_charge`, `total`, `payment_status`, `order_status`, `priority`, `notes`, `created_at`, `updated_at`, `completed_at`, `cancelled_at`) VALUES ('41', 'ORD-B1-20260909-0012', '1', NULL, NULL, '9', '1', NULL, 'TAKEAWAY', '24.00', '0.00', '1.20', '0.00', '0.00', '25.20', 'UNPAID', 'COMPLETED', 'NORMAL', NULL, '2026-09-09 06:53:54', '2026-09-09 06:53:54', '2026-09-09 00:53:54', NULL);
INSERT INTO `orders` (`id`, `order_number`, `branch_id`, `dining_session_id`, `table_id`, `customer_id`, `taken_by_user_id`, `served_by_user_id`, `order_type`, `subtotal`, `discount`, `tax`, `service_charge`, `delivery_charge`, `total`, `payment_status`, `order_status`, `priority`, `notes`, `created_at`, `updated_at`, `completed_at`, `cancelled_at`) VALUES ('42', 'ORD-B1-20260909-0013', '1', '1', '2', '10', '1', NULL, 'DINE_IN', '12.00', '0.00', '0.60', '0.00', '0.00', '12.60', 'UNPAID', 'ROUTED', 'NORMAL', 'Automated QR test order', '2026-09-09 06:53:54', '2026-09-09 06:53:54', NULL, NULL);
INSERT INTO `orders` (`id`, `order_number`, `branch_id`, `dining_session_id`, `table_id`, `customer_id`, `taken_by_user_id`, `served_by_user_id`, `order_type`, `subtotal`, `discount`, `tax`, `service_charge`, `delivery_charge`, `total`, `payment_status`, `order_status`, `priority`, `notes`, `created_at`, `updated_at`, `completed_at`, `cancelled_at`) VALUES ('43', 'ORD-B1-20260909-0014', '1', NULL, NULL, '11', '1', NULL, 'TAKEAWAY', '24.00', '0.00', '1.20', '0.00', '0.00', '25.20', 'UNPAID', 'COMPLETED', 'NORMAL', NULL, '2026-09-09 06:54:03', '2026-09-09 06:54:03', '2026-09-09 00:54:03', NULL);
INSERT INTO `orders` (`id`, `order_number`, `branch_id`, `dining_session_id`, `table_id`, `customer_id`, `taken_by_user_id`, `served_by_user_id`, `order_type`, `subtotal`, `discount`, `tax`, `service_charge`, `delivery_charge`, `total`, `payment_status`, `order_status`, `priority`, `notes`, `created_at`, `updated_at`, `completed_at`, `cancelled_at`) VALUES ('44', 'ORD-B1-20260909-0015', '1', '1', '2', '12', '1', NULL, 'DINE_IN', '12.00', '0.00', '0.60', '0.00', '0.00', '12.60', 'UNPAID', 'ROUTED', 'NORMAL', 'Automated QR test order', '2026-09-09 06:54:03', '2026-09-09 06:54:03', NULL, NULL);

-- Table structure for `payment_allocations`
DROP TABLE IF EXISTS `payment_allocations`;
CREATE TABLE `payment_allocations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `payment_id` int(10) unsigned NOT NULL,
  `order_id` int(10) unsigned NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `payment_id` (`payment_id`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `payment_allocations_ibfk_1` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payment_allocations_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `payment_allocations`
INSERT INTO `payment_allocations` (`id`, `payment_id`, `order_id`, `amount`, `created_at`) VALUES ('2', '2', '12', '200.00', '2026-09-09 05:46:28');
INSERT INTO `payment_allocations` (`id`, `payment_id`, `order_id`, `amount`, `created_at`) VALUES ('3', '3', '12', '325.00', '2026-09-09 05:46:28');
INSERT INTO `payment_allocations` (`id`, `payment_id`, `order_id`, `amount`, `created_at`) VALUES ('4', '4', '13', '200.00', '2026-09-09 05:46:43');
INSERT INTO `payment_allocations` (`id`, `payment_id`, `order_id`, `amount`, `created_at`) VALUES ('5', '5', '13', '325.00', '2026-09-09 05:46:43');
INSERT INTO `payment_allocations` (`id`, `payment_id`, `order_id`, `amount`, `created_at`) VALUES ('6', '6', '14', '200.00', '2026-09-09 05:46:55');
INSERT INTO `payment_allocations` (`id`, `payment_id`, `order_id`, `amount`, `created_at`) VALUES ('7', '7', '14', '325.00', '2026-09-09 05:46:55');
INSERT INTO `payment_allocations` (`id`, `payment_id`, `order_id`, `amount`, `created_at`) VALUES ('8', '8', '15', '200.00', '2026-09-09 05:47:02');
INSERT INTO `payment_allocations` (`id`, `payment_id`, `order_id`, `amount`, `created_at`) VALUES ('9', '9', '15', '325.00', '2026-09-09 05:47:02');
INSERT INTO `payment_allocations` (`id`, `payment_id`, `order_id`, `amount`, `created_at`) VALUES ('10', '10', '17', '1050.00', '2026-09-09 05:51:21');
INSERT INTO `payment_allocations` (`id`, `payment_id`, `order_id`, `amount`, `created_at`) VALUES ('11', '11', '18', '1050.00', '2026-09-09 05:51:31');
INSERT INTO `payment_allocations` (`id`, `payment_id`, `order_id`, `amount`, `created_at`) VALUES ('12', '12', '19', '1050.00', '2026-09-09 05:51:40');
INSERT INTO `payment_allocations` (`id`, `payment_id`, `order_id`, `amount`, `created_at`) VALUES ('13', '13', '20', '1050.00', '2026-09-09 05:51:48');

-- Table structure for `payment_methods`
DROP TABLE IF EXISTS `payment_methods`;
CREATE TABLE `payment_methods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `code` varchar(30) NOT NULL,
  `type` enum('CASH','CARD','MOBILE_FINANCIAL_SERVICE','OTHER') DEFAULT 'CASH',
  `requires_reference` tinyint(1) DEFAULT 0,
  `requires_transaction_id` tinyint(1) DEFAULT 0,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for `payment_methods`
INSERT INTO `payment_methods` (`id`, `name`, `code`, `type`, `requires_reference`, `requires_transaction_id`, `display_order`, `created_at`, `updated_at`, `is_active`) VALUES ('1', 'Cash', 'CASH', 'CASH', '0', '0', '1', '2026-09-09 05:45:33', '2026-09-09 05:45:33', '1');
INSERT INTO `payment_methods` (`id`, `name`, `code`, `type`, `requires_reference`, `requires_transaction_id`, `display_order`, `created_at`, `updated_at`, `is_active`) VALUES ('2', 'Debit/Credit Card', 'CARD', 'CARD', '1', '1', '2', '2026-09-09 05:45:33', '2026-09-09 05:45:33', '1');
INSERT INTO `payment_methods` (`id`, `name`, `code`, `type`, `requires_reference`, `requires_transaction_id`, `display_order`, `created_at`, `updated_at`, `is_active`) VALUES ('3', 'bKash Mobile Money', 'BKASH', 'MOBILE_FINANCIAL_SERVICE', '1', '1', '3', '2026-09-09 05:45:33', '2026-09-09 05:45:33', '1');
INSERT INTO `payment_methods` (`id`, `name`, `code`, `type`, `requires_reference`, `requires_transaction_id`, `display_order`, `created_at`, `updated_at`, `is_active`) VALUES ('4', 'Nagad Mobile Money', 'NAGAD', 'MOBILE_FINANCIAL_SERVICE', '1', '1', '4', '2026-09-09 05:45:33', '2026-09-09 05:45:33', '1');
INSERT INTO `payment_methods` (`id`, `name`, `code`, `type`, `requires_reference`, `requires_transaction_id`, `display_order`, `created_at`, `updated_at`, `is_active`) VALUES ('5', 'Rocket Mobile Money', 'ROCKET', 'MOBILE_FINANCIAL_SERVICE', '1', '1', '5', '2026-09-09 05:45:33', '2026-09-09 05:45:33', '1');
INSERT INTO `payment_methods` (`id`, `name`, `code`, `type`, `requires_reference`, `requires_transaction_id`, `display_order`, `created_at`, `updated_at`, `is_active`) VALUES ('6', 'Other Payment', 'OTHER', 'OTHER', '0', '0', '6', '2026-09-09 05:45:33', '2026-09-09 05:45:33', '1');

-- Table structure for `payments`
DROP TABLE IF EXISTS `payments`;
CREATE TABLE `payments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `payment_number` varchar(40) DEFAULT NULL,
  `branch_id` int(10) unsigned NOT NULL DEFAULT 1,
  `order_id` int(10) unsigned NOT NULL,
  `dining_session_id` int(10) unsigned DEFAULT NULL,
  `payment_method_id` int(10) unsigned NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `currency` varchar(10) DEFAULT 'BDT',
  `transaction_reference` varchar(100) DEFAULT NULL,
  `received_by_user_id` int(10) unsigned NOT NULL,
  `status` enum('PENDING','COMPLETED','FAILED','REFUNDED') DEFAULT 'COMPLETED',
  `idempotency_key` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_payment_number` (`payment_number`),
  KEY `idx_payments_order_id` (`order_id`),
  KEY `idx_payments_created_at` (`created_at`),
  KEY `received_by_user_id` (`received_by_user_id`),
  KEY `idx_payments_branch_created` (`branch_id`,`created_at`),
  KEY `idx_payments_method` (`branch_id`,`payment_method_id`,`created_at`),
  KEY `idx_pay_ord_status` (`order_id`,`status`),
  KEY `idx_pay_method_date` (`payment_method_id`,`created_at`),
  CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods` (`id`),
  CONSTRAINT `payments_ibfk_3` FOREIGN KEY (`received_by_user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for `payments`
INSERT INTO `payments` (`id`, `payment_number`, `branch_id`, `order_id`, `dining_session_id`, `payment_method_id`, `amount`, `currency`, `transaction_reference`, `received_by_user_id`, `status`, `idempotency_key`, `notes`, `created_at`, `updated_at`) VALUES ('2', 'PM-20260908-903342', '1', '12', NULL, '1', '200.00', 'BDT', NULL, '1', 'COMPLETED', 'IDEM-TEST-1788911188.2506', 'Partial cash deposit', '2026-09-09 05:46:28', '2026-09-09 05:46:28');
INSERT INTO `payments` (`id`, `payment_number`, `branch_id`, `order_id`, `dining_session_id`, `payment_method_id`, `amount`, `currency`, `transaction_reference`, `received_by_user_id`, `status`, `idempotency_key`, `notes`, `created_at`, `updated_at`) VALUES ('3', 'PM-20260908-650638', '1', '12', NULL, '3', '325.00', 'BDT', 'BKASH-TX-998877', '1', 'COMPLETED', 'IDEM-TEST-1788911188.2572', 'Settlement payment via bKash', '2026-09-09 05:46:28', '2026-09-09 05:46:28');
INSERT INTO `payments` (`id`, `payment_number`, `branch_id`, `order_id`, `dining_session_id`, `payment_method_id`, `amount`, `currency`, `transaction_reference`, `received_by_user_id`, `status`, `idempotency_key`, `notes`, `created_at`, `updated_at`) VALUES ('4', 'PM-20260908-892350', '1', '13', NULL, '1', '200.00', 'BDT', NULL, '1', 'COMPLETED', 'IDEM-TEST-1788911203.4283', 'Partial cash deposit', '2026-09-09 05:46:43', '2026-09-09 05:46:43');
INSERT INTO `payments` (`id`, `payment_number`, `branch_id`, `order_id`, `dining_session_id`, `payment_method_id`, `amount`, `currency`, `transaction_reference`, `received_by_user_id`, `status`, `idempotency_key`, `notes`, `created_at`, `updated_at`) VALUES ('5', 'PM-20260908-572424', '1', '13', NULL, '3', '325.00', 'BDT', 'BKASH-TX-998877', '1', 'COMPLETED', 'IDEM-TEST-1788911203.4349', 'Settlement payment via bKash', '2026-09-09 05:46:43', '2026-09-09 05:46:43');
INSERT INTO `payments` (`id`, `payment_number`, `branch_id`, `order_id`, `dining_session_id`, `payment_method_id`, `amount`, `currency`, `transaction_reference`, `received_by_user_id`, `status`, `idempotency_key`, `notes`, `created_at`, `updated_at`) VALUES ('6', 'PM-20260908-441507', '1', '14', NULL, '1', '200.00', 'BDT', NULL, '1', 'COMPLETED', 'IDEM-TEST-1788911215.9774', 'Partial cash deposit', '2026-09-09 05:46:55', '2026-09-09 05:46:55');
INSERT INTO `payments` (`id`, `payment_number`, `branch_id`, `order_id`, `dining_session_id`, `payment_method_id`, `amount`, `currency`, `transaction_reference`, `received_by_user_id`, `status`, `idempotency_key`, `notes`, `created_at`, `updated_at`) VALUES ('7', 'PM-20260908-466040', '1', '14', NULL, '3', '325.00', 'BDT', 'BKASH-TX-998877', '1', 'COMPLETED', 'IDEM-TEST-1788911215.9838', 'Settlement payment via bKash', '2026-09-09 05:46:55', '2026-09-09 05:46:55');
INSERT INTO `payments` (`id`, `payment_number`, `branch_id`, `order_id`, `dining_session_id`, `payment_method_id`, `amount`, `currency`, `transaction_reference`, `received_by_user_id`, `status`, `idempotency_key`, `notes`, `created_at`, `updated_at`) VALUES ('8', 'PM-20260908-460001', '1', '15', NULL, '1', '200.00', 'BDT', NULL, '1', 'COMPLETED', 'IDEM-TEST-1788911222.9352', 'Partial cash deposit', '2026-09-09 05:47:02', '2026-09-09 05:47:02');
INSERT INTO `payments` (`id`, `payment_number`, `branch_id`, `order_id`, `dining_session_id`, `payment_method_id`, `amount`, `currency`, `transaction_reference`, `received_by_user_id`, `status`, `idempotency_key`, `notes`, `created_at`, `updated_at`) VALUES ('9', 'PM-20260908-872017', '1', '15', NULL, '3', '325.00', 'BDT', 'BKASH-TX-998877', '1', 'COMPLETED', 'IDEM-TEST-1788911222.9414', 'Settlement payment via bKash', '2026-09-09 05:47:02', '2026-09-09 05:47:02');
INSERT INTO `payments` (`id`, `payment_number`, `branch_id`, `order_id`, `dining_session_id`, `payment_method_id`, `amount`, `currency`, `transaction_reference`, `received_by_user_id`, `status`, `idempotency_key`, `notes`, `created_at`, `updated_at`) VALUES ('10', 'PM-20260908-124583', '1', '17', NULL, '1', '1050.00', 'BDT', NULL, '1', 'COMPLETED', 'IDEM-WTR-1788911481.9198', NULL, '2026-09-09 05:51:21', '2026-09-09 05:51:21');
INSERT INTO `payments` (`id`, `payment_number`, `branch_id`, `order_id`, `dining_session_id`, `payment_method_id`, `amount`, `currency`, `transaction_reference`, `received_by_user_id`, `status`, `idempotency_key`, `notes`, `created_at`, `updated_at`) VALUES ('11', 'PM-20260908-292064', '1', '18', NULL, '1', '1050.00', 'BDT', NULL, '1', 'COMPLETED', 'IDEM-WTR-1788911491.4335', NULL, '2026-09-09 05:51:31', '2026-09-09 05:51:31');
INSERT INTO `payments` (`id`, `payment_number`, `branch_id`, `order_id`, `dining_session_id`, `payment_method_id`, `amount`, `currency`, `transaction_reference`, `received_by_user_id`, `status`, `idempotency_key`, `notes`, `created_at`, `updated_at`) VALUES ('12', 'PM-20260908-742737', '1', '19', NULL, '1', '1050.00', 'BDT', NULL, '1', 'COMPLETED', 'IDEM-WTR-1788911500.3661', NULL, '2026-09-09 05:51:40', '2026-09-09 05:51:40');
INSERT INTO `payments` (`id`, `payment_number`, `branch_id`, `order_id`, `dining_session_id`, `payment_method_id`, `amount`, `currency`, `transaction_reference`, `received_by_user_id`, `status`, `idempotency_key`, `notes`, `created_at`, `updated_at`) VALUES ('13', 'PM-20260908-863420', '1', '20', NULL, '1', '1050.00', 'BDT', NULL, '1', 'COMPLETED', 'IDEM-WTR-1788911508.7872', NULL, '2026-09-09 05:51:48', '2026-09-09 05:51:48');

-- Table structure for `permissions`
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL,
  `module` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for `product_modifiers`
DROP TABLE IF EXISTS `product_modifiers`;
CREATE TABLE `product_modifiers` (
  `product_id` int(10) unsigned NOT NULL,
  `modifier_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`product_id`,`modifier_id`),
  KEY `modifier_id` (`modifier_id`),
  CONSTRAINT `product_modifiers_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_modifiers_ibfk_2` FOREIGN KEY (`modifier_id`) REFERENCES `modifiers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for `product_variants`
DROP TABLE IF EXISTS `product_variants`;
CREATE TABLE `product_variants` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL,
  `station_id` int(10) unsigned DEFAULT NULL,
  `variant_name` varchar(80) NOT NULL,
  `sku` varchar(50) DEFAULT NULL,
  `price` decimal(12,2) DEFAULT NULL,
  `price_adjustment` decimal(12,2) DEFAULT 0.00,
  `is_default` tinyint(1) DEFAULT 0,
  `status` enum('ACTIVE','INACTIVE') DEFAULT 'ACTIVE',
  `display_order` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `product_variants_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for `products`
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(10) unsigned NOT NULL,
  `default_station_id` int(10) unsigned NOT NULL,
  `name` varchar(120) NOT NULL,
  `slug` varchar(120) DEFAULT NULL,
  `sku` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `short_description` varchar(255) DEFAULT NULL,
  `price` decimal(12,2) NOT NULL,
  `cost_price` decimal(12,2) DEFAULT 0.00,
  `status` enum('ACTIVE','INACTIVE') DEFAULT 'ACTIVE',
  `is_available` tinyint(1) DEFAULT 1,
  `display_order` int(11) DEFAULT 0,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sku` (`sku`),
  KEY `default_station_id` (`default_station_id`),
  KEY `idx_products_cat_status` (`category_id`,`status`,`is_available`),
  KEY `idx_products_sku` (`sku`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  CONSTRAINT `products_ibfk_2` FOREIGN KEY (`default_station_id`) REFERENCES `stations` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for `products`
INSERT INTO `products` (`id`, `category_id`, `default_station_id`, `name`, `slug`, `sku`, `description`, `short_description`, `price`, `cost_price`, `status`, `is_available`, `display_order`, `image_url`, `created_at`, `updated_at`, `deleted_at`) VALUES ('4', '1', '1', 'Chef Special Burger', NULL, 'TST-BGR', NULL, NULL, '12.00', '0.00', 'ACTIVE', '1', '0', NULL, '2026-09-09 05:40:50', '2026-09-09 05:40:50', NULL);
INSERT INTO `products` (`id`, `category_id`, `default_station_id`, `name`, `slug`, `sku`, `description`, `short_description`, `price`, `cost_price`, `status`, `is_available`, `display_order`, `image_url`, `created_at`, `updated_at`, `deleted_at`) VALUES ('5', '1', '2', 'Iced Americano', NULL, 'TST-COF', NULL, NULL, '4.50', '0.00', 'ACTIVE', '1', '0', NULL, '2026-09-09 05:40:50', '2026-09-09 05:40:50', NULL);
INSERT INTO `products` (`id`, `category_id`, `default_station_id`, `name`, `slug`, `sku`, `description`, `short_description`, `price`, `cost_price`, `status`, `is_available`, `display_order`, `image_url`, `created_at`, `updated_at`, `deleted_at`) VALUES ('6', '1', '4', 'Molten Chocolate Cake', NULL, 'TST-CAK', NULL, NULL, '7.50', '0.00', 'ACTIVE', '1', '0', NULL, '2026-09-09 05:40:50', '2026-09-09 05:40:50', NULL);

-- Table structure for `promotions`
DROP TABLE IF EXISTS `promotions`;
CREATE TABLE `promotions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `discount_type` enum('PERCENTAGE','FIXED') DEFAULT 'PERCENTAGE',
  `discount_value` decimal(12,2) NOT NULL DEFAULT 0.00,
  `min_order_amount` decimal(12,2) DEFAULT 0.00,
  `max_discount` decimal(12,2) DEFAULT 0.00,
  `start_at` datetime DEFAULT NULL,
  `end_at` datetime DEFAULT NULL,
  `status` enum('ACTIVE','INACTIVE','EXPIRED') DEFAULT 'ACTIVE',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_promo_branch` (`branch_id`),
  KEY `idx_promo_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for `purchase_order_items`
DROP TABLE IF EXISTS `purchase_order_items`;
CREATE TABLE `purchase_order_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `purchase_order_id` int(10) unsigned NOT NULL,
  `ingredient_id` int(10) unsigned NOT NULL,
  `ordered_quantity` decimal(12,4) NOT NULL,
  `received_quantity` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `unit` varchar(20) NOT NULL,
  `unit_price` decimal(12,2) NOT NULL,
  `line_total` decimal(12,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_order_id` (`purchase_order_id`),
  KEY `ingredient_id` (`ingredient_id`),
  CONSTRAINT `purchase_order_items_ibfk_1` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchase_order_items_ibfk_2` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `purchase_order_items`
INSERT INTO `purchase_order_items` (`id`, `purchase_order_id`, `ingredient_id`, `ordered_quantity`, `received_quantity`, `unit`, `unit_price`, `line_total`) VALUES ('1', '2', '4', '50.0000', '0.0000', 'kg', '110.00', '5500.00');
INSERT INTO `purchase_order_items` (`id`, `purchase_order_id`, `ingredient_id`, `ordered_quantity`, `received_quantity`, `unit`, `unit_price`, `line_total`) VALUES ('2', '3', '5', '50.0000', '0.0000', 'kg', '110.00', '5500.00');
INSERT INTO `purchase_order_items` (`id`, `purchase_order_id`, `ingredient_id`, `ordered_quantity`, `received_quantity`, `unit`, `unit_price`, `line_total`) VALUES ('3', '4', '6', '50.0000', '50.0000', 'kg', '110.00', '5500.00');
INSERT INTO `purchase_order_items` (`id`, `purchase_order_id`, `ingredient_id`, `ordered_quantity`, `received_quantity`, `unit`, `unit_price`, `line_total`) VALUES ('4', '5', '7', '50.0000', '50.0000', 'kg', '110.00', '5500.00');
INSERT INTO `purchase_order_items` (`id`, `purchase_order_id`, `ingredient_id`, `ordered_quantity`, `received_quantity`, `unit`, `unit_price`, `line_total`) VALUES ('5', '6', '8', '50.0000', '50.0000', 'kg', '110.00', '5500.00');
INSERT INTO `purchase_order_items` (`id`, `purchase_order_id`, `ingredient_id`, `ordered_quantity`, `received_quantity`, `unit`, `unit_price`, `line_total`) VALUES ('6', '7', '9', '50.0000', '50.0000', 'kg', '110.00', '5500.00');
INSERT INTO `purchase_order_items` (`id`, `purchase_order_id`, `ingredient_id`, `ordered_quantity`, `received_quantity`, `unit`, `unit_price`, `line_total`) VALUES ('7', '8', '10', '50.0000', '50.0000', 'kg', '110.00', '5500.00');
INSERT INTO `purchase_order_items` (`id`, `purchase_order_id`, `ingredient_id`, `ordered_quantity`, `received_quantity`, `unit`, `unit_price`, `line_total`) VALUES ('8', '9', '11', '50.0000', '50.0000', 'kg', '110.00', '5500.00');
INSERT INTO `purchase_order_items` (`id`, `purchase_order_id`, `ingredient_id`, `ordered_quantity`, `received_quantity`, `unit`, `unit_price`, `line_total`) VALUES ('9', '10', '12', '50.0000', '50.0000', 'kg', '110.00', '5500.00');
INSERT INTO `purchase_order_items` (`id`, `purchase_order_id`, `ingredient_id`, `ordered_quantity`, `received_quantity`, `unit`, `unit_price`, `line_total`) VALUES ('10', '11', '13', '50.0000', '50.0000', 'kg', '110.00', '5500.00');
INSERT INTO `purchase_order_items` (`id`, `purchase_order_id`, `ingredient_id`, `ordered_quantity`, `received_quantity`, `unit`, `unit_price`, `line_total`) VALUES ('11', '12', '14', '50.0000', '50.0000', 'kg', '110.00', '5500.00');
INSERT INTO `purchase_order_items` (`id`, `purchase_order_id`, `ingredient_id`, `ordered_quantity`, `received_quantity`, `unit`, `unit_price`, `line_total`) VALUES ('12', '13', '15', '50.0000', '50.0000', 'kg', '110.00', '5500.00');
INSERT INTO `purchase_order_items` (`id`, `purchase_order_id`, `ingredient_id`, `ordered_quantity`, `received_quantity`, `unit`, `unit_price`, `line_total`) VALUES ('13', '14', '16', '50.0000', '50.0000', 'kg', '110.00', '5500.00');
INSERT INTO `purchase_order_items` (`id`, `purchase_order_id`, `ingredient_id`, `ordered_quantity`, `received_quantity`, `unit`, `unit_price`, `line_total`) VALUES ('14', '16', '18', '50.0000', '50.0000', 'kg', '110.00', '5500.00');
INSERT INTO `purchase_order_items` (`id`, `purchase_order_id`, `ingredient_id`, `ordered_quantity`, `received_quantity`, `unit`, `unit_price`, `line_total`) VALUES ('15', '17', '19', '50.0000', '50.0000', 'kg', '110.00', '5500.00');
INSERT INTO `purchase_order_items` (`id`, `purchase_order_id`, `ingredient_id`, `ordered_quantity`, `received_quantity`, `unit`, `unit_price`, `line_total`) VALUES ('16', '18', '20', '50.0000', '50.0000', 'kg', '110.00', '5500.00');

-- Table structure for `purchase_orders`
DROP TABLE IF EXISTS `purchase_orders`;
CREATE TABLE `purchase_orders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` int(10) unsigned NOT NULL DEFAULT 1,
  `po_number` varchar(50) NOT NULL,
  `supplier_id` int(10) unsigned NOT NULL,
  `status` enum('DRAFT','SUBMITTED','APPROVED','PARTIALLY_RECEIVED','RECEIVED','CLOSED','CANCELLED') DEFAULT 'DRAFT',
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00,
  `tax` decimal(12,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `grand_total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_by` int(10) unsigned DEFAULT NULL,
  `approved_by` int(10) unsigned DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `po_number` (`po_number`),
  KEY `supplier_id` (`supplier_id`),
  CONSTRAINT `purchase_orders_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `purchase_orders`
INSERT INTO `purchase_orders` (`id`, `branch_id`, `po_number`, `supplier_id`, `status`, `subtotal`, `tax`, `discount`, `grand_total`, `created_by`, `approved_by`, `notes`, `created_at`, `updated_at`) VALUES ('2', '1', 'PO-20260909-5881', '1', 'DRAFT', '5500.00', '0.00', '0.00', '5500.00', '1', NULL, 'Bulk rice purchase for weekend rush', '2026-09-09 06:03:28', '2026-09-09 06:03:28');
INSERT INTO `purchase_orders` (`id`, `branch_id`, `po_number`, `supplier_id`, `status`, `subtotal`, `tax`, `discount`, `grand_total`, `created_by`, `approved_by`, `notes`, `created_at`, `updated_at`) VALUES ('3', '1', 'PO-20260909-1731', '3', 'APPROVED', '5500.00', '0.00', '0.00', '5500.00', '1', NULL, 'Bulk rice purchase for weekend rush', '2026-09-09 06:03:50', '2026-09-09 06:03:50');
INSERT INTO `purchase_orders` (`id`, `branch_id`, `po_number`, `supplier_id`, `status`, `subtotal`, `tax`, `discount`, `grand_total`, `created_by`, `approved_by`, `notes`, `created_at`, `updated_at`) VALUES ('4', '1', 'PO-20260909-5931', '4', 'RECEIVED', '5500.00', '0.00', '0.00', '5500.00', '1', NULL, 'Bulk rice purchase for weekend rush', '2026-09-09 06:04:00', '2026-09-09 06:04:00');
INSERT INTO `purchase_orders` (`id`, `branch_id`, `po_number`, `supplier_id`, `status`, `subtotal`, `tax`, `discount`, `grand_total`, `created_by`, `approved_by`, `notes`, `created_at`, `updated_at`) VALUES ('5', '1', 'PO-20260909-1769', '5', 'RECEIVED', '5500.00', '0.00', '0.00', '5500.00', '1', NULL, 'Bulk rice purchase for weekend rush', '2026-09-09 06:04:08', '2026-09-09 06:04:08');
INSERT INTO `purchase_orders` (`id`, `branch_id`, `po_number`, `supplier_id`, `status`, `subtotal`, `tax`, `discount`, `grand_total`, `created_by`, `approved_by`, `notes`, `created_at`, `updated_at`) VALUES ('6', '1', 'PO-20260909-8246', '6', 'RECEIVED', '5500.00', '0.00', '0.00', '5500.00', '1', NULL, 'Bulk rice purchase for weekend rush', '2026-09-09 06:04:26', '2026-09-09 06:04:26');
INSERT INTO `purchase_orders` (`id`, `branch_id`, `po_number`, `supplier_id`, `status`, `subtotal`, `tax`, `discount`, `grand_total`, `created_by`, `approved_by`, `notes`, `created_at`, `updated_at`) VALUES ('7', '1', 'PO-20260909-8070', '7', 'RECEIVED', '5500.00', '0.00', '0.00', '5500.00', '1', NULL, 'Bulk rice purchase for weekend rush', '2026-09-09 06:04:35', '2026-09-09 06:04:35');
INSERT INTO `purchase_orders` (`id`, `branch_id`, `po_number`, `supplier_id`, `status`, `subtotal`, `tax`, `discount`, `grand_total`, `created_by`, `approved_by`, `notes`, `created_at`, `updated_at`) VALUES ('8', '1', 'PO-20260909-7475', '8', 'RECEIVED', '5500.00', '0.00', '0.00', '5500.00', '1', NULL, 'Bulk rice purchase for weekend rush', '2026-09-09 06:04:43', '2026-09-09 06:04:43');
INSERT INTO `purchase_orders` (`id`, `branch_id`, `po_number`, `supplier_id`, `status`, `subtotal`, `tax`, `discount`, `grand_total`, `created_by`, `approved_by`, `notes`, `created_at`, `updated_at`) VALUES ('9', '1', 'PO-20260909-2668', '9', 'RECEIVED', '5500.00', '0.00', '0.00', '5500.00', '1', NULL, 'Bulk rice purchase for weekend rush', '2026-09-09 06:04:55', '2026-09-09 06:04:55');
INSERT INTO `purchase_orders` (`id`, `branch_id`, `po_number`, `supplier_id`, `status`, `subtotal`, `tax`, `discount`, `grand_total`, `created_by`, `approved_by`, `notes`, `created_at`, `updated_at`) VALUES ('10', '1', 'PO-20260909-8780', '10', 'RECEIVED', '5500.00', '0.00', '0.00', '5500.00', '1', NULL, 'Bulk rice purchase for weekend rush', '2026-09-09 06:05:06', '2026-09-09 06:05:06');
INSERT INTO `purchase_orders` (`id`, `branch_id`, `po_number`, `supplier_id`, `status`, `subtotal`, `tax`, `discount`, `grand_total`, `created_by`, `approved_by`, `notes`, `created_at`, `updated_at`) VALUES ('11', '1', 'PO-20260909-9955', '11', 'RECEIVED', '5500.00', '0.00', '0.00', '5500.00', '1', NULL, 'Bulk rice purchase for weekend rush', '2026-09-09 06:05:15', '2026-09-09 06:05:15');
INSERT INTO `purchase_orders` (`id`, `branch_id`, `po_number`, `supplier_id`, `status`, `subtotal`, `tax`, `discount`, `grand_total`, `created_by`, `approved_by`, `notes`, `created_at`, `updated_at`) VALUES ('12', '1', 'PO-20260909-8592', '12', 'RECEIVED', '5500.00', '0.00', '0.00', '5500.00', '1', NULL, 'Bulk rice purchase for weekend rush', '2026-09-09 06:05:23', '2026-09-09 06:05:23');
INSERT INTO `purchase_orders` (`id`, `branch_id`, `po_number`, `supplier_id`, `status`, `subtotal`, `tax`, `discount`, `grand_total`, `created_by`, `approved_by`, `notes`, `created_at`, `updated_at`) VALUES ('13', '1', 'PO-20260909-3565', '13', 'RECEIVED', '5500.00', '0.00', '0.00', '5500.00', '1', NULL, 'Bulk rice purchase for weekend rush', '2026-09-09 06:05:31', '2026-09-09 06:05:31');
INSERT INTO `purchase_orders` (`id`, `branch_id`, `po_number`, `supplier_id`, `status`, `subtotal`, `tax`, `discount`, `grand_total`, `created_by`, `approved_by`, `notes`, `created_at`, `updated_at`) VALUES ('14', '1', 'PO-20260909-6227', '14', 'RECEIVED', '5500.00', '0.00', '0.00', '5500.00', '1', NULL, 'Bulk rice purchase for weekend rush', '2026-09-09 06:05:43', '2026-09-09 06:05:43');
INSERT INTO `purchase_orders` (`id`, `branch_id`, `po_number`, `supplier_id`, `status`, `subtotal`, `tax`, `discount`, `grand_total`, `created_by`, `approved_by`, `notes`, `created_at`, `updated_at`) VALUES ('16', '1', 'PO-20260909-5611', '16', 'RECEIVED', '5500.00', '0.00', '0.00', '5500.00', '1', NULL, 'Bulk rice purchase for weekend rush', '2026-09-09 06:06:06', '2026-09-09 06:06:06');
INSERT INTO `purchase_orders` (`id`, `branch_id`, `po_number`, `supplier_id`, `status`, `subtotal`, `tax`, `discount`, `grand_total`, `created_by`, `approved_by`, `notes`, `created_at`, `updated_at`) VALUES ('17', '1', 'PO-20260909-6324', '17', 'RECEIVED', '5500.00', '0.00', '0.00', '5500.00', '1', NULL, 'Bulk rice purchase for weekend rush', '2026-09-09 06:06:20', '2026-09-09 06:06:20');
INSERT INTO `purchase_orders` (`id`, `branch_id`, `po_number`, `supplier_id`, `status`, `subtotal`, `tax`, `discount`, `grand_total`, `created_by`, `approved_by`, `notes`, `created_at`, `updated_at`) VALUES ('18', '1', 'PO-20260909-7205', '18', 'RECEIVED', '5500.00', '0.00', '0.00', '5500.00', '1', NULL, 'Bulk rice purchase for weekend rush', '2026-09-09 06:06:26', '2026-09-09 06:06:26');

-- Table structure for `qr_table_tokens`
DROP TABLE IF EXISTS `qr_table_tokens`;
CREATE TABLE `qr_table_tokens` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` int(10) unsigned NOT NULL,
  `table_id` int(10) unsigned NOT NULL,
  `token` varchar(64) NOT NULL,
  `status` enum('ACTIVE','REVOKED') DEFAULT 'ACTIVE',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_qr_token` (`token`),
  UNIQUE KEY `idx_qr_branch_table` (`branch_id`,`table_id`),
  KEY `fk_qr_table` (`table_id`),
  KEY `idx_qr_token_status` (`token`,`status`),
  CONSTRAINT `fk_qr_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_qr_table` FOREIGN KEY (`table_id`) REFERENCES `restaurant_tables` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `qr_table_tokens`
INSERT INTO `qr_table_tokens` (`id`, `branch_id`, `table_id`, `token`, `status`, `created_at`, `updated_at`) VALUES ('1', '1', '2', 'b83779d3990fa76fd80a58d150f2fc03dd95442203c41e5c4c108a609406aa93', 'ACTIVE', '2026-09-09 06:53:01', NULL);

-- Table structure for `receipts`
DROP TABLE IF EXISTS `receipts`;
CREATE TABLE `receipts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `receipt_number` varchar(40) NOT NULL,
  `branch_id` int(10) unsigned NOT NULL DEFAULT 1,
  `order_id` int(10) unsigned NOT NULL,
  `payment_id` int(10) unsigned NOT NULL,
  `amount_paid` decimal(12,2) NOT NULL DEFAULT 0.00,
  `receipt_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `receipt_number` (`receipt_number`),
  KEY `order_id` (`order_id`),
  KEY `payment_id` (`payment_id`),
  CONSTRAINT `receipts_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `receipts_ibfk_2` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `receipts`
INSERT INTO `receipts` (`id`, `receipt_number`, `branch_id`, `order_id`, `payment_id`, `amount_paid`, `receipt_data`, `created_by`, `created_at`) VALUES ('2', 'RCP-2026-934969', '1', '12', '2', '200.00', '{\"receipt_number\":\"RCP-2026-934969\",\"payment_number\":\"PM-20260908-903342\",\"order_number\":\"TEST-ORD-1788911188\",\"branch_name\":\"Main Outlet\",\"table_number\":\"N\\/A\",\"waiter_name\":\"System Administrator\",\"customer_name\":\"Guest\",\"items\":[{\"name\":\"Test Item 100\",\"quantity\":2,\"unit_price\":\"250.00\",\"subtotal\":\"500.00\"}],\"financials\":{\"subtotal\":500,\"discount\":0,\"tax\":25,\"service_charge\":0,\"grand_total\":525,\"amount_paid_this_txn\":200,\"total_net_paid\":200,\"outstanding_balance\":325,\"payment_method\":\"Cash\",\"transaction_reference\":null},\"timestamp\":\"2026-09-08 23:46:28\"}', '1', '2026-09-09 05:46:28');
INSERT INTO `receipts` (`id`, `receipt_number`, `branch_id`, `order_id`, `payment_id`, `amount_paid`, `receipt_data`, `created_by`, `created_at`) VALUES ('3', 'RCP-2026-260788', '1', '12', '3', '325.00', '{\"receipt_number\":\"RCP-2026-260788\",\"payment_number\":\"PM-20260908-650638\",\"order_number\":\"TEST-ORD-1788911188\",\"branch_name\":\"Main Outlet\",\"table_number\":\"N\\/A\",\"waiter_name\":\"System Administrator\",\"customer_name\":\"Guest\",\"items\":[{\"name\":\"Test Item 100\",\"quantity\":2,\"unit_price\":\"250.00\",\"subtotal\":\"500.00\"}],\"financials\":{\"subtotal\":500,\"discount\":0,\"tax\":25,\"service_charge\":0,\"grand_total\":525,\"amount_paid_this_txn\":325,\"total_net_paid\":525,\"outstanding_balance\":0,\"payment_method\":\"bKash Mobile Money\",\"transaction_reference\":\"BKASH-TX-998877\"},\"timestamp\":\"2026-09-08 23:46:28\"}', '1', '2026-09-09 05:46:28');
INSERT INTO `receipts` (`id`, `receipt_number`, `branch_id`, `order_id`, `payment_id`, `amount_paid`, `receipt_data`, `created_by`, `created_at`) VALUES ('4', 'RCP-2026-999663', '1', '13', '4', '200.00', '{\"receipt_number\":\"RCP-2026-999663\",\"payment_number\":\"PM-20260908-892350\",\"order_number\":\"TEST-ORD-1788911203\",\"branch_name\":\"Main Outlet\",\"table_number\":\"N\\/A\",\"waiter_name\":\"System Administrator\",\"customer_name\":\"Guest\",\"items\":[{\"name\":\"Test Item 100\",\"quantity\":2,\"unit_price\":\"250.00\",\"subtotal\":\"500.00\"}],\"financials\":{\"subtotal\":500,\"discount\":0,\"tax\":25,\"service_charge\":0,\"grand_total\":525,\"amount_paid_this_txn\":200,\"total_net_paid\":200,\"outstanding_balance\":325,\"payment_method\":\"Cash\",\"transaction_reference\":null},\"timestamp\":\"2026-09-08 23:46:43\"}', '1', '2026-09-09 05:46:43');
INSERT INTO `receipts` (`id`, `receipt_number`, `branch_id`, `order_id`, `payment_id`, `amount_paid`, `receipt_data`, `created_by`, `created_at`) VALUES ('5', 'RCP-2026-978447', '1', '13', '5', '325.00', '{\"receipt_number\":\"RCP-2026-978447\",\"payment_number\":\"PM-20260908-572424\",\"order_number\":\"TEST-ORD-1788911203\",\"branch_name\":\"Main Outlet\",\"table_number\":\"N\\/A\",\"waiter_name\":\"System Administrator\",\"customer_name\":\"Guest\",\"items\":[{\"name\":\"Test Item 100\",\"quantity\":2,\"unit_price\":\"250.00\",\"subtotal\":\"500.00\"}],\"financials\":{\"subtotal\":500,\"discount\":0,\"tax\":25,\"service_charge\":0,\"grand_total\":525,\"amount_paid_this_txn\":325,\"total_net_paid\":525,\"outstanding_balance\":0,\"payment_method\":\"bKash Mobile Money\",\"transaction_reference\":\"BKASH-TX-998877\"},\"timestamp\":\"2026-09-08 23:46:43\"}', '1', '2026-09-09 05:46:43');
INSERT INTO `receipts` (`id`, `receipt_number`, `branch_id`, `order_id`, `payment_id`, `amount_paid`, `receipt_data`, `created_by`, `created_at`) VALUES ('6', 'RCP-2026-888224', '1', '14', '6', '200.00', '{\"receipt_number\":\"RCP-2026-888224\",\"payment_number\":\"PM-20260908-441507\",\"order_number\":\"TEST-ORD-1788911215\",\"branch_name\":\"Main Outlet\",\"table_number\":\"N\\/A\",\"waiter_name\":\"System Administrator\",\"customer_name\":\"Guest\",\"items\":[{\"name\":\"Test Item 100\",\"quantity\":2,\"unit_price\":\"250.00\",\"subtotal\":\"500.00\"}],\"financials\":{\"subtotal\":500,\"discount\":0,\"tax\":25,\"service_charge\":0,\"grand_total\":525,\"amount_paid_this_txn\":200,\"total_net_paid\":200,\"outstanding_balance\":325,\"payment_method\":\"Cash\",\"transaction_reference\":null},\"timestamp\":\"2026-09-08 23:46:55\"}', '1', '2026-09-09 05:46:55');
INSERT INTO `receipts` (`id`, `receipt_number`, `branch_id`, `order_id`, `payment_id`, `amount_paid`, `receipt_data`, `created_by`, `created_at`) VALUES ('7', 'RCP-2026-885811', '1', '14', '7', '325.00', '{\"receipt_number\":\"RCP-2026-885811\",\"payment_number\":\"PM-20260908-466040\",\"order_number\":\"TEST-ORD-1788911215\",\"branch_name\":\"Main Outlet\",\"table_number\":\"N\\/A\",\"waiter_name\":\"System Administrator\",\"customer_name\":\"Guest\",\"items\":[{\"name\":\"Test Item 100\",\"quantity\":2,\"unit_price\":\"250.00\",\"subtotal\":\"500.00\"}],\"financials\":{\"subtotal\":500,\"discount\":0,\"tax\":25,\"service_charge\":0,\"grand_total\":525,\"amount_paid_this_txn\":325,\"total_net_paid\":525,\"outstanding_balance\":0,\"payment_method\":\"bKash Mobile Money\",\"transaction_reference\":\"BKASH-TX-998877\"},\"timestamp\":\"2026-09-08 23:46:55\"}', '1', '2026-09-09 05:46:55');
INSERT INTO `receipts` (`id`, `receipt_number`, `branch_id`, `order_id`, `payment_id`, `amount_paid`, `receipt_data`, `created_by`, `created_at`) VALUES ('8', 'RCP-2026-386863', '1', '15', '8', '200.00', '{\"receipt_number\":\"RCP-2026-386863\",\"payment_number\":\"PM-20260908-460001\",\"order_number\":\"TEST-ORD-1788911222\",\"branch_name\":\"Main Outlet\",\"table_number\":\"N\\/A\",\"waiter_name\":\"System Administrator\",\"customer_name\":\"Guest\",\"items\":[{\"name\":\"Test Item 100\",\"quantity\":2,\"unit_price\":\"250.00\",\"subtotal\":\"500.00\"}],\"financials\":{\"subtotal\":500,\"discount\":0,\"tax\":25,\"service_charge\":0,\"grand_total\":525,\"amount_paid_this_txn\":200,\"total_net_paid\":200,\"outstanding_balance\":325,\"payment_method\":\"Cash\",\"transaction_reference\":null},\"timestamp\":\"2026-09-08 23:47:02\"}', '1', '2026-09-09 05:47:02');
INSERT INTO `receipts` (`id`, `receipt_number`, `branch_id`, `order_id`, `payment_id`, `amount_paid`, `receipt_data`, `created_by`, `created_at`) VALUES ('9', 'RCP-2026-265603', '1', '15', '9', '325.00', '{\"receipt_number\":\"RCP-2026-265603\",\"payment_number\":\"PM-20260908-872017\",\"order_number\":\"TEST-ORD-1788911222\",\"branch_name\":\"Main Outlet\",\"table_number\":\"N\\/A\",\"waiter_name\":\"System Administrator\",\"customer_name\":\"Guest\",\"items\":[{\"name\":\"Test Item 100\",\"quantity\":2,\"unit_price\":\"250.00\",\"subtotal\":\"500.00\"}],\"financials\":{\"subtotal\":500,\"discount\":0,\"tax\":25,\"service_charge\":0,\"grand_total\":525,\"amount_paid_this_txn\":325,\"total_net_paid\":525,\"outstanding_balance\":0,\"payment_method\":\"bKash Mobile Money\",\"transaction_reference\":\"BKASH-TX-998877\"},\"timestamp\":\"2026-09-08 23:47:02\"}', '1', '2026-09-09 05:47:02');
INSERT INTO `receipts` (`id`, `receipt_number`, `branch_id`, `order_id`, `payment_id`, `amount_paid`, `receipt_data`, `created_by`, `created_at`) VALUES ('10', 'RCP-2026-965722', '1', '17', '10', '1050.00', '{\"receipt_number\":\"RCP-2026-965722\",\"payment_number\":\"PM-20260908-124583\",\"order_number\":\"WTR-ORD-1788911481\",\"branch_name\":\"Main Outlet\",\"table_number\":\"T-100\",\"waiter_name\":\"System Administrator\",\"customer_name\":\"Guest\",\"items\":[{\"name\":\"Mutton Kacchi Feast\",\"quantity\":2,\"unit_price\":\"500.00\",\"subtotal\":\"1000.00\"}],\"financials\":{\"subtotal\":1000,\"discount\":0,\"tax\":50,\"service_charge\":0,\"grand_total\":1050,\"amount_paid_this_txn\":1050,\"total_net_paid\":1050,\"outstanding_balance\":0,\"payment_method\":\"Cash\",\"transaction_reference\":null},\"timestamp\":\"2026-09-08 23:51:21\"}', '1', '2026-09-09 05:51:21');
INSERT INTO `receipts` (`id`, `receipt_number`, `branch_id`, `order_id`, `payment_id`, `amount_paid`, `receipt_data`, `created_by`, `created_at`) VALUES ('11', 'RCP-2026-570190', '1', '18', '11', '1050.00', '{\"receipt_number\":\"RCP-2026-570190\",\"payment_number\":\"PM-20260908-292064\",\"order_number\":\"WTR-ORD-1788911491\",\"branch_name\":\"Main Outlet\",\"table_number\":\"T-100\",\"waiter_name\":\"System Administrator\",\"customer_name\":\"Guest\",\"items\":[{\"name\":\"Mutton Kacchi Feast\",\"quantity\":2,\"unit_price\":\"500.00\",\"subtotal\":\"1000.00\"}],\"financials\":{\"subtotal\":1000,\"discount\":0,\"tax\":50,\"service_charge\":0,\"grand_total\":1050,\"amount_paid_this_txn\":1050,\"total_net_paid\":1050,\"outstanding_balance\":0,\"payment_method\":\"Cash\",\"transaction_reference\":null},\"timestamp\":\"2026-09-08 23:51:31\"}', '1', '2026-09-09 05:51:31');
INSERT INTO `receipts` (`id`, `receipt_number`, `branch_id`, `order_id`, `payment_id`, `amount_paid`, `receipt_data`, `created_by`, `created_at`) VALUES ('12', 'RCP-2026-935994', '1', '19', '12', '1050.00', '{\"receipt_number\":\"RCP-2026-935994\",\"payment_number\":\"PM-20260908-742737\",\"order_number\":\"WTR-ORD-1788911500\",\"branch_name\":\"Main Outlet\",\"table_number\":\"T-100\",\"waiter_name\":\"System Administrator\",\"customer_name\":\"Guest\",\"items\":[{\"name\":\"Mutton Kacchi Feast\",\"quantity\":2,\"unit_price\":\"500.00\",\"subtotal\":\"1000.00\"}],\"financials\":{\"subtotal\":1000,\"discount\":0,\"tax\":50,\"service_charge\":0,\"grand_total\":1050,\"amount_paid_this_txn\":1050,\"total_net_paid\":1050,\"outstanding_balance\":0,\"payment_method\":\"Cash\",\"transaction_reference\":null},\"timestamp\":\"2026-09-08 23:51:40\"}', '1', '2026-09-09 05:51:40');
INSERT INTO `receipts` (`id`, `receipt_number`, `branch_id`, `order_id`, `payment_id`, `amount_paid`, `receipt_data`, `created_by`, `created_at`) VALUES ('13', 'RCP-2026-387798', '1', '20', '13', '1050.00', '{\"receipt_number\":\"RCP-2026-387798\",\"payment_number\":\"PM-20260908-863420\",\"order_number\":\"WTR-ORD-1788911508\",\"branch_name\":\"Main Outlet\",\"table_number\":\"T-100\",\"waiter_name\":\"System Administrator\",\"customer_name\":\"Guest\",\"items\":[{\"name\":\"Mutton Kacchi Feast\",\"quantity\":2,\"unit_price\":\"500.00\",\"subtotal\":\"1000.00\"}],\"financials\":{\"subtotal\":1000,\"discount\":0,\"tax\":50,\"service_charge\":0,\"grand_total\":1050,\"amount_paid_this_txn\":1050,\"total_net_paid\":1050,\"outstanding_balance\":0,\"payment_method\":\"Cash\",\"transaction_reference\":null},\"timestamp\":\"2026-09-08 23:51:48\"}', '1', '2026-09-09 05:51:48');

-- Table structure for `recipe_items`
DROP TABLE IF EXISTS `recipe_items`;
CREATE TABLE `recipe_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `recipe_id` int(10) unsigned NOT NULL,
  `ingredient_id` int(10) unsigned NOT NULL,
  `quantity` decimal(12,4) NOT NULL,
  `unit` varchar(20) NOT NULL,
  `wastage_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `unit_cost` decimal(12,2) NOT NULL DEFAULT 0.00,
  `line_cost` decimal(12,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `recipe_id` (`recipe_id`),
  KEY `ingredient_id` (`ingredient_id`),
  CONSTRAINT `recipe_items_ibfk_1` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `recipe_items_ibfk_2` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `recipe_items`
INSERT INTO `recipe_items` (`id`, `recipe_id`, `ingredient_id`, `quantity`, `unit`, `wastage_percentage`, `unit_cost`, `line_cost`) VALUES ('1', '1', '10', '0.3000', 'g', '0.00', '112.86', '33.86');
INSERT INTO `recipe_items` (`id`, `recipe_id`, `ingredient_id`, `quantity`, `unit`, `wastage_percentage`, `unit_cost`, `line_cost`) VALUES ('2', '2', '11', '0.3000', 'g', '0.00', '112.86', '33.86');
INSERT INTO `recipe_items` (`id`, `recipe_id`, `ingredient_id`, `quantity`, `unit`, `wastage_percentage`, `unit_cost`, `line_cost`) VALUES ('3', '3', '12', '0.3000', 'g', '0.00', '112.86', '33.86');
INSERT INTO `recipe_items` (`id`, `recipe_id`, `ingredient_id`, `quantity`, `unit`, `wastage_percentage`, `unit_cost`, `line_cost`) VALUES ('4', '4', '13', '0.3000', 'g', '0.00', '112.86', '33.86');
INSERT INTO `recipe_items` (`id`, `recipe_id`, `ingredient_id`, `quantity`, `unit`, `wastage_percentage`, `unit_cost`, `line_cost`) VALUES ('5', '5', '14', '0.3000', 'g', '0.00', '112.86', '33.86');
INSERT INTO `recipe_items` (`id`, `recipe_id`, `ingredient_id`, `quantity`, `unit`, `wastage_percentage`, `unit_cost`, `line_cost`) VALUES ('6', '6', '15', '0.3000', 'g', '0.00', '112.86', '33.86');
INSERT INTO `recipe_items` (`id`, `recipe_id`, `ingredient_id`, `quantity`, `unit`, `wastage_percentage`, `unit_cost`, `line_cost`) VALUES ('7', '7', '16', '0.3000', 'g', '0.00', '112.86', '33.86');
INSERT INTO `recipe_items` (`id`, `recipe_id`, `ingredient_id`, `quantity`, `unit`, `wastage_percentage`, `unit_cost`, `line_cost`) VALUES ('8', '8', '18', '0.3000', 'g', '0.00', '112.86', '33.86');
INSERT INTO `recipe_items` (`id`, `recipe_id`, `ingredient_id`, `quantity`, `unit`, `wastage_percentage`, `unit_cost`, `line_cost`) VALUES ('9', '9', '19', '0.3000', 'g', '0.00', '112.86', '33.86');
INSERT INTO `recipe_items` (`id`, `recipe_id`, `ingredient_id`, `quantity`, `unit`, `wastage_percentage`, `unit_cost`, `line_cost`) VALUES ('10', '10', '20', '0.3000', 'g', '0.00', '112.86', '33.86');

-- Table structure for `recipes`
DROP TABLE IF EXISTS `recipes`;
CREATE TABLE `recipes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` int(10) unsigned NOT NULL DEFAULT 1,
  `product_id` int(10) unsigned NOT NULL,
  `variant_id` int(10) unsigned DEFAULT NULL,
  `version` int(10) unsigned NOT NULL DEFAULT 1,
  `name` varchar(150) DEFAULT NULL,
  `yield_quantity` decimal(12,2) NOT NULL DEFAULT 1.00,
  `yield_unit` varchar(20) NOT NULL DEFAULT 'portion',
  `total_cost` decimal(12,2) NOT NULL DEFAULT 0.00,
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` int(10) unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ingredient_id` int(10) unsigned NOT NULL,
  `quantity_required` decimal(12,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `ingredient_id` (`ingredient_id`),
  CONSTRAINT `recipes_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `recipes_ibfk_2` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for `recipes`
INSERT INTO `recipes` (`id`, `branch_id`, `product_id`, `variant_id`, `version`, `name`, `yield_quantity`, `yield_unit`, `total_cost`, `is_active`, `created_by`, `created_at`, `updated_at`, `ingredient_id`, `quantity_required`) VALUES ('1', '1', '4', NULL, '1', 'Recipe for Product #4', '1.00', 'portion', '33.86', '0', '1', '2026-09-09 06:04:43', '2026-09-09 06:04:55', '10', '0.30');
INSERT INTO `recipes` (`id`, `branch_id`, `product_id`, `variant_id`, `version`, `name`, `yield_quantity`, `yield_unit`, `total_cost`, `is_active`, `created_by`, `created_at`, `updated_at`, `ingredient_id`, `quantity_required`) VALUES ('2', '1', '4', NULL, '1', 'Recipe for Product #4', '1.00', 'portion', '33.86', '0', '1', '2026-09-09 06:04:55', '2026-09-09 06:05:06', '11', '0.30');
INSERT INTO `recipes` (`id`, `branch_id`, `product_id`, `variant_id`, `version`, `name`, `yield_quantity`, `yield_unit`, `total_cost`, `is_active`, `created_by`, `created_at`, `updated_at`, `ingredient_id`, `quantity_required`) VALUES ('3', '1', '4', NULL, '1', 'Recipe for Product #4', '1.00', 'portion', '33.86', '0', '1', '2026-09-09 06:05:06', '2026-09-09 06:05:15', '12', '0.30');
INSERT INTO `recipes` (`id`, `branch_id`, `product_id`, `variant_id`, `version`, `name`, `yield_quantity`, `yield_unit`, `total_cost`, `is_active`, `created_by`, `created_at`, `updated_at`, `ingredient_id`, `quantity_required`) VALUES ('4', '1', '4', NULL, '1', 'Recipe for Product #4', '1.00', 'portion', '33.86', '0', '1', '2026-09-09 06:05:15', '2026-09-09 06:05:23', '13', '0.30');
INSERT INTO `recipes` (`id`, `branch_id`, `product_id`, `variant_id`, `version`, `name`, `yield_quantity`, `yield_unit`, `total_cost`, `is_active`, `created_by`, `created_at`, `updated_at`, `ingredient_id`, `quantity_required`) VALUES ('5', '1', '4', NULL, '1', 'Recipe for Product #4', '1.00', 'portion', '33.86', '0', '1', '2026-09-09 06:05:23', '2026-09-09 06:05:31', '14', '0.30');
INSERT INTO `recipes` (`id`, `branch_id`, `product_id`, `variant_id`, `version`, `name`, `yield_quantity`, `yield_unit`, `total_cost`, `is_active`, `created_by`, `created_at`, `updated_at`, `ingredient_id`, `quantity_required`) VALUES ('6', '1', '4', NULL, '1', 'Recipe for Product #4', '1.00', 'portion', '33.86', '0', '1', '2026-09-09 06:05:31', '2026-09-09 06:05:43', '15', '0.30');
INSERT INTO `recipes` (`id`, `branch_id`, `product_id`, `variant_id`, `version`, `name`, `yield_quantity`, `yield_unit`, `total_cost`, `is_active`, `created_by`, `created_at`, `updated_at`, `ingredient_id`, `quantity_required`) VALUES ('7', '1', '4', NULL, '1', 'Recipe for Product #4', '1.00', 'portion', '33.86', '0', '1', '2026-09-09 06:05:43', '2026-09-09 06:06:06', '16', '0.30');
INSERT INTO `recipes` (`id`, `branch_id`, `product_id`, `variant_id`, `version`, `name`, `yield_quantity`, `yield_unit`, `total_cost`, `is_active`, `created_by`, `created_at`, `updated_at`, `ingredient_id`, `quantity_required`) VALUES ('8', '1', '4', NULL, '1', 'Recipe for Product #4', '1.00', 'portion', '33.86', '0', '1', '2026-09-09 06:06:06', '2026-09-09 06:06:20', '18', '0.30');
INSERT INTO `recipes` (`id`, `branch_id`, `product_id`, `variant_id`, `version`, `name`, `yield_quantity`, `yield_unit`, `total_cost`, `is_active`, `created_by`, `created_at`, `updated_at`, `ingredient_id`, `quantity_required`) VALUES ('9', '1', '4', NULL, '1', 'Recipe for Product #4', '1.00', 'portion', '33.86', '0', '1', '2026-09-09 06:06:20', '2026-09-09 06:06:26', '19', '0.30');
INSERT INTO `recipes` (`id`, `branch_id`, `product_id`, `variant_id`, `version`, `name`, `yield_quantity`, `yield_unit`, `total_cost`, `is_active`, `created_by`, `created_at`, `updated_at`, `ingredient_id`, `quantity_required`) VALUES ('10', '1', '4', NULL, '1', 'Recipe for Product #4', '1.00', 'portion', '33.86', '1', '1', '2026-09-09 06:06:26', '2026-09-09 06:06:26', '20', '0.30');

-- Table structure for `reconciliations`
DROP TABLE IF EXISTS `reconciliations`;
CREATE TABLE `reconciliations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `branch_id` int(11) NOT NULL DEFAULT 1,
  `shift_id` int(11) DEFAULT NULL,
  `business_date` date NOT NULL,
  `payment_method_id` int(11) NOT NULL,
  `expected_amount` decimal(14,2) NOT NULL DEFAULT 0.00,
  `actual_amount` decimal(14,2) NOT NULL DEFAULT 0.00,
  `difference` decimal(14,2) NOT NULL DEFAULT 0.00,
  `status` enum('PENDING','VERIFIED','DISCREPANCY') NOT NULL DEFAULT 'PENDING',
  `notes` text DEFAULT NULL,
  `verified_by_user_id` int(11) DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_recon_branch_date` (`branch_id`,`business_date`),
  KEY `idx_recon_method` (`payment_method_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for `refunds`
DROP TABLE IF EXISTS `refunds`;
CREATE TABLE `refunds` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `refund_number` varchar(40) DEFAULT NULL,
  `branch_id` int(10) unsigned NOT NULL DEFAULT 1,
  `payment_id` int(10) unsigned NOT NULL,
  `order_id` int(10) unsigned NOT NULL,
  `refund_amount` decimal(12,2) NOT NULL,
  `status` enum('PENDING','APPROVED','PROCESSED','REJECTED') DEFAULT 'PROCESSED',
  `approved_by_user_id` int(10) unsigned DEFAULT NULL,
  `reason` text NOT NULL,
  `processed_by_user_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `payment_id` (`payment_id`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `refunds_ibfk_1` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`),
  CONSTRAINT `refunds_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for `refunds`
INSERT INTO `refunds` (`id`, `refund_number`, `branch_id`, `payment_id`, `order_id`, `refund_amount`, `status`, `approved_by_user_id`, `reason`, `processed_by_user_id`, `created_at`, `updated_at`) VALUES ('1', 'REF-20260908-974101', '1', '8', '15', '50.00', 'PROCESSED', '1', 'Customer price adjustment discount request', '1', '2026-09-09 05:47:02', '2026-09-09 05:47:02');
INSERT INTO `refunds` (`id`, `refund_number`, `branch_id`, `payment_id`, `order_id`, `refund_amount`, `status`, `approved_by_user_id`, `reason`, `processed_by_user_id`, `created_at`, `updated_at`) VALUES ('2', 'REF-20260908-711279', '1', '10', '17', '525.00', 'PROCESSED', '1', 'Customer partial dish refund', '1', '2026-09-09 05:51:21', '2026-09-09 05:51:21');
INSERT INTO `refunds` (`id`, `refund_number`, `branch_id`, `payment_id`, `order_id`, `refund_amount`, `status`, `approved_by_user_id`, `reason`, `processed_by_user_id`, `created_at`, `updated_at`) VALUES ('3', 'REF-20260908-238118', '1', '11', '18', '525.00', 'PROCESSED', '1', 'Customer partial dish refund', '1', '2026-09-09 05:51:31', '2026-09-09 05:51:31');
INSERT INTO `refunds` (`id`, `refund_number`, `branch_id`, `payment_id`, `order_id`, `refund_amount`, `status`, `approved_by_user_id`, `reason`, `processed_by_user_id`, `created_at`, `updated_at`) VALUES ('4', 'REF-20260908-713589', '1', '12', '19', '525.00', 'PROCESSED', '1', 'Customer partial dish refund', '1', '2026-09-09 05:51:40', '2026-09-09 05:51:40');
INSERT INTO `refunds` (`id`, `refund_number`, `branch_id`, `payment_id`, `order_id`, `refund_amount`, `status`, `approved_by_user_id`, `reason`, `processed_by_user_id`, `created_at`, `updated_at`) VALUES ('5', 'REF-20260908-126456', '1', '13', '20', '525.00', 'PROCESSED', '1', 'Customer partial dish refund', '1', '2026-09-09 05:51:48', '2026-09-09 05:51:48');

-- Table structure for `reservations`
DROP TABLE IF EXISTS `reservations`;
CREATE TABLE `reservations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` int(10) unsigned DEFAULT NULL,
  `reservation_number` varchar(50) DEFAULT NULL,
  `customer_id` int(10) unsigned NOT NULL,
  `table_id` int(10) unsigned NOT NULL,
  `reservation_date` date DEFAULT NULL,
  `guest_count` int(11) NOT NULL DEFAULT 2,
  `duration_minutes` int(11) DEFAULT 90,
  `reservation_time` datetime NOT NULL,
  `status` enum('REQUESTED','CONFIRMED','SEATED','COMPLETED','CANCELLED','NO_SHOW','EXPIRED','REJECTED') DEFAULT 'CONFIRMED',
  `notes` varchar(255) DEFAULT NULL,
  `created_by_user_id` int(10) unsigned DEFAULT NULL,
  `confirmed_by_user_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `cancelled_at` datetime DEFAULT NULL,
  `seated_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_res_number` (`reservation_number`),
  KEY `customer_id` (`customer_id`),
  KEY `idx_res_branch` (`branch_id`),
  KEY `idx_res_date` (`reservation_date`),
  KEY `idx_res_status` (`status`),
  KEY `idx_res_branch_date_status` (`branch_id`,`reservation_date`,`status`),
  KEY `idx_res_table_time` (`table_id`,`reservation_date`,`status`),
  CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`table_id`) REFERENCES `restaurant_tables` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for `reservations`
INSERT INTO `reservations` (`id`, `branch_id`, `reservation_number`, `customer_id`, `table_id`, `reservation_date`, `guest_count`, `duration_minutes`, `reservation_time`, `status`, `notes`, `created_by_user_id`, `confirmed_by_user_id`, `created_at`, `updated_at`, `cancelled_at`, `seated_at`, `completed_at`) VALUES ('1', '1', 'RES-20260909-0001', '2', '2', '2026-09-10', '2', '90', '2026-09-10 19:30:00', 'SEATED', 'Test reservation 1', '1', '1', '2026-09-09 06:52:16', '2026-09-09 06:52:16', NULL, '2026-09-09 06:52:16', NULL);
INSERT INTO `reservations` (`id`, `branch_id`, `reservation_number`, `customer_id`, `table_id`, `reservation_date`, `guest_count`, `duration_minutes`, `reservation_time`, `status`, `notes`, `created_by_user_id`, `confirmed_by_user_id`, `created_at`, `updated_at`, `cancelled_at`, `seated_at`, `completed_at`) VALUES ('2', '1', 'RES-20260909-0002', '4', '2', '2026-10-05', '2', '90', '2026-10-05 19:30:00', 'SEATED', 'Test reservation 1', '1', '1', '2026-09-09 06:52:51', '2026-09-09 06:52:51', NULL, '2026-09-09 06:52:51', NULL);
INSERT INTO `reservations` (`id`, `branch_id`, `reservation_number`, `customer_id`, `table_id`, `reservation_date`, `guest_count`, `duration_minutes`, `reservation_time`, `status`, `notes`, `created_by_user_id`, `confirmed_by_user_id`, `created_at`, `updated_at`, `cancelled_at`, `seated_at`, `completed_at`) VALUES ('3', '1', 'RES-20260909-0003', '5', '2', '2026-10-01', '2', '90', '2026-10-01 19:30:00', 'SEATED', 'Test reservation 1', '1', '1', '2026-09-09 06:53:01', '2026-09-09 06:53:01', NULL, '2026-09-09 06:53:01', NULL);
INSERT INTO `reservations` (`id`, `branch_id`, `reservation_number`, `customer_id`, `table_id`, `reservation_date`, `guest_count`, `duration_minutes`, `reservation_time`, `status`, `notes`, `created_by_user_id`, `confirmed_by_user_id`, `created_at`, `updated_at`, `cancelled_at`, `seated_at`, `completed_at`) VALUES ('4', '1', 'RES-20260909-0004', '6', '2', '2026-10-10', '2', '90', '2026-10-10 19:30:00', 'SEATED', 'Test reservation 1', '1', '1', '2026-09-09 06:53:19', '2026-09-09 06:53:19', NULL, '2026-09-09 06:53:19', NULL);
INSERT INTO `reservations` (`id`, `branch_id`, `reservation_number`, `customer_id`, `table_id`, `reservation_date`, `guest_count`, `duration_minutes`, `reservation_time`, `status`, `notes`, `created_by_user_id`, `confirmed_by_user_id`, `created_at`, `updated_at`, `cancelled_at`, `seated_at`, `completed_at`) VALUES ('5', '1', 'RES-20260909-0005', '7', '2', '2026-09-26', '2', '90', '2026-09-26 19:30:00', 'SEATED', 'Test reservation 1', '1', '1', '2026-09-09 06:53:45', '2026-09-09 06:53:45', NULL, '2026-09-09 06:53:45', NULL);
INSERT INTO `reservations` (`id`, `branch_id`, `reservation_number`, `customer_id`, `table_id`, `reservation_date`, `guest_count`, `duration_minutes`, `reservation_time`, `status`, `notes`, `created_by_user_id`, `confirmed_by_user_id`, `created_at`, `updated_at`, `cancelled_at`, `seated_at`, `completed_at`) VALUES ('6', '1', 'RES-20260909-0006', '9', '2', '2026-10-19', '2', '90', '2026-10-19 19:30:00', 'SEATED', 'Test reservation 1', '1', '1', '2026-09-09 06:53:54', '2026-09-09 06:53:54', NULL, '2026-09-09 06:53:54', NULL);
INSERT INTO `reservations` (`id`, `branch_id`, `reservation_number`, `customer_id`, `table_id`, `reservation_date`, `guest_count`, `duration_minutes`, `reservation_time`, `status`, `notes`, `created_by_user_id`, `confirmed_by_user_id`, `created_at`, `updated_at`, `cancelled_at`, `seated_at`, `completed_at`) VALUES ('7', '1', 'RES-20260909-0007', '11', '2', '2026-10-28', '2', '90', '2026-10-28 19:30:00', 'SEATED', 'Test reservation 1', '1', '1', '2026-09-09 06:54:03', '2026-09-09 06:54:03', NULL, '2026-09-09 06:54:03', NULL);

-- Table structure for `restaurant_tables`
DROP TABLE IF EXISTS `restaurant_tables`;
CREATE TABLE `restaurant_tables` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `floor_id` int(10) unsigned NOT NULL,
  `table_number` varchar(30) NOT NULL,
  `capacity` int(11) NOT NULL DEFAULT 4,
  `status` enum('AVAILABLE','OCCUPIED','RESERVED','WAITING_PAYMENT','CLEANING','OUT_OF_SERVICE') DEFAULT 'AVAILABLE',
  `is_active` tinyint(1) DEFAULT 1,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_floor_table` (`floor_id`,`table_number`),
  CONSTRAINT `restaurant_tables_ibfk_1` FOREIGN KEY (`floor_id`) REFERENCES `floors` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for `restaurant_tables`
INSERT INTO `restaurant_tables` (`id`, `floor_id`, `table_number`, `capacity`, `status`, `is_active`, `deleted_at`) VALUES ('2', '1', 'T-100', '4', 'OCCUPIED', '1', NULL);

-- Table structure for `role_permissions`
DROP TABLE IF EXISTS `role_permissions`;
CREATE TABLE `role_permissions` (
  `role_id` int(10) unsigned NOT NULL,
  `permission_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`permission_id`),
  KEY `permission_id` (`permission_id`),
  CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for `roles`
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for `roles`
INSERT INTO `roles` (`id`, `name`, `description`, `created_at`) VALUES ('1', 'System Administrator', 'Full Admin', '2026-09-09 05:40:44');

-- Table structure for `settings`
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for `settlements`
DROP TABLE IF EXISTS `settlements`;
CREATE TABLE `settlements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `branch_id` int(11) NOT NULL DEFAULT 1,
  `payment_method_id` int(11) NOT NULL,
  `settlement_reference` varchar(100) NOT NULL,
  `settlement_date` date NOT NULL,
  `expected_amount` decimal(14,2) NOT NULL DEFAULT 0.00,
  `actual_amount` decimal(14,2) NOT NULL DEFAULT 0.00,
  `difference` decimal(14,2) NOT NULL DEFAULT 0.00,
  `status` enum('PENDING','SUBMITTED','VERIFIED','DISCREPANCY','CLOSED') NOT NULL DEFAULT 'PENDING',
  `created_by_user_id` int(11) NOT NULL,
  `verified_by_user_id` int(11) DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_set_branch_date` (`branch_id`,`settlement_date`),
  KEY `idx_set_method` (`payment_method_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for `shifts`
DROP TABLE IF EXISTS `shifts`;
CREATE TABLE `shifts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` int(11) NOT NULL DEFAULT 1,
  `user_id` int(10) unsigned NOT NULL,
  `cash_drawer_id` int(11) DEFAULT 1,
  `opened_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `closed_at` timestamp NULL DEFAULT NULL,
  `opening_balance` decimal(12,2) NOT NULL DEFAULT 0.00,
  `opening_cash` decimal(14,2) NOT NULL DEFAULT 0.00,
  `expected_cash` decimal(14,2) NOT NULL DEFAULT 0.00,
  `closing_balance` decimal(12,2) DEFAULT NULL,
  `actual_cash` decimal(14,2) NOT NULL DEFAULT 0.00,
  `cash_difference` decimal(14,2) NOT NULL DEFAULT 0.00,
  `status` enum('OPEN','CLOSED') DEFAULT 'OPEN',
  `notes` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `shifts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for `shifts`
INSERT INTO `shifts` (`id`, `branch_id`, `user_id`, `cash_drawer_id`, `opened_at`, `closed_at`, `opening_balance`, `opening_cash`, `expected_cash`, `closing_balance`, `actual_cash`, `cash_difference`, `status`, `notes`, `created_at`, `updated_at`) VALUES ('2', '1', '1', '1', '2026-09-09 06:45:46', '2026-09-09 06:46:02', '10000.00', '10000.00', '10000.00', NULL, '0.00', '0.00', 'CLOSED', 'Automated Test Shift Float', '2026-09-09 06:45:46', '2026-09-09 06:46:02');
INSERT INTO `shifts` (`id`, `branch_id`, `user_id`, `cash_drawer_id`, `opened_at`, `closed_at`, `opening_balance`, `opening_cash`, `expected_cash`, `closing_balance`, `actual_cash`, `cash_difference`, `status`, `notes`, `created_at`, `updated_at`) VALUES ('3', '1', '1', '1', '2026-09-09 06:46:02', '2026-09-09 06:46:12', '10000.00', '10000.00', '10000.00', NULL, '0.00', '0.00', 'CLOSED', 'Automated Test Shift Float', '2026-09-09 06:46:02', '2026-09-09 06:46:12');
INSERT INTO `shifts` (`id`, `branch_id`, `user_id`, `cash_drawer_id`, `opened_at`, `closed_at`, `opening_balance`, `opening_cash`, `expected_cash`, `closing_balance`, `actual_cash`, `cash_difference`, `status`, `notes`, `created_at`, `updated_at`) VALUES ('4', '1', '1', '1', '2026-09-09 06:46:12', '2026-09-09 06:46:12', '10000.00', '10000.00', '10700.00', '10650.00', '10650.00', '-50.00', 'CLOSED', 'Shift Closed with ৳50 coin shortage', '2026-09-09 06:46:12', '2026-09-09 06:46:12');

-- Table structure for `stations`
DROP TABLE IF EXISTS `stations`;
CREATE TABLE `stations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` int(10) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `type` enum('KITCHEN','BAR','COFFEE','GRILL','DESSERT','PACKAGING') DEFAULT 'KITCHEN',
  `badge_code` varchar(20) NOT NULL,
  `display_device` varchar(100) DEFAULT NULL,
  `printer` varchar(100) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('ACTIVE','INACTIVE','PAUSED','OUT_OF_SERVICE') DEFAULT 'ACTIVE',
  `is_paused` tinyint(1) DEFAULT 0,
  `pause_reason` varchar(255) DEFAULT NULL,
  `paused_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `badge_code` (`badge_code`),
  KEY `branch_id` (`branch_id`),
  CONSTRAINT `stations_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for `stations`
INSERT INTO `stations` (`id`, `branch_id`, `name`, `type`, `badge_code`, `display_device`, `printer`, `display_order`, `created_at`, `updated_at`, `status`, `is_paused`, `pause_reason`, `paused_at`) VALUES ('1', '1', 'Main Kitchen', 'KITCHEN', 'ST-KITCHEN', NULL, NULL, '0', '2026-09-09 05:40:44', '2026-09-09 05:40:44', 'ACTIVE', '0', NULL, NULL);
INSERT INTO `stations` (`id`, `branch_id`, `name`, `type`, `badge_code`, `display_device`, `printer`, `display_order`, `created_at`, `updated_at`, `status`, `is_paused`, `pause_reason`, `paused_at`) VALUES ('2', '1', 'Beverage Bar', 'BAR', 'ST-BAR', NULL, NULL, '0', '2026-09-09 05:40:44', '2026-09-09 05:40:44', 'ACTIVE', '0', NULL, NULL);
INSERT INTO `stations` (`id`, `branch_id`, `name`, `type`, `badge_code`, `display_device`, `printer`, `display_order`, `created_at`, `updated_at`, `status`, `is_paused`, `pause_reason`, `paused_at`) VALUES ('3', '1', 'Coffee Corner', 'COFFEE', 'ST-COFFEE', NULL, NULL, '0', '2026-09-09 05:40:44', '2026-09-09 05:40:44', 'ACTIVE', '0', NULL, NULL);
INSERT INTO `stations` (`id`, `branch_id`, `name`, `type`, `badge_code`, `display_device`, `printer`, `display_order`, `created_at`, `updated_at`, `status`, `is_paused`, `pause_reason`, `paused_at`) VALUES ('4', '1', 'Dessert Station', 'DESSERT', 'ST-DESSERT', NULL, NULL, '0', '2026-09-09 05:40:44', '2026-09-09 05:40:44', 'ACTIVE', '0', NULL, NULL);
INSERT INTO `stations` (`id`, `branch_id`, `name`, `type`, `badge_code`, `display_device`, `printer`, `display_order`, `created_at`, `updated_at`, `status`, `is_paused`, `pause_reason`, `paused_at`) VALUES ('5', '1', 'Test Grill Station', 'GRILL', 'ST-GRILLTEST', NULL, NULL, '0', '2026-09-09 05:54:57', '2026-09-09 05:54:57', 'ACTIVE', '0', NULL, NULL);
INSERT INTO `stations` (`id`, `branch_id`, `name`, `type`, `badge_code`, `display_device`, `printer`, `display_order`, `created_at`, `updated_at`, `status`, `is_paused`, `pause_reason`, `paused_at`) VALUES ('7', '1', 'Test Grill Station 415', 'GRILL', 'ST-GRILL-7602', NULL, NULL, '0', '2026-09-09 05:55:18', '2026-09-09 05:55:18', 'ACTIVE', '0', NULL, NULL);
INSERT INTO `stations` (`id`, `branch_id`, `name`, `type`, `badge_code`, `display_device`, `printer`, `display_order`, `created_at`, `updated_at`, `status`, `is_paused`, `pause_reason`, `paused_at`) VALUES ('8', '1', 'Test Grill Station 304', 'GRILL', 'ST-GRILL-6101', NULL, NULL, '0', '2026-09-09 05:55:25', '2026-09-09 05:55:25', 'ACTIVE', '0', NULL, NULL);
INSERT INTO `stations` (`id`, `branch_id`, `name`, `type`, `badge_code`, `display_device`, `printer`, `display_order`, `created_at`, `updated_at`, `status`, `is_paused`, `pause_reason`, `paused_at`) VALUES ('9', '1', 'Test Grill Station 146', 'GRILL', 'ST-GRILL-8272', NULL, NULL, '0', '2026-09-09 05:55:33', '2026-09-09 05:55:33', 'ACTIVE', '0', NULL, NULL);
INSERT INTO `stations` (`id`, `branch_id`, `name`, `type`, `badge_code`, `display_device`, `printer`, `display_order`, `created_at`, `updated_at`, `status`, `is_paused`, `pause_reason`, `paused_at`) VALUES ('10', '1', 'Test Grill Station 276', 'GRILL', 'ST-GRILL-8028', NULL, NULL, '0', '2026-09-09 05:55:45', '2026-09-09 05:55:45', 'ACTIVE', '0', NULL, NULL);
INSERT INTO `stations` (`id`, `branch_id`, `name`, `type`, `badge_code`, `display_device`, `printer`, `display_order`, `created_at`, `updated_at`, `status`, `is_paused`, `pause_reason`, `paused_at`) VALUES ('11', '1', 'Test Grill Station 714', 'GRILL', 'ST-GRILL-1936', NULL, NULL, '0', '2026-09-09 05:56:00', '2026-09-09 05:56:00', 'ACTIVE', '0', NULL, NULL);
INSERT INTO `stations` (`id`, `branch_id`, `name`, `type`, `badge_code`, `display_device`, `printer`, `display_order`, `created_at`, `updated_at`, `status`, `is_paused`, `pause_reason`, `paused_at`) VALUES ('12', '1', 'Test Grill Station 966', 'GRILL', 'ST-GRILL-2817', NULL, NULL, '0', '2026-09-09 05:56:07', '2026-09-09 05:56:07', 'ACTIVE', '0', NULL, NULL);

-- Table structure for `stock_transfer_items`
DROP TABLE IF EXISTS `stock_transfer_items`;
CREATE TABLE `stock_transfer_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `stock_transfer_id` int(10) unsigned NOT NULL,
  `ingredient_id` int(10) unsigned NOT NULL,
  `quantity` decimal(12,4) NOT NULL,
  `unit` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_transfer_id` (`stock_transfer_id`),
  KEY `ingredient_id` (`ingredient_id`),
  CONSTRAINT `stock_transfer_items_ibfk_1` FOREIGN KEY (`stock_transfer_id`) REFERENCES `stock_transfers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stock_transfer_items_ibfk_2` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `stock_transfer_items`
INSERT INTO `stock_transfer_items` (`id`, `stock_transfer_id`, `ingredient_id`, `quantity`, `unit`) VALUES ('1', '1', '19', '10.0000', 'kg');
INSERT INTO `stock_transfer_items` (`id`, `stock_transfer_id`, `ingredient_id`, `quantity`, `unit`) VALUES ('2', '2', '20', '10.0000', 'kg');

-- Table structure for `stock_transfers`
DROP TABLE IF EXISTS `stock_transfers`;
CREATE TABLE `stock_transfers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` int(10) unsigned NOT NULL DEFAULT 1,
  `transfer_number` varchar(50) NOT NULL,
  `source_location_id` int(10) unsigned NOT NULL,
  `destination_location_id` int(10) unsigned NOT NULL,
  `status` enum('COMPLETED','CANCELLED') DEFAULT 'COMPLETED',
  `created_by` int(10) unsigned DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `transfer_number` (`transfer_number`),
  KEY `source_location_id` (`source_location_id`),
  KEY `destination_location_id` (`destination_location_id`),
  CONSTRAINT `stock_transfers_ibfk_1` FOREIGN KEY (`source_location_id`) REFERENCES `inventory_locations` (`id`),
  CONSTRAINT `stock_transfers_ibfk_2` FOREIGN KEY (`destination_location_id`) REFERENCES `inventory_locations` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `stock_transfers`
INSERT INTO `stock_transfers` (`id`, `branch_id`, `transfer_number`, `source_location_id`, `destination_location_id`, `status`, `created_by`, `notes`, `created_at`) VALUES ('1', '1', 'TRF-20260909-7785', '1', '2', 'COMPLETED', '1', 'Daily prep shift transfer', '2026-09-09 06:06:20');
INSERT INTO `stock_transfers` (`id`, `branch_id`, `transfer_number`, `source_location_id`, `destination_location_id`, `status`, `created_by`, `notes`, `created_at`) VALUES ('2', '1', 'TRF-20260909-2944', '1', '2', 'COMPLETED', '1', 'Daily prep shift transfer', '2026-09-09 06:06:27');

-- Table structure for `suppliers`
DROP TABLE IF EXISTS `suppliers`;
CREATE TABLE `suppliers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` int(10) unsigned NOT NULL DEFAULT 1,
  `supplier_code` varchar(50) NOT NULL,
  `name` varchar(150) NOT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `tax_number` varchar(50) DEFAULT NULL,
  `payment_terms` varchar(100) DEFAULT NULL,
  `status` enum('ACTIVE','INACTIVE') DEFAULT 'ACTIVE',
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `supplier_code` (`supplier_code`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `suppliers`
INSERT INTO `suppliers` (`id`, `branch_id`, `supplier_code`, `name`, `contact_person`, `email`, `phone`, `address`, `tax_number`, `payment_terms`, `status`, `created_at`) VALUES ('1', '1', 'SUP-TEJG-973', 'Tejgaon Grain Suppliers Ltd.', 'Kamal Hossain', 'kamal@tejgaongrain.com', '+8801700998877', 'Tejgaon Industrial Area, Dhaka', NULL, NULL, 'ACTIVE', '2026-09-09 06:02:59');
INSERT INTO `suppliers` (`id`, `branch_id`, `supplier_code`, `name`, `contact_person`, `email`, `phone`, `address`, `tax_number`, `payment_terms`, `status`, `created_at`) VALUES ('2', '1', 'SUP-TEJG-138', 'Tejgaon Grain Suppliers Ltd.', 'Kamal Hossain', 'kamal@tejgaongrain.com', '+8801700998877', 'Tejgaon Industrial Area, Dhaka', NULL, NULL, 'ACTIVE', '2026-09-09 06:03:28');
INSERT INTO `suppliers` (`id`, `branch_id`, `supplier_code`, `name`, `contact_person`, `email`, `phone`, `address`, `tax_number`, `payment_terms`, `status`, `created_at`) VALUES ('3', '1', 'SUP-TEJG-988', 'Tejgaon Grain Suppliers Ltd.', 'Kamal Hossain', 'kamal@tejgaongrain.com', '+8801700998877', 'Tejgaon Industrial Area, Dhaka', NULL, NULL, 'ACTIVE', '2026-09-09 06:03:50');
INSERT INTO `suppliers` (`id`, `branch_id`, `supplier_code`, `name`, `contact_person`, `email`, `phone`, `address`, `tax_number`, `payment_terms`, `status`, `created_at`) VALUES ('4', '1', 'SUP-TEJG-209', 'Tejgaon Grain Suppliers Ltd.', 'Kamal Hossain', 'kamal@tejgaongrain.com', '+8801700998877', 'Tejgaon Industrial Area, Dhaka', NULL, NULL, 'ACTIVE', '2026-09-09 06:04:00');
INSERT INTO `suppliers` (`id`, `branch_id`, `supplier_code`, `name`, `contact_person`, `email`, `phone`, `address`, `tax_number`, `payment_terms`, `status`, `created_at`) VALUES ('5', '1', 'SUP-TEJG-338', 'Tejgaon Grain Suppliers Ltd.', 'Kamal Hossain', 'kamal@tejgaongrain.com', '+8801700998877', 'Tejgaon Industrial Area, Dhaka', NULL, NULL, 'ACTIVE', '2026-09-09 06:04:08');
INSERT INTO `suppliers` (`id`, `branch_id`, `supplier_code`, `name`, `contact_person`, `email`, `phone`, `address`, `tax_number`, `payment_terms`, `status`, `created_at`) VALUES ('6', '1', 'SUP-TEJG-528', 'Tejgaon Grain Suppliers Ltd.', 'Kamal Hossain', 'kamal@tejgaongrain.com', '+8801700998877', 'Tejgaon Industrial Area, Dhaka', NULL, NULL, 'ACTIVE', '2026-09-09 06:04:26');
INSERT INTO `suppliers` (`id`, `branch_id`, `supplier_code`, `name`, `contact_person`, `email`, `phone`, `address`, `tax_number`, `payment_terms`, `status`, `created_at`) VALUES ('7', '1', 'SUP-TEJG-933', 'Tejgaon Grain Suppliers Ltd.', 'Kamal Hossain', 'kamal@tejgaongrain.com', '+8801700998877', 'Tejgaon Industrial Area, Dhaka', NULL, NULL, 'ACTIVE', '2026-09-09 06:04:35');
INSERT INTO `suppliers` (`id`, `branch_id`, `supplier_code`, `name`, `contact_person`, `email`, `phone`, `address`, `tax_number`, `payment_terms`, `status`, `created_at`) VALUES ('8', '1', 'SUP-TEJG-703', 'Tejgaon Grain Suppliers Ltd.', 'Kamal Hossain', 'kamal@tejgaongrain.com', '+8801700998877', 'Tejgaon Industrial Area, Dhaka', NULL, NULL, 'ACTIVE', '2026-09-09 06:04:43');
INSERT INTO `suppliers` (`id`, `branch_id`, `supplier_code`, `name`, `contact_person`, `email`, `phone`, `address`, `tax_number`, `payment_terms`, `status`, `created_at`) VALUES ('9', '1', 'SUP-TEJG-165', 'Tejgaon Grain Suppliers Ltd.', 'Kamal Hossain', 'kamal@tejgaongrain.com', '+8801700998877', 'Tejgaon Industrial Area, Dhaka', NULL, NULL, 'ACTIVE', '2026-09-09 06:04:55');
INSERT INTO `suppliers` (`id`, `branch_id`, `supplier_code`, `name`, `contact_person`, `email`, `phone`, `address`, `tax_number`, `payment_terms`, `status`, `created_at`) VALUES ('10', '1', 'SUP-TEJG-696', 'Tejgaon Grain Suppliers Ltd.', 'Kamal Hossain', 'kamal@tejgaongrain.com', '+8801700998877', 'Tejgaon Industrial Area, Dhaka', NULL, NULL, 'ACTIVE', '2026-09-09 06:05:06');
INSERT INTO `suppliers` (`id`, `branch_id`, `supplier_code`, `name`, `contact_person`, `email`, `phone`, `address`, `tax_number`, `payment_terms`, `status`, `created_at`) VALUES ('11', '1', 'SUP-TEJG-796', 'Tejgaon Grain Suppliers Ltd.', 'Kamal Hossain', 'kamal@tejgaongrain.com', '+8801700998877', 'Tejgaon Industrial Area, Dhaka', NULL, NULL, 'ACTIVE', '2026-09-09 06:05:15');
INSERT INTO `suppliers` (`id`, `branch_id`, `supplier_code`, `name`, `contact_person`, `email`, `phone`, `address`, `tax_number`, `payment_terms`, `status`, `created_at`) VALUES ('12', '1', 'SUP-TEJG-807', 'Tejgaon Grain Suppliers Ltd.', 'Kamal Hossain', 'kamal@tejgaongrain.com', '+8801700998877', 'Tejgaon Industrial Area, Dhaka', NULL, NULL, 'ACTIVE', '2026-09-09 06:05:23');
INSERT INTO `suppliers` (`id`, `branch_id`, `supplier_code`, `name`, `contact_person`, `email`, `phone`, `address`, `tax_number`, `payment_terms`, `status`, `created_at`) VALUES ('13', '1', 'SUP-TEJG-897', 'Tejgaon Grain Suppliers Ltd.', 'Kamal Hossain', 'kamal@tejgaongrain.com', '+8801700998877', 'Tejgaon Industrial Area, Dhaka', NULL, NULL, 'ACTIVE', '2026-09-09 06:05:31');
INSERT INTO `suppliers` (`id`, `branch_id`, `supplier_code`, `name`, `contact_person`, `email`, `phone`, `address`, `tax_number`, `payment_terms`, `status`, `created_at`) VALUES ('14', '1', 'SUP-TEJG-560', 'Tejgaon Grain Suppliers Ltd.', 'Kamal Hossain', 'kamal@tejgaongrain.com', '+8801700998877', 'Tejgaon Industrial Area, Dhaka', NULL, NULL, 'ACTIVE', '2026-09-09 06:05:43');
INSERT INTO `suppliers` (`id`, `branch_id`, `supplier_code`, `name`, `contact_person`, `email`, `phone`, `address`, `tax_number`, `payment_terms`, `status`, `created_at`) VALUES ('15', '1', 'SUP-TEJG-252', 'Tejgaon Grain Suppliers Ltd.', 'Kamal Hossain', 'kamal@tejgaongrain.com', '+8801700998877', 'Tejgaon Industrial Area, Dhaka', NULL, NULL, 'ACTIVE', '2026-09-09 06:05:58');
INSERT INTO `suppliers` (`id`, `branch_id`, `supplier_code`, `name`, `contact_person`, `email`, `phone`, `address`, `tax_number`, `payment_terms`, `status`, `created_at`) VALUES ('16', '1', 'SUP-TEJG-790', 'Tejgaon Grain Suppliers Ltd.', 'Kamal Hossain', 'kamal@tejgaongrain.com', '+8801700998877', 'Tejgaon Industrial Area, Dhaka', NULL, NULL, 'ACTIVE', '2026-09-09 06:06:06');
INSERT INTO `suppliers` (`id`, `branch_id`, `supplier_code`, `name`, `contact_person`, `email`, `phone`, `address`, `tax_number`, `payment_terms`, `status`, `created_at`) VALUES ('17', '1', 'SUP-TEJG-404', 'Tejgaon Grain Suppliers Ltd.', 'Kamal Hossain', 'kamal@tejgaongrain.com', '+8801700998877', 'Tejgaon Industrial Area, Dhaka', NULL, NULL, 'ACTIVE', '2026-09-09 06:06:20');
INSERT INTO `suppliers` (`id`, `branch_id`, `supplier_code`, `name`, `contact_person`, `email`, `phone`, `address`, `tax_number`, `payment_terms`, `status`, `created_at`) VALUES ('18', '1', 'SUP-TEJG-489', 'Tejgaon Grain Suppliers Ltd.', 'Kamal Hossain', 'kamal@tejgaongrain.com', '+8801700998877', 'Tejgaon Industrial Area, Dhaka', NULL, NULL, 'ACTIVE', '2026-09-09 06:06:26');

-- Table structure for `users`
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(10) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(120) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','manager','reception','waiter','kitchen') NOT NULL DEFAULT 'waiter',
  `status` enum('ACTIVE','INACTIVE','ON_BREAK') DEFAULT 'ACTIVE',
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for `users`
INSERT INTO `users` (`id`, `role_id`, `name`, `email`, `phone`, `password_hash`, `role`, `status`, `last_login`, `created_at`, `updated_at`, `deleted_at`) VALUES
('1', '1', 'System Administrator', 'admin@smartresta.com', NULL, '$2y$12$LSKTpAxq6SX/xNz5H9Fm8.GL/LyutLCO01z6EstsT3E5/zPJl3yzO', 'admin', 'ACTIVE', NULL, '2026-09-09 05:40:44', '2026-09-09 05:40:44', NULL),
('2', '2', 'Branch Manager', 'manager@smartresta.com', NULL, '$2y$12$LSKTpAxq6SX/xNz5H9Fm8.GL/LyutLCO01z6EstsT3E5/zPJl3yzO', 'manager', 'ACTIVE', NULL, '2026-09-09 05:40:44', '2026-09-09 05:40:44', NULL),
('3', '3', 'Receptionist / Cashier', 'reception@smartresta.com', NULL, '$2y$12$LSKTpAxq6SX/xNz5H9Fm8.GL/LyutLCO01z6EstsT3E5/zPJl3yzO', 'reception', 'ACTIVE', NULL, '2026-09-09 05:40:44', '2026-09-09 05:40:44', NULL),
('4', '4', 'Head Waiter', 'waiter@smartresta.com', NULL, '$2y$12$LSKTpAxq6SX/xNz5H9Fm8.GL/LyutLCO01z6EstsT3E5/zPJl3yzO', 'waiter', 'ACTIVE', NULL, '2026-09-09 05:40:44', '2026-09-09 05:40:44', NULL),
('5', '5', 'Head Chef', 'kitchen@smartresta.com', NULL, '$2y$12$LSKTpAxq6SX/xNz5H9Fm8.GL/LyutLCO01z6EstsT3E5/zPJl3yzO', 'kitchen', 'ACTIVE', NULL, '2026-09-09 05:40:44', '2026-09-09 05:40:44', NULL);

-- Table structure for `waiter_assignments`
DROP TABLE IF EXISTS `waiter_assignments`;
CREATE TABLE `waiter_assignments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` int(10) unsigned NOT NULL DEFAULT 1,
  `waiter_id` int(10) unsigned NOT NULL,
  `table_id` int(10) unsigned NOT NULL,
  `dining_session_id` int(10) unsigned DEFAULT NULL,
  `order_id` int(10) unsigned DEFAULT NULL,
  `assignment_type` enum('TABLE','SESSION','ORDER') DEFAULT 'TABLE',
  `assigned_by_user_id` int(10) unsigned DEFAULT NULL,
  `status` enum('ACTIVE','COMPLETED','CANCELLED') DEFAULT 'ACTIVE',
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `unassigned_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `waiter_id` (`waiter_id`),
  KEY `table_id` (`table_id`),
  CONSTRAINT `waiter_assignments_ibfk_1` FOREIGN KEY (`waiter_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `waiter_assignments_ibfk_2` FOREIGN KEY (`table_id`) REFERENCES `restaurant_tables` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for `waiter_assignments`
INSERT INTO `waiter_assignments` (`id`, `branch_id`, `waiter_id`, `table_id`, `dining_session_id`, `order_id`, `assignment_type`, `assigned_by_user_id`, `status`, `assigned_at`, `updated_at`, `unassigned_at`) VALUES ('2', '1', '1', '2', NULL, NULL, 'TABLE', '1', 'COMPLETED', '2026-09-09 05:51:12', '2026-09-09 05:51:21', '2026-09-09 05:51:21');
INSERT INTO `waiter_assignments` (`id`, `branch_id`, `waiter_id`, `table_id`, `dining_session_id`, `order_id`, `assignment_type`, `assigned_by_user_id`, `status`, `assigned_at`, `updated_at`, `unassigned_at`) VALUES ('3', '1', '1', '2', NULL, NULL, 'TABLE', '1', 'COMPLETED', '2026-09-09 05:51:21', '2026-09-09 05:51:31', '2026-09-09 05:51:31');
INSERT INTO `waiter_assignments` (`id`, `branch_id`, `waiter_id`, `table_id`, `dining_session_id`, `order_id`, `assignment_type`, `assigned_by_user_id`, `status`, `assigned_at`, `updated_at`, `unassigned_at`) VALUES ('4', '1', '1', '2', NULL, NULL, 'TABLE', '1', 'COMPLETED', '2026-09-09 05:51:31', '2026-09-09 05:51:40', '2026-09-09 05:51:40');
INSERT INTO `waiter_assignments` (`id`, `branch_id`, `waiter_id`, `table_id`, `dining_session_id`, `order_id`, `assignment_type`, `assigned_by_user_id`, `status`, `assigned_at`, `updated_at`, `unassigned_at`) VALUES ('5', '1', '1', '2', NULL, NULL, 'TABLE', '1', 'COMPLETED', '2026-09-09 05:51:40', '2026-09-09 05:51:48', '2026-09-09 05:51:48');
INSERT INTO `waiter_assignments` (`id`, `branch_id`, `waiter_id`, `table_id`, `dining_session_id`, `order_id`, `assignment_type`, `assigned_by_user_id`, `status`, `assigned_at`, `updated_at`, `unassigned_at`) VALUES ('6', '1', '1', '2', NULL, NULL, 'TABLE', '1', 'ACTIVE', '2026-09-09 05:51:48', '2026-09-09 05:51:48', NULL);

-- Table structure for `waiter_profiles`
DROP TABLE IF EXISTS `waiter_profiles`;
CREATE TABLE `waiter_profiles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `employee_code` varchar(40) NOT NULL,
  `branch_id` int(10) unsigned NOT NULL DEFAULT 1,
  `commission_enabled` tinyint(1) DEFAULT 1,
  `default_commission_rule_id` int(10) unsigned DEFAULT NULL,
  `status` enum('ACTIVE','INACTIVE','SUSPENDED') DEFAULT 'ACTIVE',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  UNIQUE KEY `employee_code` (`employee_code`),
  KEY `branch_id` (`branch_id`),
  CONSTRAINT `waiter_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `waiter_profiles_ibfk_2` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `waiter_profiles`
INSERT INTO `waiter_profiles` (`id`, `user_id`, `employee_code`, `branch_id`, `commission_enabled`, `default_commission_rule_id`, `status`, `created_at`, `updated_at`) VALUES ('1', '1', 'WTR-001', '1', '1', NULL, 'ACTIVE', '2026-09-09 05:50:13', '2026-09-09 05:50:13');

-- Table structure for `wastage_records`
DROP TABLE IF EXISTS `wastage_records`;
CREATE TABLE `wastage_records` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` int(10) unsigned NOT NULL DEFAULT 1,
  `ingredient_id` int(10) unsigned NOT NULL,
  `location_id` int(10) unsigned NOT NULL,
  `quantity` decimal(12,4) NOT NULL,
  `unit` varchar(20) NOT NULL,
  `reason` enum('EXPIRED','DAMAGED','SPOILED','BURNED','PREPARATION_LOSS','OTHER') DEFAULT 'OTHER',
  `estimated_cost` decimal(12,2) NOT NULL DEFAULT 0.00,
  `recorded_by` int(10) unsigned DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `ingredient_id` (`ingredient_id`),
  KEY `location_id` (`location_id`),
  CONSTRAINT `wastage_records_ibfk_1` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`id`),
  CONSTRAINT `wastage_records_ibfk_2` FOREIGN KEY (`location_id`) REFERENCES `inventory_locations` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `wastage_records`
INSERT INTO `wastage_records` (`id`, `branch_id`, `ingredient_id`, `location_id`, `quantity`, `unit`, `reason`, `estimated_cost`, `recorded_by`, `notes`, `created_at`) VALUES ('1', '1', '19', '1', '1.5000', 'kg', 'SPOILED', '169.29', '1', 'Humid storage damage', '2026-09-09 06:06:20');
INSERT INTO `wastage_records` (`id`, `branch_id`, `ingredient_id`, `location_id`, `quantity`, `unit`, `reason`, `estimated_cost`, `recorded_by`, `notes`, `created_at`) VALUES ('2', '1', '20', '1', '1.5000', 'kg', 'SPOILED', '169.29', '1', 'Humid storage damage', '2026-09-09 06:06:27');

SET FOREIGN_KEY_CHECKS = 1;
