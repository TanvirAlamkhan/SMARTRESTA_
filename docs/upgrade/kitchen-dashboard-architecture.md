# SMARTRESTA — Kitchen/KDS Portal Dashboard Architecture

## Overview
The Kitchen Display System (KDS) and Production Workflow portal (`/kitchen/` and `/kitchen/index.php`) provides real-time ticket display, station queue management, ticket status transitions (`NEW` -> `PREPARING` -> `READY` -> `SERVED`), SLA delay tracking, and ticket recall/refire capabilities for kitchen and counter production staff.

## Key Architectural Components
1. **Server-Side Access Control**: Protected via `Router::authorizePortal('kitchen')`, enforcing strict RBAC permissions (`kds.view`, `kds.manage`, `kds.start`, `kds.ready`, `kds.served`, `kds.recall`, `kds.refire`, `kds.cancel`).
2. **Dedicated Workspace View**: Implemented in `views/kitchen.php` featuring live status KPI cards, 4-column production Kanban board (`NEW`, `PREPARING`, `READY`, `SERVED`), SLA delay monitor, station pause/resume management, and ticket audit history table.
3. **Frontend Controller (`assets/js/kitchen.js`)**: Wraps and extends `SmartKDS`, managing auto-refresh cycles, SLA timer tickers, chime sound alerts, modal dialogs for recall/refire/cancellation reasons, and station pause/resume operations.
4. **Backend KDS Engine (`core/KDSEngine.php`)**: Manages ticket locking, idempotent status transitions, status history logging (`order_ticket_status_history`), recipe-based inventory consumption upon completion (`InventoryEngine::consumeForTicket`), parent order status synchronization (`syncOrderReadiness`), and station operational state changes.
5. **Database Integration**: Utilizes `order_tickets`, `order_ticket_items`, `order_ticket_status_history`, `stations`, `orders`, `restaurant_tables`, and `inventory_transactions`.
