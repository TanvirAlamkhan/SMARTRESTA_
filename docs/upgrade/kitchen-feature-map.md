# SMARTRESTA — Kitchen/KDS Feature Map

| Feature Area | Component / Endpoint | Description | Status |
| :--- | :--- | :--- | :--- |
| **KDS Live Board** | `views/kitchen.php`, `assets/js/kitchen.js` | 4-column visual Kanban queue (`NEW`, `PREPARING`, `READY`, `SERVED`) with live timer tickers, priority badges, and item details. | IMPLEMENTED |
| **Ticket Status Transitions** | `api/v1/kds/tickets.php`, `KDSEngine::updateTicketStatus` | Controlled state machine transitions (`NEW` -> `PREPARING` -> `READY` -> `SERVED`) with concurrency locking & audit trail. | IMPLEMENTED |
| **Ticket Recall** | `api/v1/kds/recall.php`, `KDSEngine::recallTicket` | Recalls accidentally completed/ready tickets back to `PREPARING` or `READY` with mandatory reason. | IMPLEMENTED |
| **Ticket Re-fire** | `api/v1/kds/refire.php`, `KDSEngine::refireTicket` | Re-fires an order item for remake as an `URGENT` ticket with refire counter tracking. | IMPLEMENTED |
| **Ticket Cancellation** | `api/v1/kds/cancel.php`, `KDSEngine::cancelTicket` | Cancels active kitchen tickets with mandatory audit reason logging. | IMPLEMENTED |
| **Station Workload & Pause** | `api/v1/kds/stations.php`, `KDSEngine::manageStation` | View active station status, pause overloaded stations, resume paused stations, or create new stations. | IMPLEMENTED |
| **SLA Delay Monitoring** | `views/kitchen.php`, `KDSEngine` | Live SLA tracking alerting on tickets exceeding target preparation window (15m threshold). | IMPLEMENTED |
| **Station Filter & Isolation** | `views/kitchen.php`, `assets/js/kitchen.js` | Real-time station tab filter (`All Stations`, `Grill`, `Pizza`, `Bar`, `Dessert`, `Counter`). | IMPLEMENTED |
| **Ticket Status Audit Log** | `api/v1/kds/history.php`, `KDSEngine::getTicketHistory` | Detailed historical log of status changes, users, timestamps, and transition reasons per ticket. | IMPLEMENTED |
| **Inventory Connection** | `KDSEngine::updateTicketStatus`, `InventoryEngine` | Triggers automatic recipe ingredient consumption when tickets transition to `READY` or `SERVED`. | IMPLEMENTED |
