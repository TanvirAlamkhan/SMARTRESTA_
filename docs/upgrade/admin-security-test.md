# SMARTRESTA — Admin Portal Security Test Matrix

## 1. Security Test Execution Log

The following security tests were performed against the Admin Portal architecture:

| Test Case ID | Security Domain | Description / Payload | Expected Behavior | Actual Result | Status |
| :--- | :--- | :--- | :--- | :--- | :---: |
| **SEC-ADM-01** | Direct URL Authorization | Non-admin user (Waiter) visits `/admin/` directly | Server blocks request with HTTP 403 Forbidden page | Access Denied 403 Page Rendered | `PASS` |
| **SEC-ADM-02** | Unauthenticated Access | Guest user visits `/admin/` | Server redirects to login page | Redirected to `public/login.php` | `PASS` |
| **SEC-ADM-03** | Role Spoofing | Request with `?role=admin` via Waiter session | Server inspects `$_SESSION['user_role']`, rejects access | Request Rejected (403) | `PASS` |
| **SEC-ADM-04** | API Endpoint Authorization | Waiter calls `GET /api/v1/dashboard/overview.php` | Server checks `Auth::requirePermission('dashboard.view')` | Returns HTTP 403 JSON | `PASS` |
| **SEC-ADM-05** | Environment Secret Privacy | Admin inspects dashboard DOM / API responses | Zero exposure of `.env` secrets or raw DB passwords | Secrets 100% Redacted | `PASS` |
| **SEC-ADM-06** | SQL Injection Safety | Date filter with `' OR 1=1 --` payload | Prepared statements sanitize input safely | Sanitized Query Executed | `PASS` |
| **SEC-ADM-07** | Audit Trail Integrity | Admin performs system action | Event recorded in `audit_logs` table | Logged in `audit_logs` | `PASS` |
