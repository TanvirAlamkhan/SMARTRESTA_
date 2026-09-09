# SMARTRESTA — Data Persistence Dictionary

## Core Entity Persistence Specifications

### 1. User (`users`)
- **Primary Key**: `id` (BIGINT AUTO_INCREMENT)
- **Key Fields**: `branch_id`, `role_id`, `name`, `email`, `password_hash`, `status`
- **Creation Source**: Admin Portal (`/api/v1/users/create.php`)
- **Persistence Mechanism**: Password hashed with `password_hash()`, role assigned via `role_id`.
- **Audit**: Logged on user creation, status change, and password reset.

### 2. Dining Table (`restaurant_tables`)
- **Primary Key**: `id` (BIGINT AUTO_INCREMENT)
- **Key Fields**: `branch_id`, `floor_id`, `table_number`, `capacity`, `status` (`FREE`, `OCCUPIED`, `RESERVED`, `DIRTY`)
- **Creation Source**: Admin / Manager Portal (`/api/v1/tables/`)
- **Persistence Mechanism**: Physical restaurant table mapping with branch scoping.

### 3. Dining Session (`dining_sessions`)
- **Primary Key**: `id` (BIGINT AUTO_INCREMENT)
- **Key Fields**: `branch_id`, `table_id`, `opened_by_user_id`, `closed_by_user_id`, `guest_count`, `status` (`OPEN`, `CLOSED`)
- **Creation Source**: Waiter / Reception Portal (`/api/v1/dining_sessions/open.php`)
- **Persistence Mechanism**: Tracks open dining experience on a table; links multiple orders.

### 4. Order (`orders`)
- **Primary Key**: `id` (BIGINT AUTO_INCREMENT)
- **Key Fields**: `order_number`, `branch_id`, `dining_session_id`, `table_id`, `customer_id`, `taken_by_user_id`, `order_type`, `subtotal`, `discount`, `tax`, `service_charge`, `delivery_charge`, `total`, `payment_status`, `order_status`
- **Creation Source**: Waiter / POS / Reception (`/api/v1/orders/create.php`, `OrderEngine::createDraftOrder`)
- **Persistence Mechanism**: Single source of truth for restaurant transactions. Shared by all 5 portals.

### 5. Order Item (`order_items`)
- **Primary Key**: `id` (BIGINT AUTO_INCREMENT)
- **Key Fields**: `order_id`, `product_id`, `variant_id`, `station_id`, `item_name`, `variant_name`, `quantity`, `unit_price`, `modifier_total`, `subtotal`, `status`, `routing_status`
- **Creation Source**: Waiter / POS (`OrderEngine::addItemToOrder`)
- **Persistence Mechanism**: Immutable line-item snapshot of product, variant, and modifier prices.

### 6. Kitchen Ticket (`order_tickets`)
- **Primary Key**: `id` (BIGINT AUTO_INCREMENT)
- **Key Fields**: `order_id`, `station_id`, `order_route_id`, `ticket_number`, `status` (`NEW`, `PREPARING`, `READY`, `SERVED`, `CANCELLED`), `priority`, `created_by`, `started_at`, `ready_at`, `completed_at`
- **Creation Source**: `RoutingEngine::routeOrder` triggered upon `OrderEngine::submitOrder`
- **Persistence Mechanism**: KDS operational record linking kitchen stations directly to parent order.

### 7. Payment (`payments`)
- **Primary Key**: `id` (BIGINT AUTO_INCREMENT)
- **Key Fields**: `payment_number`, `branch_id`, `order_id`, `dining_session_id`, `payment_method_id`, `amount`, `currency`, `transaction_reference`, `received_by_user_id`, `status`, `idempotency_key`
- **Creation Source**: Reception / Cashier Desk (`PaymentEngine::processPayment`)
- **Persistence Mechanism**: Financial settlement record with split payment allocations and idempotency protection.

### 8. Waiter Commission (`commission_transactions`)
- **Primary Key**: `id` (BIGINT AUTO_INCREMENT)
- **Key Fields**: `branch_id`, `order_id`, `waiter_id`, `commission_rule_id`, `base_amount`, `rate`, `fixed_amount`, `commission_amount`, `status`, `idempotency_key`
- **Creation Source**: `CommissionEngine::processOrderCommission` (triggered on order payment)
- **Persistence Mechanism**: Server-calculated waiter sales commission with batch approval capabilities.

### 9. Inventory Transaction (`inventory_transactions`)
- **Primary Key**: `id` (BIGINT AUTO_INCREMENT)
- **Key Fields**: `branch_id`, `ingredient_id`, `transaction_type` (`CONSUMPTION`, `PURCHASE`, `ADJUSTMENT`), `quantity`, `unit_cost`, `reference_type`, `reference_id`
- **Creation Source**: `InventoryEngine::consumeForTicket` (triggered when kitchen ticket is completed)
- **Persistence Mechanism**: Recipe-based stock deduction and inventory tracking.

### 10. Audit Log (`audit_logs`)
- **Primary Key**: `id` (BIGINT AUTO_INCREMENT)
- **Key Fields**: `user_id`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`
- **Creation Source**: `AuditLogger::log()` across all core business engines
- **Persistence Mechanism**: Immutable operational event log for non-repudiation and compliance.
