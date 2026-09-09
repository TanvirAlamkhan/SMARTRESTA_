# SMARTRESTA — Complete API Endpoint & Connection Map

## 1. REST API Endpoint Audit (v1 Endpoints)

All API endpoints reside under `api/v1/` and return JSON via [core/Response.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/core/Response.php) and [helpers/response.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/helpers/response.php).

### Authentication & RBAC (`api/v1/auth/`)
- `POST /api/v1/auth/login.php` -> Authenticates credentials, sets session, returns user object & role.
- `POST /api/v1/auth/logout.php` -> Destroys PHP session.
- `GET /api/v1/auth/me.php` -> Returns current active user profile and permissions.

### Orders & Waiter POS (`api/v1/orders/`)
- `POST /api/v1/orders/create.php` -> Calls [OrderEngine::createOrder()](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/core/OrderEngine.php#L15), calculates totals, triggers `RoutingEngine::routeOrder()`.
- `GET /api/v1/orders/index.php` -> Lists active orders by branch/table/status.
- `GET /api/v1/orders/detail.php?id={id}` -> Retrieves order items, modifiers, status history.
- `POST /api/v1/orders/status.php` -> Updates order status (e.g. `SERVED`, `CANCELLED`).

### Kitchen Display System (`api/v1/kds/`)
- `GET /api/v1/kds/tickets.php` -> Calls [KDSEngine::getActiveTickets()](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/core/KDSEngine.php#L18) for kitchen station.
- `POST /api/v1/kds/status.php` -> Calls [KDSEngine::updateTicketStatus()](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/core/KDSEngine.php#L65) (`NEW` -> `PREPARING` -> `READY` -> `SERVED`).
- `POST /api/v1/kds/recall.php` -> Recalls tickets back to previous kitchen state.

### Dining Sessions & Tables (`api/v1/dining_sessions/`, `api/v1/tables/`)
- `POST /api/v1/dining_sessions/open.php` -> Opens table dining session.
- `POST /api/v1/dining_sessions/close.php` -> Closes session after bill settlement.
- `GET /api/v1/tables/index.php` -> Fetches table status grid (AVAILABLE, OCCUPIED, RESERVED, WAITING_PAYMENT).

### Payments & Billing (`api/v1/payments/`, `api/v1/billing/`, `api/v1/receipts/`)
- `POST /api/v1/payments/pay.php` -> Calls [PaymentEngine::processPayment()](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/core/PaymentEngine.php#L16), records cash/bKash/Nagad payment.
- `GET /api/v1/payments/index.php` -> Lists payment history log.
- `GET /api/v1/receipts/print.php?order_id={id}` -> Generates printable HTML receipt via [ReceiptEngine](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/core/ReceiptEngine.php).
- `POST /api/v1/refunds/process.php` -> Calls [RefundEngine::processRefund()](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/core/RefundEngine.php).

### Waiter Commissions (`api/v1/commissions/`, `api/v1/commission_rules/`)
- `GET /api/v1/commissions/index.php` -> Fetches commission log.
- `POST /api/v1/commissions/approve.php` -> Manager approves waiter commission.
- `POST /api/v1/commissions/payout.php` -> Settles waiter commission payout via [PayoutEngine](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/core/PayoutEngine.php).

### Inventory & Purchasing (`api/v1/inventory/`, `api/v1/ingredients/`, `api/v1/recipes/`)
- `GET /api/v1/inventory/stock.php` -> Lists current stock levels vs min threshold.
- `POST /api/v1/inventory/adjust.php` -> Records stock adjustment/wastage.
- `POST /api/v1/purchases/create.php` -> Records supplier ingredient purchases.

### Finance, Shifts & Reports (`api/v1/finance/`, `api/v1/shifts/`, `api/v1/reports/`)
- `GET /api/v1/finance/summary.php` -> KPI overview (sales, expenses, net profit).
- `POST /api/v1/day_closing/close.php` -> Triggers daily register close via [FinanceEngine](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/core/FinanceEngine.php).
- `GET /api/v1/reports/sales.php` -> Fetches sales analytics chart data.

### CRM & Reservations (`api/v1/crm/`)
- `POST /api/v1/crm/customers/create.php` -> Registers customer.
- `POST /api/v1/crm/reservations/create.php` -> Creates table reservation.
