# SMARTRESTA — Manager Portal Security Test Matrix

## 1. Manager Security Execution Log

| Test Case ID | Domain | Description / Test Scenario | Expected Outcome | Actual Result | Status |
| :--- | :--- | :--- | :--- | :--- | :---: |
| **SEC-MGR-01** | Direct URL Authorization | Non-manager user (Waiter) visits `/manager/` | Server blocks request with HTTP 403 Forbidden page | Access Denied 403 Page Rendered | `PASS` |
| **SEC-MGR-02** | Unauthenticated Access | Guest visits `/manager/` without session | Server redirects to login page | Redirected to `public/login.php` | `PASS` |
| **SEC-MGR-03** | Role Spoofing Protection | Request with `?role=manager` via Waiter session | Server inspects `$_SESSION['user_role']`, rejects request | Request Rejected (403) | `PASS` |
| **SEC-MGR-04** | Admin Access Escalation | Manager visits `/admin/` portal | Server blocks request with HTTP 403 Forbidden page | Access Denied 403 Page Rendered | `PASS` |
| **SEC-MGR-05** | API Permission Guard | Manager calls `POST /api/v1/users/create.php` | Server checks `Auth::requirePermission('users.manage')` | Returns HTTP 403 JSON | `PASS` |
| **SEC-MGR-06** | Branch Isolation | Manager assigned to Branch 1 requests Branch 2 data | Server restricts data scope to Branch 1 | Branch 1 Data Only | `PASS` |
| **SEC-MGR-07** | IDOR Safety | Manager alters `order_id` in drilldown request | Server checks branch & session scope | Order Scope Enforced | `PASS` |
