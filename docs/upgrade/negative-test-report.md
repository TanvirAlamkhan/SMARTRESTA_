# SMARTRESTA — Negative & Security Acceptance Test Report

## Negative & Abuse Test Results

| Test Scenario | Test Input | Expected Behavior | Actual Behavior | Result |
| :--- | :--- | :--- | :--- | :--- |
| **Invalid Credentials Login** | Email: `wrong@restaurant.com`, Pass: `badpass` | Reject login with error message | HTTP 401 Unauthorized, "Invalid credentials" | PASSED |
| **Empty Order Submission** | Submit order with 0 line items | Reject order submission | HTTP 422 Unprocessable, "Cannot submit empty order" | PASSED |
| **Negative Item Quantity** | Add product with quantity `-5` | Reject line item addition | HTTP 422 Unprocessable, "Quantity must be > 0" | PASSED |
| **Illegal KDS State Jump** | Transition ticket directly from `NEW` to `SERVED` | Reject invalid transition | HTTP 409 Conflict, "Invalid status transition" | PASSED |
| **Overpayment Attempt** | Pay ৳2,000 on ৳500 bill | Reject payment | HTTP 400 Bad Request, "Payment exceeds balance" | PASSED |
| **Unauthenticated API Access** | `GET /api/v1/orders/index.php` without session | Redirect / Return 401 | HTTP 401 Unauthorized | PASSED |
| **Cross-Portal RBAC Bypass** | Waiter user accesses `/admin/` | Return 403 Forbidden | HTTP 403 Forbidden page rendered | PASSED |
| **Cross-Branch IDOR Attack** | Branch 1 user requests Branch 2 order ID | Return 403 / 404 | HTTP 404 Not Found / 403 Access Denied | PASSED |
| **XSS Payload Injection** | Note: `<script>alert('XSS')</script>` | HTML escape output | Escaped as `&lt;script&gt;` | PASSED |
| **SQL Injection Attempt** | Input: `' OR '1'='1` in search field | Prepared statement parameter binding | Treated as literal search string | PASSED |
| **Invalid CSRF Token** | POST request with invalid CSRF token | Reject request | HTTP 403 Forbidden, "Invalid CSRF token" | PASSED |
