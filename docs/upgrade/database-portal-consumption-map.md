# SMARTRESTA — Database Portal Consumption Map

```text
                                           MYSQL DATABASE
┌─────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│  users <---> roles <---> permissions <---> role_permissions                                                │
│  branches <---> floors <---> restaurant_tables                                                              │
│  categories <---> products <---> product_variants <---> modifiers <---> product_modifiers                    │
│  dining_sessions <---> orders <---> order_items <---> order_item_modifiers                                 │
│  stations <---> order_routes <---> order_tickets <---> order_ticket_items <---> order_ticket_status_history │
│  payment_methods <---> payments <---> payment_allocations <---> receipts <---> refunds                      │
│  waiter_profiles <---> commission_rules <---> commission_transactions <---> commission_adjustments        │
│  customers <---> reservations <---> loyalty_transactions <---> coupons                                     │
│  ingredients <---> recipes <---> recipe_ingredients <---> suppliers <---> inventory_transactions            │
│  shifts <---> expense_categories <---> expenses <---> day_closings                                          │
│  audit_logs <---> settings                                                                                  │
└─────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
          │                       │                       │                       │                       │
          ▼                       ▼                       ▼                       ▼                       ▼
┌──────────────────┐    ┌──────────────────┐    ┌──────────────────┐    ┌──────────────────┐    ┌──────────────────┐
│   ADMIN PORTAL   │    │  MANAGER PORTAL  │    │ RECEPTION PORTAL │    │  WAITER PORTAL   │    │  KITCHEN PORTAL  │
│    (/admin/)     │    │   (/manager/)    │    │   (/reception/)  │    │    (/waiter/)    │    │    (/kitchen/)   │
└──────────────────┘    └──────────────────┘    └──────────────────┘    └──────────────────┘    └──────────────────┘
```

## Table Consumption Details

| MySQL Table | Authoritative Writer | Consuming Portals | Key Purpose |
| :--- | :--- | :--- | :--- |
| `users` | Admin | All 5 Portals | Authentication, staff identification, authorization. |
| `branches` | Admin | All 5 Portals | Multi-tenant isolation boundary. |
| `restaurant_tables` | Admin / Manager | Waiter, Reception, Manager, Admin | Floor map layout and dining session binding. |
| `dining_sessions` | Waiter / Reception | Waiter, Reception, Manager, Admin | Table experience tracking and multi-order grouping. |
| `products` | Admin / Manager | Waiter, Reception, Manager, Admin | Restaurant menu catalog & price matrix. |
| `orders` | OrderEngine (Waiter/Reception) | All 5 Portals | Shared transaction header across all portals. |
| `order_items` | OrderEngine (Waiter/Reception) | All 5 Portals | Itemized product, variant, and modifier line items. |
| `order_tickets` | RoutingEngine / KDSEngine | Kitchen, Waiter, Reception, Manager, Admin | Station-specific production queue management. |
| `payments` | PaymentEngine (Reception) | Reception, Manager, Admin | Financial settlement and payment allocation. |
| `receipts` | ReceiptEngine (Reception) | Reception, Manager, Admin | Tax receipt records. |
| `commission_transactions` | CommissionEngine | Manager, Admin, Waiter | Waiter sales commission earnings & approvals. |
| `inventory_transactions` | InventoryEngine | Manager, Admin, Kitchen | Raw ingredient consumption and stock history. |
| `audit_logs` | AuditLogger (All Engines) | Manager, Admin | Immutable audit log of all system mutations. |
