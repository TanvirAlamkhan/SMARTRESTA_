# SMARTRESTA — Cross-Dashboard Shared Data Flow

```text
WAITER                      KITCHEN                     RECEPTION                   MANAGER / ADMIN
┌─────────────────────┐     ┌─────────────────────┐     ┌─────────────────────┐     ┌─────────────────────┐
│ 1. Open Session     │     │                     │     │                     │     │                     │
│ 2. Create Order     │     │                     │     │                     │     │                     │
│ 3. Add Line Items   │     │                     │     │                     │     │                     │
│ 4. Submit Order ────┼────►│ 5. Receive Ticket   │     │                     │     │                     │
│    (Status: SUBMIT) │     │ 6. Start (PREPARING)│     │                     │     │                     │
│                     │     │ 7. Mark READY ──────┼────►│ 8. Fetch Order Bill │     │                     │
│ 9. See READY Status │◄────┼─────────────────────┤     │ 9. Process Payment  │     │                     │
│                     │     │                     │     │    (Status: PAID) ──┼────►│ 10. Real-time KPIs  │
│                     │     │                     │     │ 10. Issue Receipt   │     │ 11. Commission Log  │
│                     │     │                     │     │                     │     │ 12. Stock Deduction │
│                     │     │                     │     │                     │     │ 13. Audit Entry     │
└─────────────────────┘     └─────────────────────┘     └─────────────────────┘     └─────────────────────┘
```

## Shared Data Records Throughout Lifecycle
- **Single Order Record (`orders`)**: Identified by `$orderId` and `$orderNumber`. Shared across all 5 portals. No portal creates a duplicate order copy.
- **Line Items (`order_items`)**: Exact snapshot of product, variant, unit price, quantity, and modifier snapshots.
- **Station Tickets (`order_tickets`)**: Created by `RoutingEngine` for `stations` (Grill, Pizza, Bar, etc.). References parent `orders.id`.
- **Status History (`order_status_history`)**: Tracks every status movement with user ID, timestamp, and audit notes.
- **Payments (`payments`)**: Settlement record linked to `orders.id` via `payment_allocations`.
- **Commission (`commission_transactions`)**: Auto-generated for order waiter upon payment settlement.
- **Inventory (`inventory_transactions`)**: Auto-deducted based on product recipe ingredients upon preparation completion.
- **Audit Logs (`audit_logs`)**: Immutable event record containing user ID, action, module, record ID, IP address, and payload diffs.
