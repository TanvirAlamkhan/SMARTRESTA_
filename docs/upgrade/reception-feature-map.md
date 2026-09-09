# SMARTRESTA — Reception Feature Map (Prompt 05)

## 1. Feature Inventory & Implementation Matrix

| Feature | Description | Engine / API Endpoint | Status |
| :--- | :--- | :--- | :--- |
| **Server-Side Portal Guard** | Role authorization for `/reception/` portal | `Router::authorizePortal('reception')` | **IMPLEMENTED** |
| **Cashier Dashboard KPIs** | Real DB metrics for Open Bills, Today's Net, Cash Drawer, Digital MFS, Refunds | `PaymentEngine`, `OrderEngine` | **IMPLEMENTED** |
| **Active Orders & Search** | Retrieve active dine-in and takeaway orders | `GET /api/v1/orders/index.php` | **IMPLEMENTED** |
| **Order Details & Billing** | Itemized subtotal, discount, 5% VAT, service charge, outstanding balance | `GET /api/v1/billing/index.php` | **IMPLEMENTED** |
| **Payment Processing** | Single and split payment processing with idempotency protection | `POST /api/v1/payments/index.php` | **IMPLEMENTED** |
| **bKash Payment QR** | Auto-generates dynamic bKash merchant QR code & TrxID input sync | `SmartBilling.onMethodChange` | **IMPLEMENTED** |
| **Duplicate Payment Guard** | Double-click UI prevention and DB `FOR UPDATE` transaction lock | `PaymentEngine::processPayment` | **IMPLEMENTED** |
| **Thermal Receipts** | 80mm printable thermal receipt generator and preview modal | `ReceiptEngine` | **IMPLEMENTED** |
| **Refund Processing** | Mandatory reason prompt, max refundable balance validation, and audit logging | `RefundEngine` | **IMPLEMENTED** |
| **Table & Session View** | Real-time floor occupancy and active session status | `DiningSessionEngine` | **IMPLEMENTED** |
| **Customer CRM & Reservations** | Search customers, register profiles, and manage table reservations | `CRMEngine` | **IMPLEMENTED** |
| **Cashier Shift Close** | Cashier shift opening, physical cash drawer count, and shift closing | `FinanceEngine` | **IMPLEMENTED** |

---

## 2. Shared Transaction Lifecycle

```text
WAITER
  │ (Creates Order #1001)
  ▼
KITCHEN
  │ (Prepares Ticket -> READY)
  ▼
RECEPTION / CASHIER
  │ (Calculates Bill -> Processes Payment -> Prints Receipt)
  ▼
FINANCE & MANAGER & ADMIN
  │ (Reflects Paid Transaction in Daily Reports)
```
