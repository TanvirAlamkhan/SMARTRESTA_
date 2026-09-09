-- SMARTRESTA Migration 021: Dashboard, Reporting & Analytics Performance Indexes
USE `smartresta_db`;

-- Helper procedure to safely add index if not exists
DROP PROCEDURE IF EXISTS `add_idx_safely`;
DELIMITER //
CREATE PROCEDURE `add_idx_safely`(
    IN tbl VARCHAR(64),
    IN idx VARCHAR(64),
    IN col_def VARCHAR(255)
)
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.statistics 
        WHERE table_schema = DATABASE() AND table_name = tbl AND index_name = idx
    ) THEN
        SET @sql = CONCAT('ALTER TABLE `', tbl, '` ADD INDEX `', idx, '` (', col_def, ')');
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END //
DELIMITER ;

CALL add_idx_safely('orders', 'idx_orders_branch_created', 'branch_id, created_at');
CALL add_idx_safely('orders', 'idx_orders_branch_status', 'branch_id, order_status, created_at');
CALL add_idx_safely('orders', 'idx_orders_pay_status', 'branch_id, payment_status, created_at');

CALL add_idx_safely('payments', 'idx_payments_branch_created', 'branch_id, created_at');
CALL add_idx_safely('payments', 'idx_payments_method', 'branch_id, payment_method_id, created_at');

CALL add_idx_safely('commission_transactions', 'idx_comm_branch_created', 'branch_id, created_at');
CALL add_idx_safely('commission_transactions', 'idx_comm_waiter_status', 'waiter_id, status, created_at');

CALL add_idx_safely('inventory_transactions', 'idx_invtx_branch_created', 'branch_id, created_at');
CALL add_idx_safely('inventory_transactions', 'idx_invtx_ing_type', 'ingredient_id, type, created_at');

CALL add_idx_safely('order_tickets', 'idx_tickets_station_created', 'station_id, created_at');
CALL add_idx_safely('order_tickets', 'idx_tickets_status_created', 'status, created_at');

DROP PROCEDURE IF EXISTS `add_idx_safely`;
