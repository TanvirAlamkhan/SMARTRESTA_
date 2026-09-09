# SMARTRESTA — Database Data-Flow Audit & Schema Map

## 1. Relational Database Overview

SMARTRESTA uses a MySQL 8.x InnoDB database (`smartresta_db`) with 35 tables, strict foreign keys, foreign key cascade/restrict rules, and UTF-8 collation (`utf8mb4_unicode_ci`).

The database schema is defined in [database/schema.sql](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/database/schema.sql) and [database/railway_deploy_master.sql](file:///d:/Saas%20Development%20project/WEBSITE/SMARTRESTA/database/railway_deploy_master.sql).

---

## 2. Complete Database Table Map (35 Tables)

### RBAC & User Management
1. `roles` (id, name, description)
2. `permissions` (id, name, module, description)
3. `role_permissions` (role_id, permission_id)
4. `users` (id, role_id, name, email, phone, password_hash, role, status, last_login)

### Restaurant Structure & Layout
5. `branches` (id, name, code, address, phone, status)
6. `floors` (id, branch_id, name, sort_order)
7. `stations` (id, branch_id, name, badge_code, status)
8. `restaurant_tables` (id, floor_id, table_number, capacity, status, is_active)
9. `dining_sessions` (id, table_id, customer_id, opened_by_user_id, guest_count, status, opened_at, closed_at)

### Menu & Product Catalog
10. `categories` (id, name, slug, description, sort_order, status)
11. `products` (id, category_id, default_station_id, name, sku, price, cost_price, is_available, image_url)
12. `product_variants` (id, product_id, variant_name, price_adjustment)
13. `modifiers` (id, name, price, status)
14. `product_modifiers` (product_id, modifier_id)

### Orders, Routing & KDS
15. `orders` (id, order_number, dining_session_id, table_id, customer_id, taken_by_user_id, served_by_user_id, order_type, subtotal, discount, tax, service_charge, total, payment_status, order_status, notes)
16. `order_items` (id, order_id, product_id, counter_id, item_name, quantity, unit_price, subtotal, status, notes)
17. `order_status_history` (id, order_id, previous_status, new_status, changed_by_user_id, notes)
18. `order_routes` (id, order_id, station_id, routing_mode)
19. `order_tickets` (id, order_id, station_id, ticket_number, status)

### Payments & Billing
20. `payment_methods` (id, name, code, is_active)
21. `payments` (id, order_id, payment_method_id, amount, transaction_reference, received_by_user_id, status)
22. `refunds` (id, payment_id, order_id, refund_amount, reason, processed_by_user_id)

### Waiters & Commissions
23. `waiter_assignments` (id, waiter_id, table_id, assigned_at, unassigned_at)
24. `commission_rules` (id, name, calculation_base, rate, is_active)
25. `commission_transactions` (id, order_id, waiter_id, commission_rule_id, base_amount, rate, commission_amount, status, approved_by_user_id, approved_at)

### CRM & Reservations
26. `customers` (id, name, phone, email, status)
27. `reservations` (id, customer_id, table_id, guest_count, reservation_time, status, notes)

### Inventory & Stock
28. `ingredients` (id, name, unit, current_stock, min_stock, cost_per_unit)
29. `recipes` (id, product_id, ingredient_id, quantity_required)
30. `inventory_transactions` (id, ingredient_id, type, quantity, reference_id, notes)

### Finance & Shifts
31. `expense_categories` (id, name)
32. `expenses` (id, category_id, amount, title, recorded_by_user_id)
33. `shifts` (id, user_id, opened_at, closed_at, opening_balance, closing_balance, status)

### Audit & System Settings
34. `audit_logs` (id, user_id, action, module, record_id, old_value, new_value, ip_address, user_agent)
35. `settings` (setting_key, setting_value, description)

---

## 3. Authoritative Single-Record Principle

```text
                                  ┌────────────────────────┐
                                  │   MySQL Database       │
                                  │   orders & order_items │
                                  └───────────┬────────────┘
                                              │
         ┌──────────────────┬─────────────────┼─────────────────┬──────────────────┐
         ▼                  ▼                 ▼                 ▼                  ▼
   Waiter Portal     Kitchen Portal    Reception Portal  Manager Portal      Admin Portal
   Creates Order     Reads & Updates   Settle Payment    Analytics & KPI     Full Audit Log
   (takes order)     Ticket Status     (processes bill)  & Reports           & System Oversight
```

**Critical Guarantee**: Order #1001 exists as a single row in `orders` and `order_items`. No portal maintains an isolated order copy.
