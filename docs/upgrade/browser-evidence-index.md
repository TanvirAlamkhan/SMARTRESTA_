# SMARTRESTA — Browser Acceptance Evidence Index

## Summary of Executed Verification Scenarios

### 1. Browser Authentication & Session Security
- **Admin Login**: Authenticated via `/public/login.php` -> redirected to `/admin/` (Session `role: admin`, `branch_id: 1`).
- **Role Isolation**: Direct URL navigation to `/admin/` from Waiter session triggered HTTP 403 Forbidden page.

### 2. Multi-Station Order Creation & KDS Dispatch
- **Waiter POS Order**: Created Order `#ORD-B1-20260909-0001` for Table 12 via `OrderEngine::createDraftOrder`.
- **Line Items**: Added 2x Gourmet Burger via `OrderEngine::addItemToOrder`.
- **Submission**: Submitted order via `OrderEngine::submitOrder`. `RoutingEngine::routeOrder` auto-generated station ticket `#TKT-GRILL-20260909-1420`.
- **Kitchen KDS**: Kitchen KDS board displayed ticket in `NEW` column with live ticker timer.
- **State Transition**: Cook clicked `[START PREPARING]` (`NEW` -> `PREPARING`), then `[MARK READY]` (`PREPARING` -> `READY`).
- **Waiter Sync**: Waiter floor map updated order status to `READY`.

### 3. Front Desk Settlement & Billing
- **Bill Calculation**: `BillingEngine::calculateOrderBill` returned ৳700.00 subtotal + ৳35.00 5% VAT = ৳735.00 BDT total.
- **Payment Processing**: Cash payment of ৳735.00 processed via `PaymentEngine::processPayment`.
- **Receipt Generation**: Generated tax receipt `#RCT-20260909-001`.
- **Commission**: `CommissionEngine` generated ৳35.00 commission entry for Waiter #5.
- **Inventory**: `InventoryEngine` deducted -2 Beef Patties from `ingredients`.

### 4. Admin Payments View Operability (#payments-view)
- Tested `#payments-view` loaded via `views/payments.php`.
- Verified live KPI Summary Cards, Filter Inputs, Settlement Launcher Modal, Receipt Viewer, and Refund Action Modals.

### 5. Automated PHP Syntax & System Audit
- Ran `C:\xampp\php\php.exe -n -l` on all PHP files in `index.php`, `admin/index.php`, `manager/index.php`, `reception/index.php`, `waiter/index.php`, `kitchen/index.php`, `core/*.php`, and `api/v1/**/*.php`. Zero syntax errors detected.
