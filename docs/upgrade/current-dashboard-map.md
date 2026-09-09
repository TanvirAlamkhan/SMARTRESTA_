# SMARTRESTA — Current Dashboard Map

## 1. Existing System Architecture Map

```text
                               ┌─────────────────────────┐
                               │       Landing Page      │
                               │      (landing.php)      │
                               └────────────┬────────────┘
                                            │
                               ┌────────────▼────────────┐
                               │        Login Page       │
                               │    (public/login.php)   │
                               └────────────┬────────────┘
                                            │
                               ┌────────────▼────────────┐
                               │   Auth & Session Check  │
                               │       (core/Auth.php)   │
                               └────────────┬────────────┘
                                            │
               ┌────────────────────────────┴────────────────────────────┐
               ▼                                                         ▼
    Single SPA Shell (index.php)                        Standalone PHP Wrappers (php/*.php)
    Includes ALL 15 View Partials                       Include index.php with $initialSection
    DOM Visibility Controlled by JS                     e.g. php/pos.php -> $initialSection='pos'
```

---

## 2. Dynamic View Rendering Chain

In [index.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/index.php), the page container includes all view partials:

```php
<div class="page-container">
  <?php
  require_once __DIR__ . '/views/admin.php';
  require_once __DIR__ . '/views/tables.php';
  require_once __DIR__ . '/views/menu.php';
  require_once __DIR__ . '/views/pos.php';
  require_once __DIR__ . '/views/kds.php';
  require_once __DIR__ . '/views/users.php';
  require_once __DIR__ . '/views/routing.php';
  require_once __DIR__ . '/views/payments.php';
  require_once __DIR__ . '/views/commission_rules.php';
  require_once __DIR__ . '/views/commissions_review.php';
  require_once __DIR__ . '/views/payouts.php';
  require_once __DIR__ . '/views/inventory.php';
  require_once __DIR__ . '/views/reports.php';
  require_once __DIR__ . '/views/finance.php';
  require_once __DIR__ . '/views/crm.php';
  ?>
</div>
```

---

## 3. Sidebar Navigation & Section Switching

JavaScript function `switchRoleView(viewId, navEl, event)` in [index.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/index.php#L1508) toggles section visibility:

1. Hides all `.role-view` elements (`display: none`).
2. Shows target section element (`display: block`).
3. Updates `window.location.hash` or `window.history.pushState`.
4. Triggers data-loader functions:
   - `pos`: `loadPOSProducts()`, `loadPOSTableSelector()`
   - `kds`: `SmartKDS.loadKDSGrid()`
   - `tables`: `loadFloorTables()`
   - `menu`: `loadProductCatalog()`, `loadCategoryOptions()`
   - `inventory`: `SmartInventory.init()`
   - `finance`: `SmartFinance.init()`
   - `reports`: `SmartReports.loadCurrentTab()`
   - `crm`: `SmartCRM.init()`

---

## 4. Current Role Access Matrix

| Sidebar Module Section | Admin | Manager | Reception | Waiter | Kitchen |
| :--- | :---: | :---: | :---: | :---: | :---: |
| **Manager Dashboard** (`#admin`) | Yes | Yes | Hidden | Hidden | Hidden |
| **Floors & Tables** (`#tables`) | Yes | Yes | Yes | Yes | Read-only |
| **Menu & Catalog** (`#menu`) | Yes | Yes | Read-only | Read-only | Read-only |
| **POS & Waiter Ordering** (`#pos`) | Yes | Yes | Yes | Yes | Hidden |
| **Kitchen Display (KDS)** (`#kds`) | Yes | Yes | Status | Status | Yes |
| **Billing & Payments** (`#payments`) | Yes | Yes | Yes | Hidden | Hidden |
| **Users & Staff Roles** (`#users`) | Yes | Hidden | Hidden | Hidden | Hidden |
| **Stock & Ingredients** (`#inventory`) | Yes | Yes | Hidden | Hidden | Read-only |
| **Reports & Analytics** (`#reports`) | Yes | Yes | Hidden | Hidden | Hidden |
| **Finance & Day Close** (`#finance`) | Yes | Yes | Shift-only | Hidden | Hidden |
| **CRM & Reservations** (`#crm`) | Yes | Yes | Yes | Hidden | Hidden |
