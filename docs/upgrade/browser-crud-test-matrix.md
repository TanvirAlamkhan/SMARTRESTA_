# SMARTRESTA — Browser CRUD Test Matrix

| Module | Entity | Create | Read | Update | Delete / Cancel | Persistence Test | Result |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| **Users & Staff** | `users` | Admin modal form -> `/api/v1/users/` | Data table with role badge | Password reset & role update | Deactivate user account | Reload page & verify MySQL `users` row | PASS |
| **Floor & Tables** | `restaurant_tables` | Floor map modal -> `/api/v1/tables/` | Interactive floor layout grid | Status toggle (`FREE` <-> `OCCUPIED`) | Delete unassigned table | Reload page & verify `restaurant_tables` | PASS |
| **Menu & Products** | `products`, `modifiers` | Add product modal -> `/api/v1/products/` | Product catalog grid | Edit price/category/modifiers | Soft delete (`deleted_at`) | Reload page & verify `products` row | PASS |
| **Dining Sessions** | `dining_sessions` | Waiter table click -> Open Session | Active session info card | Update guest count / table | Close session on settlement | Reload page & verify `dining_sessions` | PASS |
| **Orders** | `orders`, `order_items` | POS cart -> `/api/v1/orders/create.php` | Order drawer & history table | Add/remove line items | Cancel draft order | Reload page & verify `orders` & `order_items` | PASS |
| **Kitchen Tickets** | `order_tickets` | Auto-dispatched on order submission | KDS 4-column Kanban board | `NEW` -> `PREPARING` -> `READY` | Cancel ticket with reason | Reload page & verify `order_tickets` | PASS |
| **Payments** | `payments` | Settlement modal -> `/api/v1/payments/` | Payment transaction log | Split payment allocation | Issue refund with audit note | Reload page & verify `payments` row | PASS |
| **Inventory** | `ingredients` | Receive stock modal -> `/api/v1/inventory/` | Ingredients stock list | Adjust stock levels | Archive ingredient | Reload page & verify `inventory_transactions` | PASS |
| **Expenses** | `expenses` | Add expense modal -> `/api/v1/expenses/` | Financial expenses log | Update category/amount | Void expense entry | Reload page & verify `expenses` row | PASS |
| **Reservations** | `reservations` | Book reservation -> `/api/v1/crm/` | Reservation schedule calendar | Confirm / reschedule | Cancel reservation | Reload page & verify `reservations` row | PASS |
