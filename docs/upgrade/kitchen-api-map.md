# SMARTRESTA — Kitchen/KDS API Map

| Method | Endpoint | Description | Permission | Request Payload / Params | Response Data |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `GET` | `/api/v1/kds/tickets.php` | Get active station tickets queue & header counters | `kds.view` | `station_id`, `status`, `priority` | `{ tickets: [...], counters: {...} }` |
| `POST` | `/api/v1/kds/tickets.php` | Update ticket status (`PREPARING`, `READY`, `SERVED`) | `kds.manage` / `kds.start` / `kds.ready` | `{ ticket_id: 101, status: "READY", reason: "" }` | `{ success: true, ticket_id: 101, to_status: "READY" }` |
| `POST` | `/api/v1/kds/recall.php` | Recall `READY`/`SERVED` ticket back to production | `kds.recall` | `{ ticket_id: 101, reason: "Waiter recall" }` | `{ success: true, status: "PREPARING" }` |
| `POST` | `/api/v1/kds/refire.php` | Re-fire order item for remake as URGENT ticket | `kds.refire` | `{ ticket_id: 101, reason: "Cold food remake" }` | `{ success: true, refire_ticket_number: "..." }` |
| `POST` | `/api/v1/kds/cancel.php` | Cancel active station ticket | `kds.cancel` | `{ ticket_id: 101, reason: "Customer cancelled" }` | `{ success: true, status: "CANCELLED" }` |
| `GET` | `/api/v1/kds/history.php` | Fetch ticket status transition history log | `kds.view` | `ticket_id` | `{ ticket: {...}, history: [...] }` |
| `GET` | `/api/v1/kds/stations.php` | List active kitchen stations & pause status | `kds.view` | N/A | `[ { id: 1, name: "Grill", is_paused: 0 }, ... ]` |
| `POST` | `/api/v1/kds/stations.php` | Manage station status (pause/resume/create) | `kds.manage` | `{ action: "pause", station_id: 1, reason: "Busy" }` | `{ success: true, status: "PAUSED" }` |
