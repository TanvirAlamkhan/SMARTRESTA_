# SMARTRESTA — Admin Dashboard Architecture (Prompt 03)

## 1. Overview & System Management Blueprint

The **Admin Portal** (`/admin/` / [admin/index.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/admin/index.php)) serves as the central management and real-time business intelligence dashboard of the SMARTRESTA platform.

It provides executive oversight across all restaurant operations:
- Executive KPIs (Net Sales, Gross Revenue, Orders Processed, Average Order Value, Paid Collections, Unpaid Balances, Waiter Commissions, Table Occupancy).
- Operational Real-Time Alerts (Low Stock Ingredients, Unpaid Orders, Delayed KDS Tickets, Pending Commission Approvals).
- Waiter Performance & Commission Matrix.
- Live Orders & Station Routing Engine.
- User Access Control & System Settings.

---

## 2. Component Hierarchy & Flow

```text
  [ ADMIN USER ]
        │
        ▼
  /admin/index.php
        │
        ▼
  Router::authorizePortal('admin') ───(If Unauthorized)───► 403 Forbidden Page
        │
        ▼ (If Authorized)
  [ ADMIN DASHBOARD VIEW ] (views/admin.php)
        │
        ├───────────────────────┼───────────────────────┐
        ▼                       ▼                       ▼
  GET /api/v1/dashboard/   GET /api/v1/waiters/    GET /api/v1/orders/
  overview.php             get_matrix.php          index.php
        │                       │                       │
        ▼                       ▼                       ▼
  ReportEngine.php         CommissionEngine.php     OrderEngine.php
        │                       │                       │
        └───────────────────────┼───────────────────────┘
                                │
                                ▼
                       [ MYSQL 8.x DATABASE ]
                     (smartresta_db 35 Tables)
```

---

## 3. Strict Real-Data Guarantee

No statistics or KPI numbers are hardcoded or simulated:
- Sales, orders, and payment figures are calculated on demand from `orders` and `payments` tables via [core/ReportEngine.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/core/ReportEngine.php).
- Operational alerts query active stock levels in `ingredients`, delayed tickets in `order_tickets`, and pending reviews in `commission_transactions`.
