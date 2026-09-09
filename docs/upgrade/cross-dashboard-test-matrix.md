# SMARTRESTA — Cross-Dashboard Integration Test Matrix

| Integration Scenario | Source Portal | Target Portal | Shared Identifiers | Underlying Engine / Mechanism | Result |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Order Dispatch to Kitchen** | Waiter (`/waiter/`) | Kitchen (`/kitchen/`) | `order_id`, `order_number` | `OrderEngine::submitOrder` -> `RoutingEngine::routeOrder` -> `order_tickets` | PASSED |
| **Kitchen Readiness Notification** | Kitchen (`/kitchen/`) | Waiter (`/waiter/`) | `order_id`, `ticket_id` | `KDSEngine::updateTicketStatus` -> `syncOrderReadiness` (`orders.order_status = READY`) | PASSED |
| **Front Desk Billing Retrieval** | Kitchen (`/kitchen/`) | Reception (`/reception/`) | `order_id`, `table_id` | `BillingEngine::calculateOrderBill` fetches live `orders` and `order_items` | PASSED |
| **Payment Settlement to Finance** | Reception (`/reception/`) | Manager (`/manager/`) | `payment_id`, `order_id` | `PaymentEngine::processPayment` -> `payments` -> `FinanceEngine` / `ReportEngine` | PASSED |
| **Revenue & Audit Transparency** | Reception (`/reception/`) | Admin (`/admin/`) | `order_id`, `payment_id` | `AuditLogger::log` & DB queries in `AdminEngine` | PASSED |
| **Recipe Ingredient Deduction** | Kitchen (`/kitchen/`) | Inventory (`/manager/`) | `ticket_id`, `product_id` | `KDSEngine` -> `InventoryEngine::consumeForTicket` -> `inventory_transactions` | PASSED |
| **Waiter Sales Commission** | Reception (`/reception/`) | Waiter (`/waiter/`) | `order_id`, `user_id` | `PaymentEngine` -> `CommissionEngine::processOrderCommission` -> `commission_transactions` | PASSED |
| **Double Submission Prevention** | Waiter (`/waiter/`) | System | `order_id` | Database transactions & state machine validation (`DRAFT` -> `SUBMITTED`) | PASSED |
| **Idempotent Payment Settlement** | Reception (`/reception/`) | System | `idempotency_key` | `PaymentEngine` idempotency check + overpayment guard balance checks | PASSED |
| **Multi-Tenant Branch Isolation** | All Portals | System | `branch_id` | Server-side session `branch_id` scoping across all SQL queries | PASSED |
