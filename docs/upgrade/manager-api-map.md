# SMARTRESTA — Manager API Connection Map

## 1. REST API Endpoints Consumed by Manager Portal

All requests enforce server-side session authentication (`Auth::requireAuth()`) and RBAC permission verification:

| API Endpoint | HTTP Method | Required Permission | Backend Engine Method | Operational Data Delivered |
| :--- | :---: | :--- | :--- | :--- |
| `api/v1/dashboard/overview.php` | GET | `dashboard.view` | `ReportEngine::getDashboardOverview()` | Live operational KPIs & alerts |
| `api/v1/orders/index.php` | GET | `orders.view` | `OrderEngine::getActiveOrders()` | Active orders list & station badges |
| `api/v1/kds/tickets.php` | GET | `kds.view` | `KDSEngine::getActiveTickets()` | Station ticket prep status & SLA timers |
| `api/v1/waiters/get_matrix.php` | GET | `commissions.view` | `CommissionEngine::getWaiterMatrix()` | Waiter order counts, sales, & commissions |
| `api/v1/commissions/approve.php` | POST | `commissions.manage` | `CommissionEngine::approveCommission()` | Approve waiter commission transaction |
| `api/v1/inventory/stock.php` | GET | `inventory.view` | `InventoryEngine::getLowStockIngredients()` | Low stock ingredient alerts & valuation |
| `api/v1/finance/summary.php` | GET | `finance.view` | `FinanceEngine::getFinanceSummary()` | Sales vs expenses reconciliation |
| `api/v1/reports/orders_drilldown.php` | GET | `reports.orders` | `ReportEngine::getDrilldownOrderDetails()` | Full order item, ticket, & payment drilldown |
