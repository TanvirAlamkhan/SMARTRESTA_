# SMARTRESTA — Cross-Dashboard Integration Architecture

## Overview
SMARTRESTA provides a single unified restaurant operating system serving five distinct portal personas:
- `/admin/` — Master Administration & Operations Engine
- `/manager/` — Store Manager Analytics & Approvals Dashboard
- `/reception/` — Front Desk Reception, Billing & Cashier Desk
- `/waiter/` — Table Ordering & Dining Session Operator
- `/kitchen/` — Kitchen Display System (KDS) & Multi-Station Production Board

## Central Unified Architecture
All five portals operate on the **same MySQL database**, **same central PHP business engines**, **same REST API contracts**, and **same RBAC server-side authorization controls**.

```text
                         AUTH + RBAC
                              │
          ┌───────────────────┼───────────────────┐
          │                   │                   │
       PORTALS            SHARED APIs       SECURITY
          │                   │                   │
 ┌────────┼────────┬──────────┼────────┐          │
 │        │        │          │        │          │
ADMIN  MANAGER  RECEPTION  WAITER   KITCHEN      │
 │        │        │          │        │          │
 └────────┴────────┴──────────┴────────┘          │
                     │
             BUSINESS ENGINES
                     │
                  MYSQL
                     │
       ┌─────────────┼─────────────┐
       │             │             │
    ORDERS       PAYMENTS      OPERATIONS
       │             │             │
   INVENTORY     FINANCE      COMMISSION
       │             │             │
       └─────────────┼─────────────┘
                     │
                AUDIT LOGS
```

## Central Core Business Engines
1. `Auth.php` & `Router.php`: Session authentication, role normalization, portal authorization, and HTTP 403 route guards.
2. `OrderEngine.php`: Order number generation, draft creation, item addition, line calculation, recalculation, and status advancement.
3. `RoutingEngine.php`: Deterministic item station resolution, station ticket creation (`order_tickets`), item routing status updates.
4. `KDSEngine.php`: Concurrency row locking (`FOR UPDATE`), status transitions (`NEW` -> `PREPARING` -> `READY` -> `SERVED`), ticket recall/refire/cancellation, and inventory consumption triggers.
5. `BillingEngine.php` & `PaymentEngine.php`: Server-authoritative bill calculation, split payment allocations, idempotency key validation, overpayment guards, and receipt generation.
6. `CommissionEngine.php`: Rule resolution, waiter sales commission calculation, idempotency validation, refund adjustment processing, and batch approvals.
7. `InventoryEngine.php`: Recipe ingredient consumption upon production completion, stock transaction logging, and purchase order tracking.
8. `AuditLogger.php`: Immutable audit logging for all compliance-sensitive state changes.
