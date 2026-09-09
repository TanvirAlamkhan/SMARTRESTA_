<?php
/**
 * SMARTRESTA System Permissions Catalog & Default Role Mapping
 * Prompt 04 Extension: Branches, Floors, Tables, and Dining Sessions
 */

define('PERMISSIONS_CATALOG', [
    // Dashboard & Analytics
    'dashboard.view' => 'View operational dashboard & metrics',

    // User & Staff Management
    'users.view' => 'View user accounts list',
    'users.create' => 'Create new staff user accounts',
    'users.update' => 'Update user account details & status',
    'users.delete' => 'Deactivate or soft-delete user accounts',
    'users.reset_password' => 'Reset staff user passwords',

    // Role & Permission Management
    'roles.view' => 'View roles and permission matrix',
    'roles.manage' => 'Manage role permission assignments',

    // Branch & Floor Management
    'branches.view' => 'View restaurant branches',
    'branches.manage' => 'Create and configure restaurant branches',
    'floors.view' => 'View restaurant floors',
    'floors.manage' => 'Create and order restaurant floor zones',

    // Table Management & Floor Operations
    'tables.view' => 'View restaurant floor tables & floor map',
    'tables.create' => 'Create new dining tables',
    'tables.update' => 'Update table numbers, capacity, or shape',
    'tables.manage' => 'Manage table layout and floor maps',
    'tables.status_update' => 'Update table status (Cleaning, Out of Service)',

    // Atomic Dining Sessions & Table Operations
    'dining_sessions.view' => 'View active dining sessions',
    'dining_sessions.create' => 'Open new dining session on a table',
    'dining_sessions.transfer' => 'Transfer active session to another table',
    'dining_sessions.close' => 'Close active dining session',

    // Menu & Product Management
    'menu.view' => 'View menu product catalog',
    'menu.manage' => 'Create, edit, or disable menu products and modifiers',
    'menus.view' => 'View restaurant menus',
    'menus.manage' => 'Create and configure restaurant menus',
    'categories.view' => 'View product categories',
    'categories.manage' => 'Create, edit, or reorder product categories',
    'products.view' => 'View product catalog and pricing',
    'products.create' => 'Create new restaurant products',
    'products.update' => 'Update product metadata, pricing, and stations',
    'products.delete' => 'Deactivate or soft-delete products',
    'products.manage_availability' => 'Toggle real-time product availability (Available/Unavailable)',
    'variants.manage' => 'Manage product size/type variants and pricing',
    'modifiers.manage' => 'Manage extra modifiers and product associations',

    // Order Engine
    'orders.view' => 'View live customer orders',
    'orders.create' => 'Take and submit customer orders',
    'orders.update' => 'Modify order items and details',
    'orders.submit' => 'Submit draft order for station routing',
    'orders.change_status' => 'Advance or change order operational status',
    'orders.cancel' => 'Cancel customer orders',
    'order_items.manage' => 'Add, update, or remove line items from order',

    // Station Item Routing & Kitchen (KDS)
    'kitchen.view' => 'View Kitchen Display System (KDS) tickets',
    'kitchen.manage' => 'Update kitchen ticket status (Preparing, Ready, Served)',
    'kds.view' => 'Access and view Kitchen Display System (KDS)',
    'kds.manage' => 'Manage KDS tickets and production workflow',
    'kds.start' => 'Start preparing station tickets',
    'kds.ready' => 'Mark station tickets as ready',
    'kds.served' => 'Mark ready tickets as served / picked up',
    'kds.cancel' => 'Cancel active station tickets',
    'kds.recall' => 'Recall ready/served tickets back to production',
    'kds.refire' => 'Re-fire production tickets for remake',
    'kds.override' => 'Override ticket status state machine checks',
    'kds.history.view' => 'View detailed ticket status audit history',
    'routing.view' => 'View station routing decisions and order dispatch',
    'routing.manage' => 'Manage order routing rules and policies',
    'routing.dispatch' => 'Dispatch submitted orders to target stations',
    'routing.override' => 'Override default station routing for order items',
    'routing.cancel' => 'Cancel station routes for an order',
    'routing.retry' => 'Retry failed order item routing',
    'stations.view' => 'View operational stations/counters',
    'stations.manage' => 'Create and configure operational stations',
    'stations.pause' => 'Pause operational stations for temporary breaks',
    'stations.activate' => 'Activate or resume paused operational stations',
    'tickets.view' => 'View station tickets',
    'tickets.manage' => 'Manage station ticket status',
    'tickets.reprint' => 'Reprint station tickets',

    // Reception & Payment Billing
    'payments.view' => 'View billing payment history',
    'payments.create' => 'Record new customer payments',
    'payments.process' => 'Process customer payments (Cash, bKash, Nagad, Card)',
    'payments.update' => 'Update payment reference details',
    'payments.void' => 'Void unprocessed customer payments',
    'payments.refund' => 'Process payment refunds',
    'payments.refund_approve' => 'Approve customer refund requests',
    'payments.reconcile' => 'Reconcile daily payment totals',
    'billing.view' => 'View order financial bills & balances',
    'billing.manage' => 'Manage discounts, service charges & bill totals',
    'receipts.view' => 'View payment receipts',
    'receipts.reprint' => 'Reprint payment receipts',
    'invoices.view' => 'View order invoices',
    'invoices.reprint' => 'Reprint order invoices',
    'payment_methods.view' => 'View active payment methods',
    'payment_methods.manage' => 'Configure restaurant payment methods',

    // Waiter & Commission Management
    'waiters.view' => 'View waiter profiles & performance matrix',
    'waiters.manage' => 'Manage waiter profiles, status & employee codes',
    'waiters.assign' => 'Assign waiters to tables, sessions & orders',
    'commissions.view' => 'View waiter commission calculations',
    'commissions.rules.manage' => 'Configure waiter commission rules & rates',
    'commissions.approve' => 'Approve or reject waiter commission transactions',
    'commissions.pay' => 'Process waiter commission payouts & settlements',

    // Inventory, Recipes, Purchasing & Stock
    'inventory.view' => 'View ingredient stock balances and low-stock alerts',
    'inventory.manage' => 'Manage inventory locations and stock settings',
    'inventory.adjust' => 'Perform manual stock adjustments (IN/OUT)',
    'inventory.wastage' => 'Record stock wastage and spoilage',
    'inventory.transfer' => 'Transfer stock between inventory locations',
    'inventory.history.view' => 'View immutable inventory transaction ledger',
    'ingredients.manage' => 'Create, edit, or disable ingredient master items',
    'recipes.manage' => 'Create and configure product recipe BOM items',
    'recipes.cost.view' => 'View calculated recipe cost and ingredient costs',
    'suppliers.manage' => 'Create and manage vendor suppliers',
    'purchases.manage' => 'Create, edit, or cancel purchase orders',
    'purchases.approve' => 'Approve submitted purchase orders',
    'purchases.receive' => 'Process goods receiving and stock updates',

    // Reports & Financial Analytics
    'reports.view' => 'View financial and operational reports',
    'reports.sales' => 'View detailed sales breakdown reports',
    'reports.orders' => 'View order performance and drill-down reports',
    'reports.waiters' => 'View waiter performance and commission reports',
    'reports.tables' => 'View table and floor session reports',
    'reports.products' => 'View product sales and recipe cost margin reports',
    'reports.payments' => 'View payment reconciliation and outstanding balance reports',
    'reports.kds' => 'View Kitchen Display System (KDS) SLA and station reports',
    'reports.inventory' => 'View inventory consumption, valuation and wastage reports',
    'reports.purchases' => 'View purchasing and vendor performance reports',
    'reports.export' => 'Export CSV financial reports',
    'reports.export.csv' => 'Export CSV reports',
    'reports.export.pdf' => 'Export PDF printable reports',

    // Finance, Expenses, Shifts, Cash & Day Closing
    'finance.view' => 'View finance center dashboard & totals',
    'finance.manage' => 'Manage overall financial operations',
    'expenses.view' => 'View operating expense records',
    'expenses.create' => 'Create new operating expenses',
    'expenses.update' => 'Update pending operating expenses',
    'expenses.approve' => 'Approve or reject operating expenses',
    'expenses.pay' => 'Process expense payments',
    'expenses.cancel' => 'Cancel operating expenses',
    'shifts.view' => 'View staff shift history and drawer balances',
    'shifts.open' => 'Open cashier shifts and enter opening cash',
    'shifts.close' => 'Close cashier shifts and record physical cash count',
    'shifts.reopen' => 'Reopen closed cashier shifts with audit reason',
    'cash.view' => 'View cash drawer balances and cash movements',
    'cash.manage' => 'Manage cash drawers and registers',
    'cash.in' => 'Record cash in (petty cash, change added)',
    'cash.out' => 'Record cash out (bank deposits, supplier payments)',
    'cash.adjust' => 'Record manual cash drawer adjustments',
    'reconciliation.view' => 'View payment reconciliation reports',
    'reconciliation.manage' => 'Manage payment reconciliation entries',
    'reconciliation.verify' => 'Verify payment method reconciliation totals',
    'settlements.view' => 'View mobile banking & card settlement records',
    'settlements.create' => 'Create settlement verification batches',
    'settlements.verify' => 'Verify settlement batches against POS records',
    'day_closing.view' => 'View business day closing history',
    'day_closing.review' => 'Run business day close pre-check audit',
    'day_closing.close' => 'Finalize business day closing and freeze snapshot',
    'day_closing.reopen' => 'Reopen closed business day with audit reason',
    'financial_adjustments.view' => 'View financial adjustment records',
    'financial_adjustments.create' => 'Create financial adjustments',
    'financial_adjustments.approve' => 'Approve financial adjustments',

    // System Settings & Audit
    'settings.view' => 'View restaurant system settings',
    'settings.update' => 'Update system settings & branch config',
    'audit.view' => 'View system audit logs',

    // CRM & Customer Lifecycle
    'customers.view' => 'View customer directory & profiles',
    'customers.create' => 'Create new customer profile',
    'customers.update' => 'Update customer information',
    'customers.manage' => 'Manage customer status & preferences',
    'customers.delete' => 'Soft delete or deactivate customer profile',

    // Table Reservations Engine
    'reservations.view' => 'View table reservations calendar & list',
    'reservations.create' => 'Create new table reservation',
    'reservations.update' => 'Update reservation details and assigned table',
    'reservations.confirm' => 'Confirm requested reservation',
    'reservations.seat' => 'Seat reservation and convert to active dining session',
    'reservations.cancel' => 'Cancel reservation',
    'reservations.no_show' => 'Mark reservation as no-show',

    // Loyalty Engine & Ledger
    'loyalty.view' => 'View customer loyalty balances and transaction ledger',
    'loyalty.manage' => 'Manage loyalty accounts and status',
    'loyalty.adjust' => 'Perform audited manual loyalty points adjustment',
    'loyalty.redeem' => 'Redeem loyalty points for order discounts',
    'loyalty.rules.manage' => 'Configure loyalty point earning and redemption rules',

    // Coupons Engine
    'coupons.view' => 'View active coupons and promotion codes',
    'coupons.create' => 'Create new coupon codes and rules',
    'coupons.update' => 'Update coupon attributes, limits and status',
    'coupons.manage' => 'Manage coupon lifecycle',
    'coupons.redeem' => 'Validate and apply coupon discounts to order',
    'coupons.usage.view' => 'View coupon redemption audit history',

    // Marketing Promotions Engine
    'promotions.view' => 'View marketing promotions',
    'promotions.create' => 'Create promotional campaigns',
    'promotions.update' => 'Update promotion rules and dates',
    'promotions.manage' => 'Manage active promotional campaigns',

    // Public QR Ordering Engine
    'qr.view' => 'View table QR tokens and status',
    'qr.manage' => 'Manage QR ordering settings',
    'qr.generate' => 'Generate secure table QR ordering tokens',
    'qr.regenerate' => 'Regenerate table QR tokens',
    'qr.revoke' => 'Revoke table QR tokens',
    'qr.orders.view' => 'View public QR incoming orders'
]);

