# SMARTRESTA — Admin API Connection Map

## 1. REST API Endpoints Consumed by Admin Portal

All endpoints require active session authentication (`Auth::requireAuth()`) and specific RBAC permission checks:

| API Endpoint | HTTP Method | Required Permission | Backend Handler / Engine Method | Response Data |
| :--- | :---: | :--- | :--- | :--- |
| `api/v1/dashboard/overview.php` | GET | `dashboard.view` | `ReportEngine::getDashboardOverview()` | KPIs (net sales, gross sales, AOV) & operational alerts |
| `api/v1/waiters/get_matrix.php` | GET | `commissions.view` | `CommissionEngine::getWaiterMatrix()` | Waiter sales totals, order counts & owed commissions |
| `api/v1/orders/index.php` | GET | `orders.view` | `OrderEngine::getActiveOrders()` | List of live active orders & payment status |
| `api/v1/reports/sales.php` | GET | `reports.sales` | `ReportEngine::getSalesReport()` | Sales analytics breakdown by period |
| `api/v1/reports/orders.php` | GET | `reports.orders` | `ReportEngine::getOrdersReport()` | Paginated order history list |
| `api/v1/reports/orders_drilldown.php` | GET | `reports.orders` | `ReportEngine::getDrilldownOrderDetails()` | Full order drilldown (items, KDS tickets, payments, inventory) |
| `api/v1/users/index.php` | GET / POST | `users.manage` | User CRUD Controller | User account management |
| `api/v1/products/index.php` | GET / POST | `menu.manage` | `MenuEngine` / Product Controller | Product catalog CRUD |
| `api/v1/inventory/stock.php` | GET / POST | `inventory.manage` | `InventoryEngine` | Stock balances & inventory adjustment transactions |
| `api/v1/finance/summary.php` | GET | `finance.view` | `FinanceEngine::getFinanceSummary()` | Finance summary, expenses, & shift closing |
