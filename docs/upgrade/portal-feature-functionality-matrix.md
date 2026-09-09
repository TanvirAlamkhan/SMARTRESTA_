# SMARTRESTA Upgrade — Portal Feature Functionality Matrix

## Functional Feature Matrix Across Role Portals

This matrix defines the exact operational features exposed, authorized, and rendered within each SMARTRESTA role portal following the Prompt 13 portal architecture repair.

---

## 1. Feature Coverage Matrix

| Feature Module | Admin (`/admin/`) | Manager (`/manager/`) | Reception (`/reception/`) | Waiter (`/waiter/`) | Kitchen (`/kitchen/`) | API Endpoint Consumed |
| :--- | :---: | :---: | :---: | :---: | :---: | :--- |
| **System Overview & Manager Dashboard** | ✅ | ✅ | ❌ | ❌ | ❌ | `api/v1/reports/summary.php` |
| **Users, Staff & Role Management** | ✅ | ❌ | ❌ | ❌ | ❌ | `api/v1/users/index.php` |
| **Floors & Table Structure Management** | ✅ | ✅ | ✅ (Read/Session) | ✅ (Read/Session) | ❌ | `api/v1/tables/index.php` |
| **Menu & Product Catalog Management** | ✅ | ✅ | ❌ | ❌ | ❌ | `api/v1/products/index.php` |
| **Waiter POS Workspace & Table Ordering** | ✅ | ✅ | ❌ | ✅ | ❌ | `api/v1/orders/index.php` |
| **Reception Desk & Session Checkout** | ✅ | ✅ | ✅ | ❌ | ❌ | `api/v1/reception/index.php` |
| **POS Ordering & Billing Checkout** | ✅ | ✅ | ✅ | ✅ | ❌ | `api/v1/orders/index.php` |
| **Kitchen Display System (KDS)** | ✅ | ✅ | ❌ | ✅ (Feed) | ✅ | `api/v1/kds/tickets.php` |
| **Kitchen Production Dashboard** | ✅ | ✅ | ❌ | ❌ | ✅ | `api/v1/kitchen/tickets.php` |
| **Station Routing Engine** | ✅ | ❌ | ❌ | ❌ | ✅ | `api/v1/routing/rules.php` |
| **Billing, Payments & Receipts** | ✅ | ✅ | ✅ | ❌ | ❌ | `api/v1/payments/index.php` |
| **Commission Rules Engine** | ✅ | ❌ | ❌ | ❌ | ❌ | `api/v1/commissions/rules.php` |
| **Commission Review & Approvals** | ✅ | ✅ | ❌ | ❌ | ❌ | `api/v1/commissions/review.php` |
| **Commission Payout Settlements** | ✅ | ❌ | ❌ | ❌ | ❌ | `api/v1/commissions/payouts.php` |
| **Stock & Ingredients Inventory** | ✅ | ✅ | ❌ | ❌ | ✅ | `api/v1/inventory/items.php` |
| **Reports & Business Analytics** | ✅ | ✅ | ❌ | ❌ | ❌ | `api/v1/reports/index.php` |
| **Finance, Shifts & Day Close** | ✅ | ✅ | ❌ | ❌ | ❌ | `api/v1/finance/shifts.php` |
| **CRM, Customers & Table Reservations** | ✅ | ✅ | ✅ | ❌ | ❌ | `api/v1/crm/customers.php` |
| **Audit Logging & System Security** | ✅ | ❌ | ❌ | ❌ | ❌ | `api/v1/audit/logs.php` |

---

## 2. Shared Engine Consistency

- All features above interact directly with shared core business engines:
  - `OrderEngine`
  - `DiningSessionEngine`
  - `KDSEngine`
  - `PaymentEngine`
  - `BillingEngine`
  - `CommissionEngine`
  - `InventoryEngine`
  - `AuditLogger`
- Database integrity is strictly preserved; no mock arrays or mock APIs are used.