// Default Role Mappings
define('ROLE_PERMISSIONS_DEFAULT', [
    'admin' => array_keys(PERMISSIONS_CATALOG),
    'manager' => array_keys(PERMISSIONS_CATALOG),
    'reception' => [
        'dashboard.view', 'branches.view', 'floors.view', 'tables.view', 'tables.status_update',
        'dining_sessions.view', 'dining_sessions.create', 'dining_sessions.transfer', 'dining_sessions.close',
        'menu.view', 'categories.view', 'products.view', 'products.manage_availability',
        'orders.view', 'orders.create', 'orders.submit', 'orders.change_status', 'orders.cancel', 'order_items.manage',
        'kds.view', 'kds.manage', 'kds.served', 'kds.history.view',
        'routing.view', 'routing.dispatch', 'stations.view', 'tickets.view', 'tickets.manage', 'tickets.reprint',
        'payments.view', 'payments.create', 'payments.process', 'payments.update', 'payments.void', 'payments.refund',
        'billing.view', 'billing.manage', 'receipts.view', 'receipts.reprint', 'invoices.view', 'payment_methods.view',
        'shifts.view', 'shifts.open', 'shifts.close', 'cash.view', 'cash.in', 'cash.out',
        'customers.view', 'customers.create', 'customers.update',
        'reservations.view', 'reservations.create', 'reservations.update', 'reservations.confirm', 'reservations.seat', 'reservations.cancel', 'reservations.no_show',
        'loyalty.view', 'loyalty.redeem', 'coupons.view', 'coupons.redeem'
    ],
    'waiter' => [
        'branches.view', 'floors.view', 'tables.view', 'tables.status_update', 'dining_sessions.view',
        'dining_sessions.create', 'dining_sessions.transfer', 'menu.view', 'categories.view', 'products.view',
        'orders.view', 'orders.create', 'orders.update', 'orders.submit', 'orders.change_status', 'order_items.manage',
        'commissions.view', 'customers.view', 'customers.create', 'reservations.view', 'loyalty.view', 'coupons.redeem',
        'stations.view', 'kds.view', 'tickets.view', 'payments.view', 'billing.view'
    ],
    'kitchen' => [
        'kitchen.view', 'kitchen.manage', 'kds.view', 'kds.manage', 'kds.start', 'kds.ready', 'kds.served', 'kds.cancel',
        'kds.recall', 'kds.refire', 'kds.override', 'kds.history.view', 'tickets.view', 'tickets.manage', 'tickets.reprint',
        'stations.view', 'stations.pause', 'stations.activate', 'routing.view', 'orders.view', 'orders.change_status',
        'inventory.view', 'inventory.wastage', 'recipes.cost.view'
    ]
]);

