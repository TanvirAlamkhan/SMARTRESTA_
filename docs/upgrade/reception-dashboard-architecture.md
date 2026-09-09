# SMARTRESTA — Reception / Cashier Dashboard Architecture (Prompt 05)

## 1. Overview & Operational Transaction Blueprint

The **Reception / Cashier Portal** (`/reception/` / [reception/index.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/reception/index.php)) serves as the primary operational transaction, billing, settlement, digital QR payment, thermal receipt, and refund management center of the SMARTRESTA platform.

It provides real-time transaction processing across:
- **Cashier Overview & Real-Time KPIs**: Open Unpaid Bills, Net Collections Today, Cash Drawer Register, Digital Payments (bKash, Nagad, Card), Refunds Issued.
- **Active Orders & Order Search**: Shared order model across Waiter, KDS, and Reception.
- **Floors & Tables Status**: Live occupancy and active dining session tracking.
- **Billing & Settlement Engine**: Itemized order calculations, discounts, 5% VAT, service charge, and net outstanding balance calculations via `BillingEngine`.
- **Payment Processing & Payment Methods**: Cash, bKash MFS with auto-generated payment QR code, Nagad, and POS Cards via `PaymentEngine`.
- **Thermal Receipts & Printing**: 80mm printable receipts with persistent JSON snapshot via `ReceiptEngine`.
- **Audit-Logged Refunds**: Reversals with mandatory reason logging and refundable balance checks via `RefundEngine`.
- **Customer CRM & Reservations**: Table reservations and customer lookup via `CRMEngine`.
- **Cashier Shift Reconciliation**: Shift closing and cash reconciliation via `FinanceEngine`.

---

## 2. Component Hierarchy & Architectural Flow

```text
       [ CASHIER / RECEPTIONIST ]
                   │
                   ▼
         /reception/index.php
                   │
                   ▼
     Router::authorizePortal('reception') ──(If Unauthorized)──► 403 Forbidden Page
                   │
                   ▼ (If Authorized)
      [ RECEPTION PORTAL SHELL ]
                   │
  ┌────────────────┼────────────────┬────────────────┬────────────────┐
  ▼                ▼                ▼                ▼                ▼
[PAYMENTS]      [ORDERS]         [TABLES]         [CRM]           [FINANCE]
(views/payments) (views/admin)   (views/tables)   (views/crm)     (views/finance)
  │                │                │                │                │
  ▼                ▼                ▼                ▼                ▼
GET /api/v1/    GET /api/v1/     GET /api/v1/     GET /api/v1/     GET /api/v1/
payments/       orders/          tables/          crm/             shifts/
  │                │                │                │                │
  ▼                ▼                ▼                ▼                ▼
PaymentEngine   OrderEngine      DiningSession    CRMEngine        FinanceEngine
  │                │                │                │                │
  └────────────────┴────────────────┼────────────────┴────────────────┘
                                    ▼
                         [ MYSQL 8.x DATABASE ]
                       (smartresta_db 35 Tables)
```

---

## 3. Authoritative Single Transaction Source

All Reception operations access the shared restaurant database:
1. **Single Order ID**: Orders placed by Waiters appear instantly in Reception.
2. **Single Session ID**: Table status is shared across all 5 portals.
3. **Single Payment ID**: Payments recorded in Reception update Manager performance and Admin financial analytics in real time.
