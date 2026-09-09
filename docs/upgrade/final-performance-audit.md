# SMARTRESTA — Final Performance & Database Indexing Audit

## Database Indexing & Query Performance Optimization

### 1. Indexed Production Foreign Keys (`021_dashboard_and_reporting_indexes.sql`)
- `idx_orders_branch_status_created`: Accelerated dashboard order queues (`branch_id`, `order_status`, `created_at`).
- `idx_order_tickets_station_status`: Accelerated KDS station ticket queries (`station_id`, `status`, `priority`).
- `idx_payments_order_branch`: Accelerated billing settlement lookup (`order_id`, `branch_id`, `status`).
- `idx_inventory_ingredient_branch`: Accelerated stock calculation queries (`ingredient_id`, `branch_id`).
- `idx_audit_logs_user_module`: Accelerated audit trail history lookups (`user_id`, `module`, `created_at`).

### 2. Transactional Concurrency & Row Locking
- `OrderEngine::createDraftOrder`: Wrapped in `BEGIN TRANSACTION ... COMMIT` with rollback protection.
- `KDSEngine::updateTicketStatus`: Uses `SELECT ... FOR UPDATE` to prevent concurrent ticket status conflicts.
- `PaymentEngine::processPayment`: Uses `FOR UPDATE` locking on orders to enforce exact balance settlement.

### 3. Asynchronous Polling Efficiency
- KDS polling frequency optimized to 10 seconds via Fetch API (`SmartKDS.loadKDSGrid`).
- Lightweight health probes configured (`/public/health.php?type=liveness` and `readiness`).
