# SMARTRESTA Upgrade — Portal Architecture & Navigation Repair Specification

## Overview

Prompt 13 repaired the portal architecture of SMARTRESTA by replacing the monolithic client-side hash navigation shell with five genuinely separate, server-side role-based portals:

1. **Admin Portal** (`/admin/` or `/admin/index.php`)
2. **Manager Portal** (`/manager/` or `/manager/index.php`)
3. **Reception Portal** (`/reception/` or `/reception/index.php`)
4. **Waiter Portal** (`/waiter/` or `/waiter/index.php`)
5. **Kitchen Portal** (`/kitchen/` or `/kitchen/index.php`)

---

## 1. Architectural Changes

```text
CLIENT REQUEST
      ↓
PORTAL ENTRYPOINT (/admin/, /manager/, /reception/, /waiter/, /kitchen/)
      ↓
SERVER-SIDE RBAC GUARD (Router::authorizePortal)
      ↓ (If Unauthorized -> HTTP 403 Forbidden Page)
SET $currentPortal Context
      ↓
RENDER ROLE-SPECIFIC SIDEBAR & TOPBAR
      ↓
LOAD ONLY AUTHORIZED VIEW PARTIALS
      ↓
SHARED REST API ENGINE & MYSQL DATABASE
```

---

## 2. Portal Configuration Mapping

| Portal | Portal Badge | Primary Route | Sidebar Navigation Items | Allowed Views Loaded in DOM |
| :--- | :--- | :--- | :--- | :--- |
| **Admin** | `ADMIN` | `/admin/` | Admin Overview, Users, Tables, Menu, Waiter, POS, KDS, Kitchen, Routing, Payments, Rules, Reviews, Payouts, Inventory, Reports, Finance, CRM | `admin`, `users`, `tables`, `menu`, `waiter`, `pos`, `kds`, `kitchen`, `routing`, `payments`, `commission_rules`, `commissions_review`, `payouts`, `inventory`, `reports`, `finance`, `crm` |
| **Manager** | `MANAGER` | `/manager/` | Manager Dashboard, Waiter Performance, Reception Desk, Tables, Menu Performance, Kitchen Monitor, Production, Payments, Approvals, Inventory, Reports, Finance, CRM | `admin`, `waiter`, `reception`, `tables`, `menu`, `kds`, `kitchen`, `payments`, `commissions_review`, `inventory`, `reports`, `finance`, `crm` |
| **Reception** | `RECEPTION` | `/reception/` | Reception Front Desk, POS Billing & Checkout, Tables & Sessions, Billing & Payment History, Customers & Reservations | `reception`, `pos`, `tables`, `payments`, `crm` |
| **Waiter** | `WAITER` | `/waiter/` | Waiter Workspace, POS & Table Ordering, Floors & Tables, Kitchen Feed | `waiter`, `pos`, `tables`, `kds` |
| **Kitchen** | `KITCHEN` | `/kitchen/` | Kitchen Production, Kitchen Display System (KDS), Station Routing, Stock & Ingredients | `kitchen`, `kds`, `routing`, `inventory` |

---

## 3. Server-Side Enforcement Rules

1. **Authentication**: All portal entrypoints enforce `Auth::requireAuth()`. Unauthenticated requests redirect to `public/login.php`.
2. **Server-Side Guard**: Entrypoints invoke `Router::authorizePortal($portal)`. Unauthorized role access returns HTTP 403 Forbidden with audit logging to `audit_logs`.
3. **Direct Root Access**: Navigating directly to `/index.php` triggers `Router::redirectToPortal()`, routing the user to their role-assigned portal entrypoint.
4. **Single Source of Truth**: All portals consume shared REST APIs and MySQL database engines without data duplication or mock fallbacks.
