# SMARTRESTA — Reception Data Flow (Prompt 05)

## 1. End-to-End Operational Order & Payment Lifecycle

```text
[ WAITER PORTAL ]
  │
  ├── Opens Table 12 -> DiningSessionEngine::openSession()
  │     └── Inserts into `dining_sessions` (Status: OPEN), Updates `restaurant_tables` (Status: OCCUPIED)
  │
  ├── Creates Order #1001 -> OrderEngine::createDraftOrder()
  │     └── Inserts into `orders` (payment_status: UNPAID) & `order_items`
  │
  └── Submits Order -> RoutingEngine::routeOrderItems()
        └── Inserts into `order_routes` & `order_tickets`

[ KITCHEN KDS PORTAL ]
  │
  ├── Receives Ticket -> KDSEngine::updateTicketStatus()
  │     └── Updates `order_tickets` (PREPARING -> READY -> SERVED)
  │
  └── Order status updated to SERVED

[ RECEPTION / CASHIER PORTAL ]
  │
  ├── Search Order #1001 / Select Table 12 -> BillingEngine::calculateOrderBill(1001)
  │     └── Fetches subtotal, calculates 5% VAT, discounts, service charge, and outstanding balance
  │
  ├── Selects Payment Method (e.g. bKash / Cash) -> PaymentEngine::processPayment()
  │     ├── Locks order row FOR UPDATE (Database Transaction)
  │     ├── Inserts payment record into `payments` table
  │     ├── Inserts allocation record into `payment_allocations` table
  │     ├── Updates order payment status to PAID in `orders` table
  │     ├── Auto-generates receipt via ReceiptEngine into `receipts` table
  │     └── Triggers AuditLogger::log('PAYMENT_CREATED')
  │
  └── Prints Thermal Receipt -> ReceiptEngine::getReceipt()

[ MANAGER & ADMIN PORTALS ]
  │
  └── View Sales & Financial Reports -> ReportEngine::getOverviewDashboard()
        └── Queries same `payments` and `orders` database tables (Instant live update)
```

---

## 2. Database Tables Scope for Reception Operations

The Reception portal interacts directly with the following MySQL tables:

1. `orders` (Order header, status, totals, payment status)
2. `order_items` (Line items, quantities, prices, subtotals)
3. `dining_sessions` (Active table sessions, guest count, timestamps)
4. `restaurant_tables` (Table numbers, capacity, operational status)
5. `payment_methods` (Active payment options: Cash, bKash, Nagad, Card)
6. `payments` (Payment transaction records, reference numbers, user ID)
7. `payment_allocations` (Order-to-payment amount mapping)
8. `receipts` (Receipt number, JSON snapshot data, timestamp)
9. `refunds` (Refund number, payment ID, amount, reason, audit user ID)
10. `customers` (Customer profiles, contact numbers, notes)
11. `reservations` (Table reservations, guest counts, requested time)
12. `shifts` (Cashier shift start/end times, opening cash, closing cash)
13. `audit_logs` (Immutable system security audit records)
