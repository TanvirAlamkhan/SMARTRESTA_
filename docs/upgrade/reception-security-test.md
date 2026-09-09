# SMARTRESTA — Reception Security & Validation Matrix (Prompt 05)

## 1. Security Test Cases & Execution Results

| Security Test Case | Scenario / Payload | Expected Behavior | Verification Result | Status |
| :--- | :--- | :--- | :--- | :---: |
| **Portal Protection** | Direct unauthenticated GET `/reception/` | Redirect to `/public/login.php` | Redirected | **PASS** |
| **Role-Gated Portal Access** | Kitchen user accessing `/reception/` | HTTP 403 Forbidden Error page | Rendered 403 Page | **PASS** |
| **Direct API Auth Check** | GET `/api/v1/payments/index.php` without session | HTTP 401 Unauthorized JSON | 401 JSON Response | **PASS** |
| **Negative Payment Reject** | Payment amount `-500.00` | 400 Error: "Payment amount must be greater than zero." | Rejected | **PASS** |
| **Zero Payment Reject** | Payment amount `0.00` | 400 Error: "Payment amount must be greater than zero." | Rejected | **PASS** |
| **Overpayment Guard** | Payment amount `10,000.00` on ৳500 balance | 400 Error: "Total payment amount exceeds remaining balance." | Rejected | **PASS** |
| **Already Settled Order** | Submitting payment for already `PAID` order | 400 Error: "Order is already fully settled." | Rejected | **PASS** |
| **Duplicate Payment Key** | Resubmitting payment with duplicate `idempotency_key` | Idempotent return of existing payment record without double charge | Idempotent Return | **PASS** |
| **Invalid Refund Amount** | Requesting ৳2,000 refund on ৳500 payment | 400 Error: "Requested refund exceeds maximum refundable balance." | Rejected | **PASS** |
| **Missing Refund Reason** | Processing refund with empty reason string | 400 Error: "Refund reason is mandatory." | Rejected | **PASS** |
| **Branch Isolation Guard** | Attempting payment for order belonging to another branch | Scope check validation & authorization check | Enforced Scope | **PASS** |
| **SQL Injection Check** | Order search `' OR 1=1 --` | Parameterized PDO binding treats payload as literal string | Sanitized Query | **PASS** |
| **XSS Defense** | Customer name `<script>alert('xss')</script>` | HTML entity escaping `htmlspecialchars` on output | Escaped HTML | **PASS** |
| **Admin Privilege Leak** | Reception accessing `/admin/` portal | HTTP 403 Forbidden Error page | 403 Forbidden | **PASS** |

---

## 2. Security Compliance Audit Summary

- **Database Parameter Binding**: 100% of queries in `PaymentEngine`, `BillingEngine`, `ReceiptEngine`, `RefundEngine`, and `DiningSessionEngine` use PDO prepared statements.
- **Idempotency Key Engine**: Client passes unique `idempotency_key` per transaction attempt to safeguard against network retries or duplicate button clicks.
- **Concurrency Safety**: Database row locking (`SELECT ... FOR UPDATE`) prevents concurrent payment submissions from race conditions.
- **Audit Logging**: All successful payments, failed attempts, and processed refunds generate immutable entries in `audit_logs`.
