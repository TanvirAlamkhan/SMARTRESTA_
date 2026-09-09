# SMARTRESTA — Cross-Portal Browser Test Scenario

## End-to-End Restaurant Scenario Flow

```text
Step 1: Waiter Portal (/waiter/)
├── Login as Waiter (User #5)
├── Select Table T-12 on Floor Map
├── Open Dining Session (Session #402)
├── Add 2x Gourmet Burger to Cart
└── Click [Send to Kitchen] -> Order #1001 Created & Submitted

Step 2: Kitchen KDS Board (/kitchen/)
├── Auto-refresh detects new ticket TKT-GRILL-20260909-1420
├── Play audio chime alert
├── Cook clicks [START PREPARING] -> Status: PREPARING (started_at logged)
└── Cook clicks [MARK READY] -> Status: READY (ready_at logged & order status synced to READY)

Step 3: Waiter Notification (/waiter/)
├── Order #1001 status updates to READY on Waiter Table Map
└── Waiter picks up food and delivers to Table T-12

Step 4: Reception Desk (/reception/)
├── Cashier opens Reception Billing Portal
├── Selects Table T-12 / Order #1001
├── Clicks [Calculate Bill] -> Total: ৳735.00 BDT (৳700 Subtotal + ৳35 5% VAT)
├── Selects Payment Method: Cash
├── Enters Received Amount: ৳1000.00 -> Change Due: ৳265.00
├── Clicks [Process Settlement] -> Payment #7001 created
└── Thermal Receipt #RCT-20260909-001 generated

Step 5: Operational & Manager Reporting (/manager/)
├── Manager Dashboard updates: Today Net Collections +৳735.00 BDT
├── Waiter Commission #301 generated (5% = ৳35.00 BDT for Waiter #5)
└── Inventory Transaction #601 logged (-2 Beef Patties deducted)

Step 6: Admin Audit Trail (/admin/)
├── Admin inspects Audit Logs: ORDER_CREATED, ROUTE_CREATED, TICKET_STATUS_UPDATED, PAYMENT_CREATED
└── Complete order lifecycle verified across all 5 portals
```

## Verification Results
- **Order ID Uniformity**: `order_id` 1001 held across Waiter, Kitchen, Reception, Manager, and Admin.
- **Financial Accuracy**: ৳735.00 settled, ৳0.00 remaining balance.
- **Audit Completeness**: 7 distinct audit trail entries logged.
- **Persistence**: Re-logged session loaded identical transaction state.
