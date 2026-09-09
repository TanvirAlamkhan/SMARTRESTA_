# SMARTRESTA — Cross-Dashboard Shared API Map

| API Endpoint | HTTP Method | Business Engine | Primary Portal Callers | Required Permission | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `/api/v1/orders/index.php` | `GET` / `POST` | `OrderEngine` | Waiter, Reception, Manager, Admin | `orders.view` / `orders.create` | List orders with filters or create new draft order. |
| `/api/v1/orders/items.php` | `POST` / `PUT` / `DELETE` | `OrderEngine` | Waiter, Reception | `orders.update` | Add item with variant/modifiers, update quantity, remove item. |
| `/api/v1/orders/submit.php` | `POST` | `OrderEngine`, `RoutingEngine` | Waiter, Reception | `orders.submit` | Submit draft order and automatically dispatch station tickets. |
| `/api/v1/kds/tickets.php` | `GET` / `POST` | `KDSEngine` | Kitchen, Manager | `kds.view` / `kds.manage` | Fetch station ticket queue and execute status transitions. |
| `/api/v1/billing/index.php` | `GET` | `BillingEngine` | Reception, Waiter, Manager | `billing.view` | Calculate server-authoritative bill breakdown and balance. |
| `/api/v1/payments/index.php` | `GET` / `POST` | `PaymentEngine` | Reception, Manager, Admin | `payments.view` / `payments.create` | List payment history or process single/split payments. |
| `/api/v1/dining_sessions/index.php` | `GET` / `POST` | `DiningSessionEngine` | Waiter, Reception | `tables.view` / `tables.manage` | Manage dining sessions, open session, close session. |
| `/api/v1/commissions/index.php` | `GET` / `POST` | `CommissionEngine` | Manager, Admin, Waiter | `commissions.view` / `commissions.approve` | Review waiter commissions and process batch approvals. |
| `/api/v1/inventory/index.php` | `GET` / `POST` | `InventoryEngine` | Manager, Admin, Kitchen | `inventory.view` / `inventory.manage` | View ingredient stock levels and record inventory transactions. |
| `/api/v1/reports/index.php` | `GET` | `ReportEngine` | Manager, Admin | `reports.view` | Fetch real-time operational analytics and revenue summaries. |
