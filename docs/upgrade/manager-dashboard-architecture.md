# SMARTRESTA — Manager Dashboard Architecture (Prompt 04)

## 1. Overview & Operational Intelligence Blueprint

The **Manager Portal** (`/manager/` / [manager/index.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/manager/index.php)) serves as the daily operational control and performance management interface of the SMARTRESTA platform.

Unlike the Admin Portal (which manages global system configurations, user RBAC, and root settings), the Manager Portal focuses on real-time operational execution:
- **Live Operations Monitoring**: Active orders, table occupancy, kitchen KDS lead times, and ready ticket dispatches.
- **Staff & Waiter Performance**: Shift monitoring, waiter sales volume, and commission review/approvals.
- **Inventory & Stock Oversight**: Low-stock ingredient alerts, wastage logs, and purchasing summaries.
- **Financial & Shift Control**: Shift register balances, daily expense tracking, and day-closing reviews.

---

## 2. Manager Component Architecture & Engine Reuse

```text
  [ MANAGER USER ]
        │
        ▼
  /manager/index.php
        │
        ▼
  Router::authorizePortal('manager') ───(If Unauthorized)───► 403 Forbidden Page
        │
        ▼ (If Authorized)
  [ MANAGER DASHBOARD VIEW ] (views/admin.php / views/reports.php)
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

## 3. Shared Database & Single-Record Guarantee

The Manager Portal communicates directly with the primary MySQL database (`smartresta_db`) through the shared REST API layer (`api/v1/*`) and core business engines (`OrderEngine.php`, `KDSEngine.php`, `PaymentEngine.php`, `FinanceEngine.php`, etc.).

No isolated or duplicate order records are created. Order #1001 created by a Waiter is the exact same database row retrieved by the Manager for operational oversight.
