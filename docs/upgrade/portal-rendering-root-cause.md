# SMARTRESTA Upgrade — Portal Rendering Root Cause Analysis

## Executive Summary

Prior to Prompt 13, SMARTRESTA exhibited a critical architectural flaw where all roles were presented with the same monolithic single dashboard interface. Navigating between operational views relied on client-side hash fragments (`/#pos`, `/#payments`, `/#kds`, `/#users`, etc.) rendered over a global shared dashboard shell.

This document details the exact technical root cause behind the single-dashboard hash navigation behavior and outlines the architectural deficiencies present before repair.

---

## 1. Identified Root Causes

### 1.1 Unconditional View Inclusion in `index.php`
- `index.php` previously contained hardcoded `require_once` statements for all 18 view partials (`views/admin.php`, `views/manager.php`, `views/pos.php`, `views/kds.php`, `views/kitchen.php`, `views/reception.php`, `views/waiter.php`, `views/tables.php`, `views/menu.php`, `views/users.php`, `views/routing.php`, `views/payments.php`, `views/commission_rules.php`, `views/commissions_review.php`, `views/payouts.php`, `views/inventory.php`, `views/reports.php`, `views/finance.php`, `views/crm.php`) inside `.page-container`.
- As a result, every HTTP request loaded the entire HTML layout of all portals into a single client DOM tree.

### 1.2 Monolithic Global Sidebar
- `<aside class="sidebar">` in `index.php` hardcoded links to every section in the platform regardless of the user's role or portal identity.
- Client-side JavaScript (`resolveRoleAccess`) attempted to hide/show sidebar items using CSS `display: flex/none`.
- Changing hash fragments in the browser URL (`/#pos`, `/#payments`) toggled section visibility client-side, bypassing server-side portal identity.

### 1.3 Portal Entrypoint Wrapper Pass-Through
- Portal entrypoints (`admin/index.php`, `manager/index.php`, `reception/index.php`, `waiter/index.php`, `kitchen/index.php`) called `Router::authorizePortal($portal)`, set `$initialSection`, and then executed `require_once __DIR__ . '/../index.php';`.
- Because `index.php` did not inspect `$currentPortal` or restrict view inclusions/navigation rendering, all entrypoints rendered the exact same master template.

---

## 2. Security & Architectural Risk

1. **Client-Side RBAC Leakage**: Unauthorized sections existed in the DOM, allowing users to potentially toggle section visibility via browser devtools.
2. **Missing Portal Identity**: Role identity was decoupled from the server-side URL path (`/admin/`, `/manager/`, `/reception/`, `/waiter/`, `/kitchen/`).
3. **Redundant DOM Payload**: Loading 18 views into a single page degraded DOM rendering performance.

---

## 3. Repair Summary

- Enforce server-side `$activePortal` context in `index.php`.
- Render role-tailored sidebar navigation shells for each portal.
- Conditionally include only authorized view partials in `.page-container`.
- Direct access to root `/` redirects server-side to the user's assigned portal URL.
