# SMARTRESTA Waiter Security & RBAC Audit

## 1. Authentication & Route Guards
- **Unauthenticated Access**: Direct HTTP GET requests to `/waiter/` or `/waiter/index.php` without active session cookies are intercepted by `Auth::requireAuth()` and redirected to `/landing.php`.
- **Unauthorized Role Access**: If a user logged in with a non-waiter role (e.g. `kitchen`) attempts to load `/waiter/`, `Router::authorizePortal('waiter')` renders standard 403 Forbidden.

## 2. API Authorization & IDOR Protection
- Every API endpoint called by Waiter Portal (`/api/v1/orders/`, `/api/v1/dining_sessions/`, `/api/v1/kds/`) calls `Auth::requireAuth()` and `Auth::requirePermission()`.
- **IDOR Protection**: Manipulating `table_id`, `session_id`, or `order_id` in API payloads is validated server-side. Orders or sessions belonging to unauthorized status or branches throw HTTP 422/403 validation errors.

## 3. Pricing & Calculation Integrity
- Item prices, subtotal, VAT (5%), and totals are derived deterministically on the server via `MenuEngine::calculateEffectivePrice()` and `OrderEngine::recalculateOrderTotals()`. Browser payload price manipulations are completely ignored.

## 4. Input Sanitization & Prepared Statements
- All database queries use PDO prepared statements with parameter binding to prevent SQL injection.
- XSS prevention is enforced via `htmlspecialchars()` escaping on all dynamic HTML outputs.
