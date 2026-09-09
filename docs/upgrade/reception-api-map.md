# SMARTRESTA — Reception API Mapping (Prompt 05)

## 1. REST API Endpoint Mapping

| API Endpoint | HTTP Method | Core Engine | Purpose / Action | Access Control |
| :--- | :---: | :--- | :--- | :--- |
| `/api/v1/payments/index.php` | `GET` | `PaymentEngine::getPaymentsList` | Fetch payment transactions list with filters | Auth + `payments.view` |
| `/api/v1/payments/index.php` | `POST` | `PaymentEngine::processPayment` | Process order payment with split payment & idempotency | Auth + `payments.process` |
| `/api/v1/billing/index.php` | `GET` | `BillingEngine::calculateOrderBill` | Calculate itemized order bill & outstanding balance | Auth + `billing.view` |
| `/api/v1/receipts/index.php` | `GET` | `ReceiptEngine::generateReceipt` | Generate/fetch 80mm printable thermal receipt snapshot | Auth + `receipts.view` |
| `/api/v1/refunds/index.php` | `GET` | `RefundEngine::getRefundsList` | Fetch refund history for branch | Auth + `payments.view` |
| `/api/v1/refunds/index.php` | `POST` | `RefundEngine::processRefund` | Reverse payment with reason check and audit log | Auth + `payments.refund` |
| `/api/v1/orders/index.php` | `GET` | `OrderEngine::getOrders` | Search active orders by table, status, or order # | Auth + `orders.view` |
| `/api/v1/payment_methods/index.php` | `GET` | `PaymentEngine::getPaymentMethods` | Fetch active payment methods (Cash, bKash, Nagad, Card) | Auth + `payment_methods.view` |
| `/api/v1/dining_sessions/index.php` | `GET` | `DiningSessionEngine` | Fetch active dining sessions and table assignments | Auth + `dining_sessions.view` |
| `/api/v1/crm/index.php` | `GET/POST` | `CRMEngine` | Search customers, register customers, manage reservations | Auth + `customers.view` |
| `/api/v1/shifts/index.php` | `GET/POST` | `FinanceEngine` | Cashier shift opening, drawer balance, and shift closing | Auth + `shifts.view` |

---

## 2. API Data Flow Example (Payment Processing)

```text
POST /api/v1/payments/index.php
Payload:
{
  "order_id": 1001,
  "split_payments": [
    { "payment_method_id": 2, "amount": 500.00, "transaction_reference": "TRX998811" }
  ],
  "idempotency_key": "IK-172589000-1234"
}
      ↓
Auth::requireAuth()
      ↓
PaymentEngine::processPayment()
  ├── Check idempotency key in payments table
  ├── Lock orders row FOR UPDATE
  ├── Calculate server-authoritative bill via BillingEngine
  ├── Validate payment amount <= outstanding balance
  ├── Insert into payments table
  ├── Insert into payment_allocations table
  ├── Auto-generate receipt via ReceiptEngine
  ├── Update orders.payment_status ('PAID' or 'PARTIAL')
  ├── Process waiter commission via CommissionEngine
  └── Record audit log via AuditLogger
      ↓
200 OK JSON Response { success: true, payment_id: ..., bill: ... }
```
