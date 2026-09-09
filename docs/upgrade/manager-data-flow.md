# SMARTRESTA — Manager Data Flow & Cross-Portal Traceability

## 1. Operational Lifecycle Traceability

```text
  [ WAITER PORTAL ]
  1. Waiter submits Order #1001 for Table 4
        │
        ▼ (POST /api/v1/orders/create.php)
  [ DATABASE ]
  - Order #1001 inserted into `orders`
  - Kitchen ticket inserted into `order_tickets`
        │
        ├─────────────────────────────────────────┐
        ▼                                         ▼
  [ KITCHEN PORTAL ]                        [ MANAGER PORTAL ]
  - Chef moves ticket to PREPARING         - Manager views Order #1001 on Live Orders
  - KDS updates `order_tickets` status     - Manager monitors Kitchen SLA timers
        │                                  - Waiter matrix updates sales volume
        │                                         │
        └────────────────────┬────────────────────┘
                             │
                             ▼ (POST /api/v1/payments/pay.php)
                       [ RECEPTION PORTAL ]
                       - Cashier collects bill payment
                       - Payment inserted into `payments` (status: 'COMPLETED')
                             │
                             ▼
                       [ MANAGER PORTAL ]
                       - Live sales KPI increases
                       - Waiter commission generated
                       - Shift register balances update
```

---

## 2. Manager vs Admin Operational Boundary

| Aspect | Manager Portal (`/manager/`) | Admin Portal (`/admin/`) |
| :--- | :--- | :--- |
| **Primary Goal** | Operational execution, live shift performance, staff oversight | Global system configuration, user RBAC, root settings |
| **Order Control** | Monitor live status, review drilldowns, void approvals | Full order history, deletion overrides, raw SQL audit |
| **Staff Control** | Review shift activity, approve waiter commissions | Full User CRUD, role assignment, password resets |
| **System Settings**| Read-only operational settings | Complete system settings, branch & station setup |
