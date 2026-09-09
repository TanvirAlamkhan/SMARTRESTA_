# SMARTRESTA Waiter Portal Feature Map

## 1. Overview & Real-Time KPIs
- **My Active Tables**: Shows real-time occupied tables assigned/operated by the waiter.
- **My Open Sessions**: Tracks active dining sessions.
- **My Active Orders**: Lists in-progress draft/submitted orders.
- **Kitchen Ticket SLA Status**: Shows count of `PREPARING` and `READY` orders.
- **Sales & Commission**: Displays daily sales total and 5% estimated commission accrued.

## 2. POS Order Builder
- **Table & Customer Selection**: Select active table or takeaway walk-in mode; associate customer CRM profile.
- **Product Catalog Grid**: Filterable by categories; supports variants (sizes/types) and extra modifiers.
- **Cart & Server Pricing**: Calculates line totals, subtotal, 5% VAT, and grand total.
- **Dual Routing Dispatch**:
  - `Send to Kitchen (Direct)`: Routes order directly to kitchen stations (`SUBMITTED`).
  - `Send to Reception (Pay-First)`: Routes order for pay-first settlement (`WAITING_PAYMENT`).

## 3. Floor Map & Table Operations
- **Interactive Table Grid**: Color-coded table states (Available, Occupied, Reserved, Cleaning).
- **Session Seating**: 1-click open session modal (`DiningSessionEngine::openSession`).
- **Table Transfer**: Transfer session and order items to an available table.

## 4. Kitchen Status & Ready Orders
- **Live Ticket Feed**: Real-time monitor of order ticket statuses.
- **Ready Pickup Banner**: Highlights `READY` orders with 1-click `Mark Served` button.

## 5. Sales Performance & Commission
- **Performance Matrix**: Total sales volume, paid sales, total orders, average order value, commission rate (5%), accrued commission breakdown (Pending, Approved, Paid).
