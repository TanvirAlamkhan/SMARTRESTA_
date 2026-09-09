# SMARTRESTA — Cross-Dashboard Security & Concurrency Audit

## Security & Concurrency Control Summary

| Security Vector | Audit Requirement | Implementation Mechanism | Status |
| :--- | :--- | :--- | :--- |
| **Server-Side Portal Access** | Protect `/admin/`, `/manager/`, `/reception/`, `/waiter/`, `/kitchen/` | `Router::authorizePortal($portal)` enforces authenticated session & RBAC role permission matching. | VERIFIED |
| **Direct API Route Protection** | Prevent unauthorized API invocation | `Auth::requireAuth()` and `Auth::requirePermission($perm)` on all REST endpoints in `/api/v1/`. | VERIFIED |
| **Multi-Tenant Branch Isolation** | Prevent cross-branch data leaks | All backend queries enforce `branch_id = $_SESSION['branch_id']`. Rejects IDOR access to external branch IDs. | VERIFIED |
| **Station-Level IDOR Guard** | Restrict ticket actions to assigned station | `KDSEngine` validates user station permissions before updating station ticket states. | VERIFIED |
| **Concurrency & Race Conditions** | Prevent double status transitions or overpayments | `SELECT ... FOR UPDATE` row locks in `KDSEngine::updateTicketStatus` and `PaymentEngine::processPayment`. | VERIFIED |
| **Idempotent Payment Processing** | Prevent duplicate charge submissions | `PaymentEngine` validates `idempotency_key` and rejects duplicate payments exceeding outstanding balance. | VERIFIED |
| **Input Validation & SQLi** | Protect database from injection attacks | Parameterized queries with PDO prepared statements across all core engines (`OrderEngine`, `KDSEngine`, `PaymentEngine`). | VERIFIED |
| **Output Sanitization & XSS** | Prevent script injection in UI views | All dynamic HTML output (order notes, customer names, product titles, table names) escaped via `htmlspecialchars()`. | VERIFIED |
| **CSRF Defense** | Protect state-changing HTTP requests | `CSRF::verifyToken()` mandatory on all POST/PUT/DELETE forms and AJAX requests. | VERIFIED |
| **Audit Log Integrity** | Non-repudiable audit logging | `AuditLogger::log()` records user ID, action, module, record ID, IP address, and payload diffs for all financial & state mutations. | VERIFIED |
