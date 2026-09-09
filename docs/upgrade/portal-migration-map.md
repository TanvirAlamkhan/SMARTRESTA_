# SMARTRESTA — View & Feature Portal Migration Map

## 1. Migration Destination for Existing Views

The table below maps all existing view partials (`views/*.php`) to their target portals for Prompts 03-05:

| Existing View File | Current Feature | Target Portal(s) | Migration Action |
| :--- | :--- | :--- | :--- |
| `views/admin.php` | Manager Dashboard Overview | `/admin/`, `/manager/` | Reuse as primary dashboard landing for Admin/Manager |
| `views/pos.php` | POS & Waiter Ordering | `/waiter/`, `/reception/` | Primary workspace for Waiter Portal |
| `views/kds.php` | Kitchen Display System | `/kitchen/` | Primary workspace for Kitchen Portal |
| `views/tables.php` | Floor Map & Table Grid | `/waiter/`, `/reception/`, `/manager/`, `/admin/` | Reusable layout view across operational portals |
| `views/payments.php` | Billing & Payments History | `/reception/`, `/admin/`, `/manager/` | Primary workspace for Reception Portal |
| `views/menu.php` | Menu & Product Catalog | `/admin/`, `/manager/` | Admin menu setup workspace |
| `views/inventory.php` | Stock & Ingredient Control | `/admin/`, `/manager/` | Inventory management workspace |
| `views/reports.php` | Operational Analytics | `/admin/`, `/manager/` | Reporting workspace |
| `views/finance.php` | Finance & Day Closing | `/admin/`, `/manager/`, `/reception/` | Financial close workspace |
| `views/crm.php` | Customer & Reservations | `/reception/`, `/manager/`, `/admin/` | CRM workspace |
| `views/users.php` | User & Role Management | `/admin/` | Admin access control workspace |
| `views/routing.php` | Station Routing Engine | `/admin/`, `/manager/` | Station config workspace |
| `views/commission_rules.php` | Commission Rules | `/admin/`, `/manager/` | Rule configuration workspace |
| `views/commissions_review.php`| Commission Approvals | `/manager/`, `/admin/` | Manager approval workspace |
| `views/payouts.php` | Commission Payouts | `/admin/`, `/manager/` | Settlement history workspace |
