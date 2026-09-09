# SMARTRESTA — Admin Data Flow & Drill-Down Specification

## 1. Single Authoritative Order Drill-Down Flow

When an Admin clicks an order number or "Details" button in the Live Orders table, the system opens a modal that calls [ReportEngine::getDrilldownOrderDetails()](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/core/ReportEngine.php#L666):

```text
  Admin clicks Order #1001
        │
        ▼ (GET /api/v1/reports/orders_drilldown.php?order_id=1001)
  ReportEngine::getDrilldownOrderDetails(1001, branchId)
        │
        ├─► SELECT FROM orders (Header, Table, Waiter, Status)
        ├─► SELECT FROM order_items (Line items, quantities, prices)
        ├─► SELECT FROM order_tickets (Kitchen KDS tickets, station, status)
        ├─► SELECT FROM payments (Payment transactions, method, receiver)
        ├─► SELECT FROM commission_transactions (Waiter earnings)
        └─► SELECT FROM inventory_transactions (Ingredient stock deductions)
        │
        ▼
  Renders Modal with 6 Linked Operational Tabs in Admin UI
```

---

## 2. Real-Time Date Preset Filtering

When the Admin changes the date preset selector:
1. JavaScript invokes `SmartReports.loadOverviewDashboard()`.
2. Request passes `preset=today|yesterday|this_week|this_month|custom` & `date_from`/`date_to`.
3. `ReportEngine::normalizeDateRange()` constructs precise `start` and `end` datetime bounds.
4. All SQL queries execute against `created_at BETWEEN :start AND :end`.
5. Real KPI numbers update dynamically without page refresh.
