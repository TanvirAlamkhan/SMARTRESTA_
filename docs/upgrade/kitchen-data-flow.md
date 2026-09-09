# SMARTRESTA — Kitchen/KDS Data Flow & Cross-Dashboard Integration

## Single Shared Order Lifecycle
```text
  WAITER PORTAL                             KITCHEN / KDS PORTAL                       RECEPTION PORTAL
┌────────────────────────┐                ┌────────────────────────┐                 ┌────────────────────────┐
│ Create Order #1001     │                │ Live Ticket Queue      │                 │ Active Bills           │
│ Table 12               │                │ Ticket TKT-GRILL-001   │                 │ Order #1001            │
│ Status: SUBMITTED      │                │ Status: NEW            │                 │ Status: READY          │
└───────────┬────────────┘                └───────────┬────────────┘                 └───────────┬────────────┘
            │                                         │                                          │
            ▼                                         ▼                                          ▼
  RoutingEngine::routeOrder                 KDSEngine::updateTicketStatus              SmartBilling::settleOrder
            │                                         │                                          │
            ├─────────────────────────────────────────┼──────────────────────────────────────────┤
            │                                         │                                          │
            ▼                                         ▼                                          ▼
┌─────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│                                           MYSQL DATABASE                                                    │
│  orders (#1001) <---> order_items <---> order_routes <---> order_tickets (TKT-GRILL-001)                  │
│                                                               |                                             │
│                                                               ▼                                             │
│                                                   order_ticket_status_history                               │
│                                                               |                                             │
│                                                               ▼                                             │
│                                                    inventory_transactions                                   │
└─────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

## Step-by-Step Data Flow
1. **Order Creation**: Waiter creates Order `#1001` for Table 12. `OrderEngine` inserts `orders` & `order_items`.
2. **Station Routing**: `RoutingEngine` splits order items into station routes (`Grill`, `Pizza`, `Bar`) and creates corresponding entries in `order_routes` and `order_tickets`.
3. **Kitchen Receipt**: Kitchen KDS auto-polls `/api/v1/kds/tickets.php` every 10 seconds. New ticket `TKT-GRILL-001` appears in the `NEW` column on the Kanban board with audio chime.
4. **Accept & Preparation**: Kitchen cook clicks `[START]`. `KDSEngine::updateTicketStatus` locks `order_tickets` row, transitions status to `PREPARING`, records `started_at = NOW()`, inserts an entry into `order_ticket_status_history`, and updates `orders.order_status = 'PREPARING'`.
5. **Completion**: Cook clicks `[MARK READY]`. Status transitions to `READY`, `ready_at = NOW()`. `KDSEngine` checks if all station tickets for Order `#1001` are ready; if so, updates `orders.order_status = 'READY'`. Automatic recipe ingredient consumption is executed via `InventoryEngine::consumeForTicket`.
6. **Cross-Dashboard Visibility**:
   - **Waiter**: Sees notification badge that Table 12 Order `#1001` is READY for pickup.
   - **Reception**: Sees Order `#1001` ready for billing & payment settlement.
   - **Manager**: Sees live SLA preparation time, station workload, and completed ticket metrics.
   - **Admin**: Views complete order lifecycle and audit logs.
