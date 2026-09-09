# SMARTRESTA — Manager Feature Map & Module Status

## 1. Manager Feature & RBAC Matrix

The table below details all operational features accessible within the Manager Portal:

| Manager Feature | Backend Engine / Controller | Key Database Tables | Permission | Implemented | Test Status |
| :--- | :--- | :--- | :--- | :---: | :---: |
| **Operational KPIs & Date Filters** | [ReportEngine.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/core/ReportEngine.php) | `orders`, `payments` | `dashboard.view` | `IMPLEMENTED` | `PASS` |
| **Live Orders & Table Monitor** | [OrderEngine.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/core/OrderEngine.php) | `orders`, `order_items`, `restaurant_tables` | `orders.view` | `IMPLEMENTED` | `PASS` |
| **Kitchen KDS Performance** | [KDSEngine.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/core/KDSEngine.php) | `order_tickets`, `stations` | `kds.view` | `IMPLEMENTED` | `PASS` |
| **Waiter Matrix & Commissions** | [CommissionEngine.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/core/CommissionEngine.php) | `users`, `commission_transactions` | `commissions.view` | `IMPLEMENTED` | `PASS` |
| **Inventory & Low Stock Alerts** | [InventoryEngine.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/core/InventoryEngine.php) | `ingredients`, `inventory_transactions` | `inventory.view` | `IMPLEMENTED` | `PASS` |
| **Sales & Payment Reconciliation** | [PaymentEngine.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/core/PaymentEngine.php) | `payments`, `payment_methods` | `payments.view` | `IMPLEMENTED` | `PASS` |
| **Expenses & Shift Close** | [FinanceEngine.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/core/FinanceEngine.php) | `shifts`, `expenses` | `finance.view` | `IMPLEMENTED` | `PASS` |
| **CRM Customer & Reservations** | [CRMEngine.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/core/CRMEngine.php) | `customers`, `reservations` | `crm.view` | `IMPLEMENTED` | `PASS` |
| **Operational Reports & Export** | [ReportEngine.php](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/core/ReportEngine.php) | `orders`, `payments`, `order_items` | `reports.view` | `IMPLEMENTED` | `PASS` |
