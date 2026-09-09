# SMARTRESTA — Portal Security & Session Hardening Spec

## 1. Security Architecture & Threat Mitigation

| Security Risk / Attack Vector | Mitigation Strategy | Implementation Location |
| :--- | :--- | :--- |
| **Role Spoofing (`?role=admin`)** | Server reads user role strictly from encrypted PHP session (`$_SESSION['user_role']`), ignoring URL params/POST bodies. | [core/Router.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/core/Router.php#L63) |
| **Unauthenticated Direct Portal Access** | All portal entrypoints execute `Router::authorizePortal()`, which invokes `Auth::requireAuth()`. | [core/Router.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/core/Router.php#L64) |
| **Cross-Portal Elevation (Waiter -> `/admin/`)** | `Router::authorizePortal('admin')` validates role on server, rejects unauthorized requests with HTTP 403. | [core/Router.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/core/Router.php#L85) |
| **Session Fixation** | Session ID is regenerated (`session_regenerate_id(true)`) upon successful authentication. | [core/Auth.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/core/Auth.php#L85) |
| **Session Inactivity Exposure** | Enforces 2-hour max inactivity timeout (`$_SESSION['last_activity']`). Automatically logs out expired sessions. | [core/Auth.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/core/Auth.php#L30) |
| **Direct API Bypassing** | All `/api/v1/` endpoints enforce independent permission checks via `Auth::requirePermission($perm)`. | `api/v1/*/*.php` |
| **Unauthorized Access Audit** | All 403 Forbidden incidents log audit events to `audit_logs` table via `AuditLogger::log()`. | [core/Router.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/core/Router.php#L80) |

---

## 2. HttpOnly & SameSite Cookie Directives

SMARTRESTA enforces production cookie flags in `Auth::initSession()`:
- `session.cookie_httponly = 1` (Prevents XSS cookie theft)
- `session.cookie_samesite = 'Lax'` (Mitigates CSRF vulnerabilities)
- `session.use_only_cookies = 1` (Disallows URL session ID passing)
