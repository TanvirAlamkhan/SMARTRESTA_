# SMARTRESTA — API Database Persistence Matrix

| API Endpoint | HTTP Method | Core Engine | MySQL Tables Read | MySQL Tables Written | Transactional? | Audit Logged? |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `/api/v1/orders/index.php` | `GET` | `OrderEngine` | `orders`, `restaurant_tables`, `users` | None | No | No |
| `/api/v1/orders/index.php` | `POST` | `OrderEngine` | `dining_sessions`, `restaurant_tables` | `orders`, `order_status_history` | YES | YES (`ORDER_CREATED`) |
| `/api/v1/orders/items.php` | `POST` | `OrderEngine` | `orders`, `products`, `product_variants`, `modifiers` | `order_items`, `order_item_modifiers`, `orders` | YES | YES (`ORDER_ITEM_ADDED`) |
| `/api/v1/orders/submit.php` | `POST` | `OrderEngine`, `RoutingEngine` | `orders`, `order_items`, `products`, `stations` | `orders`, `order_items`, `order_routes`, `order_tickets`, `order_ticket_items` | YES | YES (`ORDER_SUBMITTED`, `ROUTE_CREATED`) |
| `/api/v1/kds/tickets.php` | `GET` | `KDSEngine` | `order_tickets`, `order_ticket_items`, `stations`, `orders` | None | No | No |
| `/api/v1/kds/tickets.php` | `POST` | `KDSEngine` | `order_tickets`, `order_ticket_items`, `recipes`, `ingredients` | `order_tickets`, `order_ticket_items`, `order_ticket_status_history`, `orders`, `inventory_transactions` | YES | YES (`TICKET_STATUS_UPDATED`) |
| `/api/v1/billing/index.php` | `GET` | `BillingEngine` | `orders`, `order_items`, `order_item_modifiers`, `payments` | None | No | No |
| `/api/v1/payments/index.php` | `POST` | `PaymentEngine` | `orders`, `payment_methods`, `waiter_profiles`, `commission_rules` | `payments`, `payment_allocations`, `receipts`, `orders`, `commission_transactions` | YES | YES (`PAYMENT_CREATED`, `COMMISSION_CREATED`) |
| `/api/v1/commissions/index.php` | `POST` | `CommissionEngine` | `commission_transactions` | `commission_transactions` | YES | YES (`COMMISSION_APPROVED`) |
| `/api/v1/inventory/index.php` | `POST` | `InventoryEngine` | `ingredients`, `branches` | `ingredients`, `inventory_transactions` | YES | YES (`INVENTORY_ADJUSTED`) |
