# SMARTRESTA — Role Dashboard Ownership & UX Matrix

## 1. Blueprint for Five Standalone Portals

To prevent unauthorized UI exposure and enforce strict role boundaries, SMARTRESTA will be restructured into five independent, role-scoped portals.

---

### Portal 1: ADMIN PORTAL (`/admin/`)
* **Target Users**: System Administrators, General Managers, Superusers
* **URL Path**: `/admin/` (Entrypoint: `public/admin/index.php`)
* **Primary Focus**: Data-dense system configuration, user access control, global reports, audit logs, global inventory, and master financial settings.
* **Core Capabilities**:
  - Full CRUD on Users, Roles & Permissions
  - Branch, Floor & Table Layout Configuration
  - Menu & Recipe Engineering
  - System-wide Sales, Tax, & Profit Reports
  - Audit Trail Inspection & Overrides

---

### Portal 2: MANAGER PORTAL (`/manager/`)
* **Target Users**: Restaurant Shift Managers, Floor Supervisors
* **URL Path**: `/manager/` (Entrypoint: `public/manager/index.php`)
* **Primary Focus**: Operational performance monitoring, shift tracking, waiter commissions, void approvals, and daily register close.
* **Core Capabilities**:
  - Floor & Table Occupancy Overview
  - Real-time Waiter Sales Matrix & Commission Approvals
  - Void / Cancel Order Approvals
  - Day-closing Shift Reconciliation & Register Close
  - Inventory Stock Level Alerts

---

### Portal 3: RECEPTION / CASHIER PORTAL (`/reception/`)
* **Target Users**: Receptionists, Cashiers, Front-Desk Hostesses
* **URL Path**: `/reception/` (Entrypoint: `public/reception/index.php`)
* **Primary Focus**: Table reservation check-in, fast order lookup, customer billing, payment settlement, and thermal receipt printing.
* **Core Capabilities**:
  - Floor Map & Active Table Session Lookup
  - Instant Order Bill Retrieval & Itemization
  - Multi-Payment Processing (Cash, bKash, Nagad, Card)
  - Receipt Printing & Invoice PDF Generation
  - Customer Registration & Reservation Check-in

---

### Portal 4: WAITER PORTAL (`/waiter/`)
* **Target Users**: Waitstaff, Floor Server Staff
* **URL Path**: `/waiter/` (Entrypoint: `public/waiter/index.php`)
* **Primary Focus**: Touch-first mobile/tablet order taking, table selection, dynamic menu search, item modifier customization, and order status tracking.
* **Core Capabilities**:
  - Visual Floor Table Selector
  - Category Pills & Instant Product Search
  - Cart Itemization with Modifiers & Special Instructions
  - One-tap "Submit & Route Order"
  - Real-time Ready Notifications from Kitchen

---

### Portal 5: KITCHEN / COUNTER PORTAL (`/kitchen/`)
* **Target Users**: Head Chefs, Line Cooks, Baristas, Station Dispatches
* **URL Path**: `/kitchen/` (Entrypoint: `public/kitchen/index.php`)
* **Primary Focus**: High-visibility, station-filtered KDS display with 4-column Kanban workflow for rapid order prep.
* **Core Capabilities**:
  - Station Filter Tabs (Kitchen Grill, Fryer, Bar, Dessert)
  - Real-time Ticket Dispatch with Elapsed Timers
  - Ticket Status Controls (`NEW` -> `PREPARING` -> `READY` -> `SERVED`)
  - Ticket Recall & Re-fire Requests
  - Station Pause / Busy Toggle

---

## 2. Shared Business Engine & Unified DB Access Matrix

```text
               ┌─────────────────────────────────────────┐
               │         AUTH & SESSION ENGINE           │
               │   (Enforces Server-Side Route Guard)    │
               └────────────────────┬────────────────────┘
                                    │
    ┌──────────────┬────────────────┼────────────────┬──────────────┐
    ▼              ▼                ▼                ▼              ▼
 /admin/        /manager/      /reception/       /waiter/        /kitchen/
 Admin UI      Manager UI     Reception UI       Waiter UI      Kitchen KDS
    │              │                │                │              │
    └──────────────┴────────────────┼────────────────┴──────────────┘
                                    │
                 ┌──────────────────▼──────────────────┐
                 │       SHARED BUSINESS ENGINES       │
                 │ OrderEngine, KDSEngine, PaymentEngine│
                 │ InventoryEngine, FinanceEngine, etc.│
                 └──────────────────┬──────────────────┘
                                    │
                 ┌──────────────────▼──────────────────┐
                 │          MYSQL DATABASE             │
                 │      Single Source of Truth         │
                 └─────────────────────────────────────┘
```
