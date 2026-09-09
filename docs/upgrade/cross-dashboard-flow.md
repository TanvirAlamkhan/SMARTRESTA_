# SMARTRESTA — Cross-Dashboard Connection & Order Lifecycle Matrix

## 1. End-to-End Operational Lifecycle Trace

The diagram below maps the complete execution lifecycle of a customer order across all five operational roles:

```text
  [ WAITER PORTAL ]
  1. Waiter selects Table 12 -> opens Dining Session
  2. Selects 2x Ribeye Steak, 1x Coke (with special instruction "Medium Rare")
  3. Clicks "Submit & Route Order"
       │
       ▼ (POST /api/v1/orders/create.php)
  [ DATABASE ]
  - Row inserted in `orders` (status: 'SUBMITTED', total: ৳3,450.00)
  - Rows inserted in `order_items`
  - Item dispatch triggered via RoutingEngine -> `order_tickets` created
       │
       ├─────────────────────────────────────────┐
       ▼                                         ▼
  [ KITCHEN PORTAL ]                        [ RECEPTION PORTAL ]
  - KDS displays ticket under NEW          - Table 12 shown as OCCUPIED
  - Chef clicks "Start Prep" (PREPARING)   - Live order total visible
  - Chef clicks "Mark Ready" (READY)       - Customer requests bill
  - Waiter receives notification                 │
       │                                         │
       └────────────────────┬────────────────────┘
                            │
                            ▼ (POST /api/v1/payments/pay.php)
                      [ RECEPTION PORTAL ]
                      - Cashier receives ৳3,450 cash
                      - Payment recorded in `payments` (status: 'COMPLETED')
                      - Order status updated to 'COMPLETED'
                      - Table 12 set to 'CLEANING' / 'AVAILABLE'
                      - Receipt printed via ReceiptEngine
                            │
                            ├─────────────────────────────────────────┐
                            ▼                                         ▼
                      [ MANAGER PORTAL ]                       [ ADMIN PORTAL ]
                      - Live sales KPI increases by ৳3,450     - Full audit log recorded
                      - Waiter commission generated (5%)        - Inventory auto-deducted
                      - Shift register updated                  - Financial summary updated
```

---

## 2. Shared Business Entity Tracing

| Business Entity | Waiter | Kitchen | Reception | Manager | Admin |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Dining Session** | Opens session | View table context | Closes session | Monitor sessions | Full override |
| **Order Items** | Selects & submits | Cooking status updates | Bill itemization | Sales breakdown | Audit log inspection |
| **Station Tickets** | Receives ready alert | Manages Kanban pipeline | Views pickup status | Monitors lead time | Station config |
| **Payment Record** | View unpaid bill | N/A | Processes settlement | Sales reconciliation | Profit & Loss engine |
| **Stock Deductions** | Menu availability | Station usage | N/A | Waste audit | Purchasing & Stock |
