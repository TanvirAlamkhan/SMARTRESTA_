# SMARTRESTA Upgrade — Portal Routing & Security Verification Log

## Security Verification Overview

This document records the security audit and server-side RBAC verification conducted to ensure unauthorized direct URL access across all portal boundaries returns HTTP 403 Forbidden with proper audit logging.

---

## 1. Direct Cross-Portal Access Security Matrix

| Authenticated User Role | Target Portal URL Attempted | Server-Side HTTP Response Code | Audit Log Event Generated | Access Granted / Blocked |
| :--- | :--- | :---: | :--- | :---: |
| **Unauthenticated Guest** | `http://localhost/admin/` | 302 Redirect to `public/login.php` | `UNAUTHENTICATED_ACCESS` | **BLOCKED** |
| **Unauthenticated Guest** | `http://localhost/manager/` | 302 Redirect to `public/login.php` | `UNAUTHENTICATED_ACCESS` | **BLOCKED** |
| **Unauthenticated Guest** | `http://localhost/reception/` | 302 Redirect to `public/login.php` | `UNAUTHENTICATED_ACCESS` | **BLOCKED** |
| **Unauthenticated Guest** | `http://localhost/waiter/` | 302 Redirect to `public/login.php` | `UNAUTHENTICATED_ACCESS` | **BLOCKED** |
| **Unauthenticated Guest** | `http://localhost/kitchen/` | 302 Redirect to `public/login.php` | `UNAUTHENTICATED_ACCESS` | **BLOCKED** |
| **Waiter** | `http://localhost/admin/` | **403 Forbidden** | `UNAUTHORIZED_PORTAL_ACCESS` | **BLOCKED** |
| **Waiter** | `http://localhost/manager/` | **403 Forbidden** | `UNAUTHORIZED_PORTAL_ACCESS` | **BLOCKED** |
| **Kitchen Staff** | `http://localhost/admin/` | **403 Forbidden** | `UNAUTHORIZED_PORTAL_ACCESS` | **BLOCKED** |
| **Kitchen Staff** | `http://localhost/manager/` | **403 Forbidden** | `UNAUTHORIZED_PORTAL_ACCESS` | **BLOCKED** |
| **Receptionist** | `http://localhost/admin/` | **403 Forbidden** | `UNAUTHORIZED_PORTAL_ACCESS` | **BLOCKED** |
| **Receptionist** | `http://localhost/manager/` | **403 Forbidden** | `UNAUTHORIZED_PORTAL_ACCESS` | **BLOCKED** |
| **Manager** | `http://localhost/admin/` | **403 Forbidden** | `UNAUTHORIZED_PORTAL_ACCESS` | **BLOCKED** |
| **Manager** | `http://localhost/waiter/` | **200 OK** | `PORTAL_ACCESS` | **AUTHORIZED** |
| **System Admin** | `http://localhost/admin/` | **200 OK** | `PORTAL_ACCESS` | **AUTHORIZED** |
| **System Admin** | `http://localhost/manager/` | **200 OK** | `PORTAL_ACCESS` | **AUTHORIZED** |
| **System Admin** | `http://localhost/reception/` | **200 OK** | `PORTAL_ACCESS` | **AUTHORIZED** |
| **System Admin** | `http://localhost/waiter/` | **200 OK** | `PORTAL_ACCESS` | **AUTHORIZED** |
| **System Admin** | `http://localhost/kitchen/` | **200 OK** | `PORTAL_ACCESS` | **AUTHORIZED** |

---

## 2. Technical Safeguards Verified

1. **Server-Side Enforcement**: All routing guards are executed in PHP via `Router::authorizePortal($portal)` before HTML parsing or view inclusion occurs.
2. **Audit Logging**: Any unauthorized portal access attempt writes a security event record to MySQL `audit_logs` containing `user_id`, `user_role`, `requested_portal`, `ip_address`, and timestamp.
3. **HTTP 403 Standard Response**: Unauthorized requests receive a dedicated HTML 403 Forbidden card with a "Return to My Portal" navigation trigger, or a JSON `{ success: false, status: 403 }` object if requested via AJAX.
