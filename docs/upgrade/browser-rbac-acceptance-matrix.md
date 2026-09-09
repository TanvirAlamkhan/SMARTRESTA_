# SMARTRESTA — Browser RBAC Acceptance Matrix

| Role | Target Portal | Direct URL Access | UI View Access | API Permission Guard | Server Response | Result |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| **Admin** | `/admin/` | ALLOWED | Full Workspace View | Full Access (`isFullAccess: true`) | HTTP 200 | PASS |
| **Admin** | `/manager/` | ALLOWED | Full Workspace View | Full Access (`isFullAccess: true`) | HTTP 200 | PASS |
| **Admin** | `/reception/` | ALLOWED | Full Workspace View | Full Access (`isFullAccess: true`) | HTTP 200 | PASS |
| **Admin** | `/waiter/` | ALLOWED | Full Workspace View | Full Access (`isFullAccess: true`) | HTTP 200 | PASS |
| **Admin** | `/kitchen/` | ALLOWED | Full Workspace View | Full Access (`isFullAccess: true`) | HTTP 200 | PASS |
| **Manager** | `/manager/` | ALLOWED | Manager Workspace View | `reports.view`, `inventory.manage` | HTTP 200 | PASS |
| **Manager** | `/admin/` | DENIED | Hidden from Navigation | `Router::authorizePortal('admin')` | HTTP 403 Forbidden | PASS |
| **Reception** | `/reception/` | ALLOWED | Front Desk Workspace | `payments.create`, `billing.view` | HTTP 200 | PASS |
| **Reception** | `/admin/` | DENIED | Hidden from Navigation | `Router::authorizePortal('admin')` | HTTP 403 Forbidden | PASS |
| **Waiter** | `/waiter/` | ALLOWED | Waiter Floor & POS View | `orders.create`, `orders.submit` | HTTP 200 | PASS |
| **Waiter** | `/reception/` | DENIED | Hidden from Navigation | `Router::authorizePortal('reception')` | HTTP 403 Forbidden | PASS |
| **Kitchen** | `/kitchen/` | ALLOWED | KDS Workspace View | `kds.view`, `kds.start`, `kds.ready` | HTTP 200 | PASS |
| **Kitchen** | `/waiter/` | DENIED | Hidden from Navigation | `Router::authorizePortal('waiter')` | HTTP 403 Forbidden | PASS |
