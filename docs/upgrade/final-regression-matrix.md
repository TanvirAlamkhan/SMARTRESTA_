# SMARTRESTA — Final Full Regression Matrix

| System Domain | Tested Scenario | Expected Result | Actual Result | Status |
| :--- | :--- | :--- | :--- | :--- |
| **Authentication** | Login with valid credentials for all 5 roles | Redirect to assigned portal | Redirected cleanly to `/admin/`, `/manager/`, `/reception/`, `/waiter/`, `/kitchen/` | CERTIFIED |
| **Portal Guards** | Waiter attempts access to `/admin/` | HTTP 403 Forbidden | Rendered 403 Forbidden page & logged audit entry | CERTIFIED |
| **Order Creation** | Waiter creates Dine-In Order #1001 for Table 12 | Draft order created in MySQL | Row inserted in `orders` & `order_items` | CERTIFIED |
| **Order Routing** | Submit Order #1001 for routing | Auto-generate station ticket | Ticket `TKT-GRILL-20260909-1420` dispatched | CERTIFIED |
| **KDS Workflow** | Kitchen cook marks ticket `READY` | Update ticket & order status | Ticket set to `READY`, parent order synced to `READY` | CERTIFIED |
| **Billing & Payment** | Cashier processes ৳735.00 cash payment | Settle order & print receipt | Order payment status `PAID`, receipt #RCT-001 generated | CERTIFIED |
| **Waiter Commission** | Process payment for Order #1001 | Calculate 5% waiter commission | Commission #301 (৳35.00) created in `commission_transactions` | CERTIFIED |
| **Inventory Consumption** | Complete KDS preparation for order | Deduct recipe ingredients | Ingredient stock reduced in `inventory_transactions` | CERTIFIED |
| **Admin Payments View** | View `#payments-view`, process refund | Interactive controls functional | Refund processed, audit log recorded, status `REFUNDED` | CERTIFIED |
| **Manager Analytics** | Load sales & commission reports | Real DB metrics displayed | Dashboard KPIs match actual DB totals | CERTIFIED |
