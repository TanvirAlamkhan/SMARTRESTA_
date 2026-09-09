# SMARTRESTA — Complete Existing-System Audit (Prompt 01)

## 1. Executive Summary & Audit Overview

This audit document presents a comprehensive, production-grade inspection of the **SMARTRESTA** web application repository. SMARTRESTA is a full-stack, multi-role Restaurant Operating System (ROS) engineered in PHP 8.x, Vanilla ES6 JavaScript, HTML5/CSS3, and MySQL 8.x PDO.

The primary objective of Upgrade Prompt 01 is to audit the entire platform, map all data flows, analyze the existing single-dashboard architecture, and design the blueprint for separating the application into **five genuinely separate role-specific portals**:
1. **Admin Portal** (`/admin`)
2. **Manager Portal** (`/manager`)
3. **Reception / Cashier Portal** (`/reception`)
4. **Waiter Portal** (`/waiter`)
5. **Kitchen / Counter Portal** (`/kitchen`)

---

## 2. Git Repository & Environment State

* **Repository Root**: `D:/Saas Development project/WEBSITE/SMARTRESTA`
* **Remote Origin**: `https://github.com/TanvirAlamkhan/SMARTRESTA_.git`
* **Active Branch**: `master` (up to date with `origin/master`)
* **Working Tree**: Clean (0 uncommitted changes)
* **Latest Commits**:
  - `26a6022` Fix standalone PHP entrypoints in php/ directory and dynamic asset resolution
  - `457b12f` Add missing helpers/response.php helper file
  - `67290a0` Fix clean hash navigation and API path resolution across index.php and php/ redirect endpoints

---

## 3. Directory & Component Inventory

### Core Modules (`core/`)
- [Auth.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/core/Auth.php): Session management, RBAC checks (`hasRole`, `requirePermission`).
- [OrderEngine.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/core/OrderEngine.php): Creation, calculation (VAT, discount), order status history transitions.
- [RoutingEngine.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/core/RoutingEngine.php): Automatic item dispatch to kitchen/bar stations.
- [KDSEngine.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/core/KDSEngine.php): Real-time station ticket status (NEW -> PREPARING -> READY -> SERVED).
- [PaymentEngine.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/core/PaymentEngine.php): Payment processing, cash/mobile banking settlements.
- [BillingEngine.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/core/BillingEngine.php): Invoice generation, dining session closure.
- [CommissionEngine.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/core/CommissionEngine.php): Waiter sales percentage & flat per-order calculations.
- [InventoryEngine.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/core/InventoryEngine.php): Ingredient stock tracking, recipe deductions, purchasing.
- [FinanceEngine.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/core/FinanceEngine.php): Day-closing shifts, expense recording, cash drawer audits.
- [ReportEngine.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/core/ReportEngine.php): Sales metrics, top products, waiter leaderboards.
- [CRMEngine.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/core/CRMEngine.php): Customer profiles, loyalty points, reservations, coupons.
- [AuditLogger.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/core/AuditLogger.php): System activity audit tracking.

### Views (`views/`)
1. [admin.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/views/admin.php): Manager KPI Overview
2. [pos.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/views/pos.php): Point of Sale & Waiter Ordering Cart
3. [kds.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/views/kds.php): Kitchen Display System (4-column Kanban)
4. [tables.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/views/tables.php): Floor Map & Table Grid
5. [menu.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/views/menu.php): Category & Product Management
6. [inventory.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/views/inventory.php): Stock, Ingredients & Recipes
7. [reports.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/views/reports.php): Sales & Analytics Tabs
8. [finance.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/views/finance.php): Expenses, Shifts & Day Close
9. [crm.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/views/crm.php): Customers, Reservations & Coupons
10. [users.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/views/users.php): Users & Staff Management
11. [payments.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/views/payments.php): Billing History

---

## 4. Current Dashboard Architecture & Flaws

Currently, SMARTRESTA utilizes a single SPA shell defined in [index.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/index.php). All views (`views/*.php`) are rendered into the DOM simultaneously inside `<div class="page-container">`, with `display:none` set on inactive sections.

### Key Flaws Identified:
1. **Lack of URL-based Role Isolation**: Staff logged in as `waiter` or `kitchen` load the full DOM tree containing admin cards, reports, and financial controls, merely hidden via CSS `display:none`.
2. **Missing Dedicated Role Routes**: Roles do not have clean entrypoints (`/admin`, `/manager`, `/reception`, `/waiter`, `/kitchen`).
3. **Frontend Privilege Leakage**: Inactive section hiding depends on client-side JS; malicious users can tamper with DOM CSS to view unauthorized sections.
