-- Migration 024: Security, Performance Indexes & Observability Optimizations
-- SMARTRESTA Prompt 15

-- 1. High-Frequency Query Indexes on Orders & Order Items
ALTER TABLE `orders`
  ADD INDEX IF NOT EXISTS `idx_ord_branch_status` (`branch_id`, `order_status`),
  ADD INDEX IF NOT EXISTS `idx_ord_cust_pay` (`customer_id`, `payment_status`),
  ADD INDEX IF NOT EXISTS `idx_ord_session` (`dining_session_id`, `order_status`),
  ADD INDEX IF NOT EXISTS `idx_ord_created_status` (`created_at`, `order_status`);

ALTER TABLE `order_items`
  ADD INDEX IF NOT EXISTS `idx_item_ord_status` (`order_id`, `status`),
  ADD INDEX IF NOT EXISTS `idx_item_product` (`product_id`, `status`);

-- 2. Payments & Financial Performance Indexes
ALTER TABLE `payments`
  ADD INDEX IF NOT EXISTS `idx_pay_ord_status` (`order_id`, `status`),
  ADD INDEX IF NOT EXISTS `idx_pay_method_date` (`payment_method_id`, `created_at`);

-- 3. Reservation & Table Conflict Checking Indexes
ALTER TABLE `reservations`
  ADD INDEX IF NOT EXISTS `idx_res_branch_date_status` (`branch_id`, `reservation_date`, `status`),
  ADD INDEX IF NOT EXISTS `idx_res_table_time` (`table_id`, `reservation_date`, `status`);

-- 4. Inventory Ledger Performance Indexes
ALTER TABLE `inventory_transactions`
  ADD INDEX IF NOT EXISTS `idx_inv_tx_item_loc` (`ingredient_id`, `location_id`, `created_at`);

-- 5. Loyalty Ledger & QR Security Indexes
ALTER TABLE `loyalty_transactions`
  ADD INDEX IF NOT EXISTS `idx_loyalty_tx_cust_type` (`customer_id`, `type`, `created_at`);

ALTER TABLE `qr_table_tokens`
  ADD INDEX IF NOT EXISTS `idx_qr_token_status` (`token`, `status`);

-- 6. Audit & Login Rate Limiting Indexes
ALTER TABLE `audit_logs`
  ADD INDEX IF NOT EXISTS `idx_audit_user_action` (`user_id`, `action`, `created_at`),
  ADD INDEX IF NOT EXISTS `idx_audit_created` (`created_at`);

ALTER TABLE `login_attempts`
  ADD INDEX IF NOT EXISTS `idx_login_ip_time` (`ip_address`, `attempted_at`);
