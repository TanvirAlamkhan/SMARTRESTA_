# SMARTRESTA Waiter Portal Architecture

## Overview
The Waiter Portal (`/waiter/` & `/waiter/index.php`) provides floor waitstaff with a high-performance, real-time operational workspace for table seating, POS order building, variant/modifier selection, dual kitchen & reception dispatching, KDS status tracking, and commission management.

## Layered Architecture
```text
┌───────────────────────────────────────────────────────────┐
│                      WAITER PORTAL UI                     │
│    views/waiter.php • assets/js/waiter.js • SmartPOS     │
└─────────────────────────────┬─────────────────────────────┘
                              │ HTTP REST / JSON
┌─────────────────────────────▼─────────────────────────────┐
│                       SERVER APIS                         │
│  api/v1/orders/  api/v1/dining_sessions/  api/v1/waiters/ │
│  api/v1/tables/  api/v1/kds/  api/v1/crm/  api/v1/billing │
└─────────────────────────────┬─────────────────────────────┘
                              │
┌─────────────────────────────▼─────────────────────────────┐
│                    BUSINESS ENGINE LAYER                  │
│  OrderEngine  DiningSessionEngine  RoutingEngine  KDSEngine│
│  BillingEngine  CommissionEngine  CRMEngine  WaiterEngine │
└─────────────────────────────┬─────────────────────────────┘
                              │ PDO Prepared Statements
┌─────────────────────────────▼─────────────────────────────┐
│                    MYSQL DATABASE LAYER                   │
│  orders  order_items  dining_sessions  restaurant_tables  │
│  order_routes  order_tickets  commission_transactions     │
└───────────────────────────────────────────────────────────┘
```

## Security & Route Authorization
- Access point `/waiter/index.php` requires authenticated session via `Auth::requireAuth()`.
- Route guard `Router::authorizePortal('waiter')` verifies the user holds `waiter`, `manager`, or `admin` role. Unauthorized roles receive standard 403 Forbidden.
