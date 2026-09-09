# SMARTRESTA — Final Security & Access Control Audit

## Hardened Security Architecture

| Security Category | Control Mechanism | Implementation | Status |
| :--- | :--- | :--- | :--- |
| **Authentication** | Password Hashing | `PASSWORD_BCRYPT` with cost factor 12 in `Auth::register` and `Auth::login`. | CERTIFIED |
| **Brute-Force Defense** | Login Rate Limiting | `RateLimiter::check` enforces max 5 failed attempts per 15 minutes per IP/identifier. | CERTIFIED |
| **Portal Guarding** | Server-Side HTTP 403 Guards | `Router::authorizePortal($portal)` enforces strict role permissions before view rendering. | CERTIFIED |
| **API Authorization** | REST Route Protection | `Auth::requireAuth()` and `Auth::requirePermission($perm)` on all `/api/v1/` endpoints. | CERTIFIED |
| **Multi-Tenant Scoping** | Branch Isolation | All queries enforce `branch_id = $_SESSION['branch_id']`. Rejects cross-tenant IDOR access. | CERTIFIED |
| **SQLi Protection** | Prepared Statements | PDO parameter binding used exclusively across all database engines (`DB::fetch`, `DB::insert`). | CERTIFIED |
| **XSS Defense** | Output Encoding | HTML escaping via `htmlspecialchars($var, ENT_QUOTES, 'UTF-8')` across all PHP templates and JS renderings. | CERTIFIED |
| **CSRF Security** | Session Token Verification | `CSRF::verifyToken()` mandatory for all POST/PUT/DELETE forms and AJAX payloads. | CERTIFIED |
| **Audit Trail** | Non-Repudiable Logs | `AuditLogger::log()` records user ID, action, module, record ID, IP, and diff payload in `audit_logs`. | CERTIFIED |
| **Error Exposure** | Sensitive Data Redaction | `APP_DEBUG=false` hides PHP stack traces, database credentials, and internal paths in production. | CERTIFIED |
