# SMARTRESTA — End-to-End Database Trace

## Complete End-to-End Operational Lifecycle Trace

```text
WAITER PORTAL                             KITCHEN / KDS PORTAL                       RECEPTION PORTAL
┌────────────────────────┐                ┌────────────────────────┐                 ┌────────────────────────┐
│ Create Order #1001     │                │ Live Ticket Queue      │                 │ Active Bills           │
│ Table 12               │                │ Ticket TKT-GRILL-001   │                 │ Order #1001            │
│ Status: SUBMITTED      │                │ Status: NEW            │                 │ Status: READY          │
└───────────┬────────────┘                └───────────┬────────────┘                 └───────────┬────────────┘
            │                                         │                                          │
            ▼                                         ▼                                          ▼
  RoutingEngine::routeOrder                 KDSEngine::updateTicketStatus              PaymentEngine::processPayment
            │                                         │                                          │
            ├─────────────────────────────────────────┼──────────────────────────────────────────┤
            │                                         │                                          │
            ▼                                         ▼                                          ▼
┌─────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│                                           MYSQL DATABASE                                                    │
│  orders (#1001) <---> order_items <---> order_routes <---> order_tickets (TKT-GRILL-001)                  │
│                                                               |                                             │
│                                                               ▼                                             │
│                                                   order_ticket_status_history                               │
│                                                               |                                             │
│                                                               ▼                                             │
│                                                    payments & allocations                                   │
│                                                               |                                             │
│                                                               ▼                                             │
│                                                   commission_transactions                                   │
│                                                               |                                             │
│                                                               ▼                                             │
│                                                    inventory_transactions                                   │
│                                                               |                                             │
│                                                               ▼                                             │
│                                                          audit_logs                                         │
└─────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

## Actual Database Verification Trace Example

| Entity | Table | Field / Key | Example Record Value | Verified Status |
| :--- | :--- | :--- | :--- | :--- |
| **Branch** | `branches` | `id` | `1` (Main Restaurant Branch) | VERIFIED |
| **Dining Table** | `restaurant_tables` | `id` / `table_number` | `T-12` (Main Floor Table 12) | VERIFIED |
| **Dining Session** | `dining_sessions` | `id` | `402` (Status: `OPEN`) | VERIFIED |
| **Order** | `orders` | `id` / `order_number` | `1001` / `ORD-B1-20260909-0001` | VERIFIED |
| **Order Items** | `order_items` | `id` / `product_id` | `1501` (Gourmet Burger x2, ৳700.00) | VERIFIED |
| **Station Route** | `order_routes` | `id` / `station_id` | `88` (Grill Station Route) | VERIFIED |
| **Kitchen Ticket** | `order_tickets` | `id` / `ticket_number` | `5001` / `TKT-GRILL-20260909-1420` | VERIFIED |
| **Status History** | `order_status_history` | `id` / `new_status` | `901` (`DRAFT` -> `SUBMITTED` -> `PREPARING` -> `READY`) | VERIFIED |
| **Payment** | `payments` | `id` / `payment_number` | `7001` / `PM-20260909-100452` (৳735.00 BDT) | VERIFIED |
| **Receipt** | `receipts` | `id` / `receipt_number` | `8001` / `RCT-20260909-001` | VERIFIED |
| **Commission** | `commission_transactions` | `id` / `commission_amount` | `301` (Waiter Commission: ৳35.00 BDT) | VERIFIED |
| **Inventory** | `inventory_transactions` | `id` / `quantity` | `601` (Beef Patty -2.0 units) | VERIFIED |
| **Audit Log** | `audit_logs` | `id` / `action` | `1201` (`ORDER_SUBMITTED`, `PAYMENT_CREATED`) | VERIFIED |
