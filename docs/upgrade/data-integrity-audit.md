# SMARTRESTA — Data Integrity & Audit Report

## Data Integrity Audit Summary

| Integrity Check Category | Verification Procedure | Finding & Controls | Status |
| :--- | :--- | :--- | :--- |
| **MySQL Source of Truth** | Audit all 5 portals to ensure no client-side local database dependency. | All business operations issue AJAX/Fetch API calls reading & writing directly to MySQL via core PHP engines. | PASSED |
| **Single Shared Order Lifecycle** | Verify Waiter, Kitchen, Reception, Manager, and Admin reference same order ID. | Shared `$orderId` and `$orderNumber` in `orders` table. No portal-specific duplicate order records created. | PASSED |
| **Server-Side Price Validation** | Test client price payload override attempts. | `OrderEngine` calculates effective line item prices via `MenuEngine::calculateEffectivePrice` using DB records. | PASSED |
| **Overpayment Protection** | Submit payment exceeding remaining order balance. | `PaymentEngine::processPayment` checks outstanding balance and rejects overpayment requests. | PASSED |
| **Payment Idempotency** | Submit identical idempotency key on payment POST. | `PaymentEngine` returns existing payment transaction record without duplicating charges. | PASSED |
| **Multi-Table Atomicity** | Test rollback on partial order / payment failure. | Database `beginTransaction()` and `rollBack()` wrapping all multi-table mutations (`OrderEngine`, `KDSEngine`, `PaymentEngine`). | PASSED |
| **Branch Scope Security** | Attempt accessing foreign branch records. | All core engine queries filter by `branch_id = $_SESSION['branch_id']`. Foreign IDs return 403/404. | PASSED |
| **Orphan Record Prevention** | Check database for foreign key mismatches. | All order items, routes, tickets, status history, payments, and commissions hold valid `order_id` references. | PASSED |
| **Refresh & Session Survival** | Create records, refresh browser, logout & login. | All created orders, tickets, payments, and sessions persist in MySQL and reload cleanly upon re-login. | PASSED |
| **Audit Trail Non-Repudiation** | Mutate financial & operational records. | `AuditLogger::log()` creates immutable entries in `audit_logs` capturing user ID, action, module, record ID, IP address, and payload diffs. | PASSED |
