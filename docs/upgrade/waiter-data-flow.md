# SMARTRESTA Waiter Portal Data Flow

## 1. Single Order Lifecycle Across Portals
```text
WAITER PORTAL
  │  (Creates Order #ORD-1001 on Table 4)
  ▼
OrderEngine :: createDraftOrder()
  │  (Inserts into `orders`, status = DRAFT)
  ▼
OrderEngine :: addItemToOrder()
  │  (Inserts line items & modifier snapshots into `order_items` & `order_item_modifiers`)
  ▼
OrderEngine :: submitOrder()
  │  (Updates status = SUBMITTED, triggers RoutingEngine)
  ├───────────────────────────────┬───────────────────────────────┐
  ▼                               ▼                               ▼
KITCHEN (KDS)               RECEPTION                     MANAGER & ADMIN
KDS Ticket #ORD-1001        Order #ORD-1001               Order #ORD-1001
  │                         Bill Settlement & Receipt       Live Dashboard
  ▼                               ▼                               ▼
KDS Preparing → READY       Payment Processed (PAID)       Sales & Commission
  │                               │                        Accrued (5%)
  ▼                               ▼
WAITER SEES READY           SESSION CLOSED
Marks SERVED                Table set AVAILABLE
```

## 2. Shared Entities & MySQL Tables
- `orders`: Shared single source of truth for order number, totals, status, and payment state.
- `order_items`: Line item snapshots including server-calculated pricing, SKU, and station routing code.
- `dining_sessions`: Tracks open table sessions, guest count, seating timestamp, and close timestamp.
- `restaurant_tables`: Stores operational table status (`AVAILABLE`, `OCCUPIED`, `RESERVED`, `CLEANING`).
- `commission_transactions`: Accrued commission records calculated by `CommissionEngine`.
- `audit_logs`: Audit trail for all order and session state transitions.
