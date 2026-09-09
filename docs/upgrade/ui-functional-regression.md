# SMARTRESTA — UI & Functional Regression Matrix

## Functional Regression Verification Post-UI Upgrade

| Workflow Area | Tested Operations | Core Engine & API | Visual & Functional Integrity | Status |
| :--- | :--- | :--- | :--- | :--- |
| **Authentication & Logout** | Login form submit, portal authorization guard, logout redirect | `Auth.php`, `Router.php` | Zero visual layout jump on login/logout cycle. | PASSED |
| **Waiter POS Ordering** | Select table, add variant/modifiers to cart, submit order | `OrderEngine.php`, `RoutingEngine.php` | Cart totals calculate correctly; submit triggers station tickets. | PASSED |
| **Kitchen KDS Operations** | Kanban queue rendering, start prep, mark ready, recall/refire | `KDSEngine.php` | Kanban cards render cleanly; status transitions log history. | PASSED |
| **Reception Billing & Settlement** | Search unpaid order, launch settlement modal, process Cash/MFS payment | `BillingEngine.php`, `PaymentEngine.php` | Bill totals match DB; payment allocation & thermal receipt work cleanly. | PASSED |
| **Admin Payments View** | KPI card metrics, filter dropdowns, receipt view modal, refund modal | `PaymentEngine.php`, `ReceiptEngine.php` | All controls interactive; zero console errors or dead buttons. | PASSED |
| **Manager Analytics** | Revenue summaries, waiter commission review, inventory stock alerts | `ReportEngine.php`, `CommissionEngine.php` | Real DB metrics render accurately in summary tables. | PASSED |
