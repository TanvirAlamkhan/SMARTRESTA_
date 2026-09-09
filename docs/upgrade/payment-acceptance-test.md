# SMARTRESTA — Payment Acceptance & Admin View Operability Test

## Admin Payments View (#payments-view) Audit Findings

### 1. View Accessibility & Layout
- **URL / Section**: `#payments` in `index.php` (included via `views/payments.php`).
- **Authorization**: Full access for `admin`, `manager`, `reception` roles.
- **KPI Summary Cards**: Real-time DB counters rendering:
  - `Open Bills (Unpaid)`
  - `Today Net Collections`
  - `Cash Register`
  - `Digital MFS & Card`
  - `Refunds Issued`

### 2. Interactive Controls & Buttons Tested

| Element ID / Selector | Action | Tested Function | Server API Endpoint | Result |
| :--- | :--- | :--- | :--- | :--- |
| `SmartBilling.loadPaymentHistory()` | Button Click | Reloads live payment transaction log | `GET /api/v1/payments/index.php` | PASSED |
| `SmartBilling.openOrderSelectionModal()` | Button Click | Launches interactive order settlement modal | `GET /api/v1/orders/index.php?payment_status=UNPAID` | PASSED |
| `#payments-search-input` | Text Input | Filters table rows dynamically by Order #, TRX ID, Cashier | Frontend filter + `GET /api/v1/payments/` | PASSED |
| `#payments-method-filter` | Dropdown | Filters table rows by Cash, bKash, Nagad, Card | Frontend filter + `GET /api/v1/payments/` | PASSED |
| `#payments-status-filter` | Dropdown | Filters table rows by `COMPLETED`, `REFUNDED`, `VOIDED` | Frontend filter + `GET /api/v1/payments/` | PASSED |
| `[View Receipt]` | Row Action | Displays thermal tax receipt overlay | `GET /api/v1/receipts/index.php` | PASSED |
| `[Process Refund]` | Row Action | Opens refund modal with audit reason prompt | `POST /api/v1/refunds/index.php` | PASSED |

### 3. Payment Settlement Workflow Verification
- **Single Payment**: Cash payment (৳735.00) processed cleanly, changing `orders.payment_status` to `PAID`.
- **Split Payment**: Partial cash + partial bKash settled against single order bill; `payment_allocations` correctly created.
- **Idempotency**: Submitting duplicate payment request returns existing `payment_id` without double-charging.
- **Overpayment Guard**: Attempting to pay ৳1000 on a ৳735 balance throws `400 Bad Request` ("Total payment exceeds remaining balance").
