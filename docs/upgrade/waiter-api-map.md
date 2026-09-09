# SMARTRESTA Waiter Portal API Map

## REST API Endpoints Consumed by Waiter Portal

| Endpoint | Method | Permission Required | Description |
| :--- | :--- | :--- | :--- |
| `/api/v1/orders/index.php` | `GET` | `orders.view` | Retrieve active & historical orders for waiter |
| `/api/v1/orders/index.php` | `POST` | `orders.create` | Initialize draft order (`OrderEngine::createDraftOrder`) |
| `/api/v1/orders/items.php` | `POST` | `order_items.manage` | Add product with variants/modifiers to order |
| `/api/v1/orders/submit.php` | `POST` | `orders.submit` | Submit draft order for kitchen routing |
| `/api/v1/orders/status.php` | `POST` | `orders.change_status` | Update order status (SERVED, WAITING_PAYMENT) |
| `/api/v1/tables/index.php` | `GET` | `tables.view` | Fetch floor tables and status |
| `/api/v1/dining_sessions/index.php` | `GET` | `dining_sessions.view` | List open dining sessions |
| `/api/v1/dining_sessions/open.php` | `POST` | `dining_sessions.create` | Open new dining session (`DiningSessionEngine::openSession`) |
| `/api/v1/dining_sessions/transfer.php` | `POST` | `dining_sessions.transfer` | Transfer session to another table |
| `/api/v1/dining_sessions/close.php` | `POST` | `dining_sessions.close` | Close dining session |
| `/api/v1/kds/index.php` | `GET` | `kds.view` | Fetch active kitchen tickets |
| `/api/v1/waiters/index.php` | `GET` | `waiters.view` | Fetch waiter performance & commission matrix |
| `/api/v1/crm/index.php` | `GET` | `customers.view` | Fetch customer CRM profiles |
