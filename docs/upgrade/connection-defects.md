# SMARTRESTA — Connection Defects & System Audit Classification

## 1. Defect Classification Hierarchy

Defects discovered during the thorough audit of SMARTRESTA are categorized according to severity:

* **P0 — Critical**: Data corruption, payment breakdown, authentication bypass, security flaw.
* **P1 — Critical Workflow**: Core operational path broken (e.g. order submission fails, ticket status stuck, payment disconnections).
* **P2 — Important**: Broken filtering, UI status mismatches, missing reporting metrics.
* **P3 — Minor**: Spacing, styling, labels, cosmetic defects.

---

## 2. Identified Defect Log

### DEFECT-01 (P1 — Fixed in Prompt 01)
* **Severity**: P1 (Critical Workflow)
* **Component**: API endpoints (`api/v1/finance/`, `api/v1/commissions/`, `api/v1/expenses/`, etc.)
* **Problem**: 11 API endpoints called `require_once __DIR__ . '/../../helpers/response.php'`, but the `helpers/` directory did not exist in the repository, causing PHP Fatal Errors (`HTTP 500`) on Railway Linux servers.
* **Root Cause**: Missing helper file in repo migration.
* **Status**: **FIXED**. Created [helpers/response.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/helpers/response.php) delegating to `Response::json()`.

---

### DEFECT-02 (P1 — Fixed in Prompt 01)
* **Severity**: P1 (Critical Workflow)
* **Component**: Standalone module entrypoints (`php/*.php`) & asset relative paths
* **Problem**: Opening standalone PHP entrypoints directly in browser resulted in 404 broken CSS/JS assets because asset paths were relative (`assets/css/...`).
* **Root Cause**: Lack of dynamic base path calculation for subfolder routes.
* **Status**: **FIXED**. Added `$assetPrefix` calculation in [index.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/index.php) and updated [assets/js/ajax.js](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/assets/js/ajax.js) to resolve API URLs under `/php/` routes.

---

### DEFECT-03 (P1 — Architecture Migration Target for Prompt 02-05)
* **Severity**: P1 (Critical Architecture Target)
* **Component**: Global Application Shell (`index.php`) & Role Isolation
* **Problem**: Single DOM shell loads all role interfaces (`admin`, `pos`, `kds`, `inventory`, `finance`, `crm`, `reports`) on every request, relying on client-side JS hiding. Non-admin users load admin views into DOM.
* **Root Cause**: Legacy SPA architecture without server-side route separation.
* **Recommended Fix**: Implement five standalone, role-gated portals (`/admin`, `/manager`, `/reception`, `/waiter`, `/kitchen`) in subsequent upgrade prompts.
