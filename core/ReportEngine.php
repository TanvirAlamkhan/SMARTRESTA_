<?php
/**
 * SMARTRESTA Core Report & Analytics Engine
 * Prompt 12: Admin & Manager Dashboard, Advanced Reporting, Analytics & Drill-Down Engine
 */

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/AuditLogger.php';

class ReportEngine {

    /**
     * Helper: Normalize date filters into datetime range
     */
    public static function normalizeDateRange(?string $dateFrom, ?string $dateTo, string $preset = 'today'): array {
        $now = new DateTime();
        
        if (!empty($dateFrom) && !empty($dateTo)) {
            return [
                'start' => date('Y-m-d 00:00:00', strtotime($dateFrom)),
                'end' => date('Y-m-d 23:59:59', strtotime($dateTo)),
                'preset' => 'custom'
            ];
        }

        switch (strtolower($preset)) {
            case 'yesterday':
                $start = (clone $now)->modify('-1 day')->format('Y-m-d 00:00:00');
                $end = (clone $now)->modify('-1 day')->format('Y-m-d 23:59:59');
                break;
            case 'this_week':
                $start = (clone $now)->modify('this week monday')->format('Y-m-d 00:00:00');
                $end = (clone $now)->format('Y-m-d 23:59:59');
                break;
            case 'last_week':
                $start = (clone $now)->modify('previous week monday')->format('Y-m-d 00:00:00');
                $end = (clone $now)->modify('previous week sunday')->format('Y-m-d 23:59:59');
                break;
            case 'this_month':
                $start = (clone $now)->format('Y-m-01 00:00:00');
                $end = (clone $now)->format('Y-m-t 23:59:59');
                break;
            case 'last_month':
                $start = (clone $now)->modify('first day of last month')->format('Y-m-01 00:00:00');
                $end = (clone $now)->modify('last day of last month')->format('Y-m-t 23:59:59');
                break;
            case 'this_year':
                $start = (clone $now)->format('Y-01-01 00:00:00');
                $end = (clone $now)->format('Y-12-31 23:59:59');
                break;
            case 'today':
            default:
                $start = (clone $now)->format('Y-m-d 00:00:00');
                $end = (clone $now)->format('Y-m-d 23:59:59');
                $preset = 'today';
                break;
        }

        return [
            'start' => $start,
            'end' => $end,
            'preset' => $preset
        ];
    }

    /**
     * Dashboard Executive Overview KPIs & Real-Time Operational Alerts
     */
    public static function getDashboardOverview(int $branchId, ?string $dateFrom = null, ?string $dateTo = null, string $preset = 'today'): array {
        $db = Database::getConnection();
        $range = self::normalizeDateRange($dateFrom, $dateTo, $preset);

        // 1. Sales & Orders Aggregates
        $stmtSales = $db->prepare("
            SELECT 
                COUNT(id) AS total_orders,
                COALESCE(SUM(CASE WHEN order_status NOT IN ('CANCELLED', 'REFUNDED') THEN subtotal + service_charge + delivery_charge ELSE 0 END), 0) AS gross_sales,
                COALESCE(SUM(CASE WHEN order_status NOT IN ('CANCELLED', 'REFUNDED') THEN discount ELSE 0 END), 0) AS total_discounts,
                COALESCE(SUM(CASE WHEN order_status NOT IN ('CANCELLED', 'REFUNDED') THEN tax ELSE 0 END), 0) AS total_tax,
                COALESCE(SUM(CASE WHEN order_status NOT IN ('CANCELLED', 'REFUNDED') THEN total ELSE 0 END), 0) AS net_sales,
                COALESCE(SUM(CASE WHEN order_status = 'COMPLETED' THEN total ELSE 0 END), 0) AS completed_sales,
                COALESCE(SUM(CASE WHEN order_status = 'CANCELLED' THEN 1 ELSE 0 END), 0) AS cancelled_orders_count,
                COALESCE(SUM(CASE WHEN order_status = 'REFUNDED' THEN total ELSE 0 END), 0) AS refunded_amount_orders
            FROM orders
            WHERE branch_id = :bid AND created_at BETWEEN :start AND :end
        ");
        $stmtSales->execute([':bid' => $branchId, ':start' => $range['start'], ':end' => $range['end']]);
        $salesMetrics = $stmtSales->fetch(PDO::FETCH_ASSOC);

        $totalOrders = (int)($salesMetrics['total_orders'] ?? 0);
        $netSales = (float)($salesMetrics['net_sales'] ?? 0.00);
        $grossSales = (float)($salesMetrics['gross_sales'] ?? 0.00);
        $totalDiscounts = (float)($salesMetrics['total_discounts'] ?? 0.00);
        $totalTax = (float)($salesMetrics['total_tax'] ?? 0.00);

        // 2. Payments & Collections
        $stmtPay = $db->prepare("
            SELECT 
                COALESCE(SUM(CASE WHEN status = 'COMPLETED' THEN amount ELSE 0 END), 0) AS total_paid,
                COALESCE(SUM(CASE WHEN status = 'REFUNDED' THEN amount ELSE 0 END), 0) AS total_refunded_payments
            FROM payments
            WHERE branch_id = :bid AND created_at BETWEEN :start AND :end
        ");
        $stmtPay->execute([':bid' => $branchId, ':start' => $range['start'], ':end' => $range['end']]);
        $payMetrics = $stmtPay->fetch(PDO::FETCH_ASSOC);

        $paidAmount = (float)($payMetrics['total_paid'] ?? 0.00);
        $refundedAmount = (float)($payMetrics['total_refunded_payments'] ?? $salesMetrics['refunded_amount_orders']);
        $pendingAmount = max(0.00, $netSales - $paidAmount);

        // Average Order Value (AOV)
        $completedOrdersCount = (int)($salesMetrics['total_orders'] - $salesMetrics['cancelled_orders_count']);
        $aov = $completedOrdersCount > 0 ? round($netSales / $completedOrdersCount, 2) : 0.00;

        // 3. Operational Real-Time Alerts
        // Low Stock Count
        $stmtLowStock = $db->prepare("
            SELECT COUNT(DISTINCT ingredient_id) FROM inventory_stock s
            JOIN ingredients i ON s.ingredient_id = i.id
            WHERE i.branch_id = :bid AND s.quantity_on_hand <= i.reorder_level AND i.status = 'ACTIVE'
        ");
        $stmtLowStock->execute([':bid' => $branchId]);
        $lowStockAlerts = (int)$stmtLowStock->fetchColumn();

        // Unpaid Orders Count
        $stmtUnpaid = $db->prepare("
            SELECT COUNT(id) FROM orders 
            WHERE branch_id = :bid AND payment_status IN ('UNPAID', 'PARTIAL') AND order_status NOT IN ('CANCELLED', 'REFUNDED')
        ");
        $stmtUnpaid->execute([':bid' => $branchId]);
        $unpaidOrdersAlerts = (int)$stmtUnpaid->fetchColumn();

        // Delayed KDS Tickets
        $stmtDelayedKDS = $db->prepare("
            SELECT COUNT(id) FROM order_tickets 
            WHERE status IN ('PENDING', 'PREPARING') AND created_at < DATE_SUB(NOW(), INTERVAL 20 MINUTE)
        ");
        $stmtDelayedKDS->execute();
        $delayedKDSAlerts = (int)$stmtDelayedKDS->fetchColumn();

        // Pending Commission Approval Count
        $stmtPendingComm = $db->prepare("
            SELECT COUNT(id) FROM commission_transactions 
            WHERE branch_id = :bid AND status = 'PENDING'
        ");
        $stmtPendingComm->execute([':bid' => $branchId]);
        $pendingCommAlerts = (int)$stmtPendingComm->fetchColumn();

        // Long running active dining sessions (> 2 hours)
        $stmtLongSessions = $db->prepare("
            SELECT COUNT(ds.id) FROM dining_sessions ds
            JOIN restaurant_tables t ON ds.table_id = t.id
            JOIN floors f ON t.floor_id = f.id
            WHERE f.branch_id = :bid AND ds.status = 'OPEN' AND ds.opened_at < DATE_SUB(NOW(), INTERVAL 2 HOUR)
        ");
        $stmtLongSessions->execute([':bid' => $branchId]);
        $longSessionsAlerts = (int)$stmtLongSessions->fetchColumn();

        return [
            'date_range' => $range,
            'kpis' => [
                'gross_sales' => $grossSales,
                'total_discounts' => $totalDiscounts,
                'total_tax' => $totalTax,
                'net_sales' => $netSales,
                'total_orders' => $totalOrders,
                'aov' => $aov,
                'paid_amount' => $paidAmount,
                'pending_amount' => $pendingAmount,
                'refunded_amount' => $refundedAmount,
                'cancelled_orders' => (int)$salesMetrics['cancelled_orders_count']
            ],
            'alerts' => [
                'low_stock_ingredients' => $lowStockAlerts,
                'unpaid_orders' => $unpaidOrdersAlerts,
                'delayed_kds_tickets' => $delayedKDSAlerts,
                'pending_commissions' => $pendingCommAlerts,
                'long_running_sessions' => $longSessionsAlerts
            ]
        ];
    }

    /**
     * Sales Analytics Breakdown Report
     */
    public static function getSalesReport(int $branchId, ?string $dateFrom = null, ?string $dateTo = null, string $groupBy = 'date'): array {
        $db = Database::getConnection();
        $range = self::normalizeDateRange($dateFrom, $dateTo, 'this_month');

        $groupSql = "DATE(created_at)";
        if ($groupBy === 'hour') {
            $groupSql = "DATE_FORMAT(created_at, '%Y-%m-%d %H:00')";
        } elseif ($groupBy === 'order_type') {
            $groupSql = "order_type";
        } elseif ($groupBy === 'payment_status') {
            $groupSql = "payment_status";
        }

        $stmt = $db->prepare("
            SELECT 
                {$groupSql} AS period,
                COUNT(id) AS order_count,
                COALESCE(SUM(subtotal), 0) AS gross_sales,
                COALESCE(SUM(discount), 0) AS discounts,
                COALESCE(SUM(tax), 0) AS tax,
                COALESCE(SUM(total), 0) AS net_sales
            FROM orders
            WHERE branch_id = :bid AND created_at BETWEEN :start AND :end
            GROUP BY {$groupSql}
            ORDER BY period ASC
        ");
        $stmt->execute([':bid' => $branchId, ':start' => $range['start'], ':end' => $range['end']]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'date_range' => $range,
            'group_by' => $groupBy,
            'sales_data' => $rows
        ];
    }

    /**
     * Server-side Paginated Orders Report & Operational Filter Engine
     */
    public static function getOrdersReport(int $branchId, ?string $dateFrom = null, ?string $dateTo = null, array $filters = [], int $page = 1, int $perPage = 25): array {
        $db = Database::getConnection();
        $range = self::normalizeDateRange($dateFrom, $dateTo, 'today');

        $where = ["o.branch_id = :bid", "o.created_at BETWEEN :start AND :end"];
        $params = [':bid' => $branchId, ':start' => $range['start'], ':end' => $range['end']];

        if (!empty($filters['order_status'])) {
            $where[] = "o.order_status = :ostatus";
            $params[':ostatus'] = $filters['order_status'];
        }
        if (!empty($filters['payment_status'])) {
            $where[] = "o.payment_status = :pstatus";
            $params[':pstatus'] = $filters['payment_status'];
        }
        if (!empty($filters['order_type'])) {
            $where[] = "o.order_type = :otype";
            $params[':otype'] = $filters['order_type'];
        }
        if (!empty($filters['table_id'])) {
            $where[] = "o.table_id = :tid";
            $params[':tid'] = (int)$filters['table_id'];
        }
        if (!empty($filters['waiter_id'])) {
            $where[] = "(o.taken_by_user_id = :wid OR o.served_by_user_id = :wid)";
            $params[':wid'] = (int)$filters['waiter_id'];
        }
        if (!empty($filters['search'])) {
            $where[] = "(o.order_number LIKE :srch OR u.name LIKE :srch OR t.table_number LIKE :srch)";
            $params[':srch'] = '%' . trim($filters['search']) . '%';
        }

        $whereClause = implode(" AND ", $where);

        // Count Total
        $countStmt = $db->prepare("
            SELECT COUNT(o.id) 
            FROM orders o
            LEFT JOIN users u ON o.taken_by_user_id = u.id
            LEFT JOIN restaurant_tables t ON o.table_id = t.id
            WHERE {$whereClause}
        ");
        $countStmt->execute($params);
        $totalItems = (int)$countStmt->fetchColumn();

        $totalPages = $totalItems > 0 ? (int)ceil($totalItems / $perPage) : 1;
        $page = max(1, min($page, $totalPages));
        $offset = ($page - 1) * $perPage;

        // Fetch Items
        $selectStmt = $db->prepare("
            SELECT 
                o.id,
                o.order_number,
                o.order_type,
                o.order_status,
                o.payment_status,
                o.subtotal,
                o.discount,
                o.tax,
                o.service_charge,
                o.total,
                o.created_at,
                o.completed_at,
                t.table_number,
                u_taker.name AS order_taker_name,
                u_server.name AS waiter_name,
                COALESCE(p.paid_amount, 0) AS paid_amount
            FROM orders o
            LEFT JOIN restaurant_tables t ON o.table_id = t.id
            LEFT JOIN users u_taker ON o.taken_by_user_id = u_taker.id
            LEFT JOIN users u_server ON o.served_by_user_id = u_server.id
            LEFT JOIN (
                SELECT order_id, SUM(amount) AS paid_amount 
                FROM payments 
                WHERE status = 'COMPLETED' 
                GROUP BY order_id
            ) p ON o.id = p.order_id
            WHERE {$whereClause}
            ORDER BY o.created_at DESC
            LIMIT {$perPage} OFFSET {$offset}
        ");
        $selectStmt->execute($params);
        $items = $selectStmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'date_range' => $range,
            'items' => $items,
            'pagination' => [
                'page' => $page,
                'perPage' => $perPage,
                'total' => $totalItems,
                'totalPages' => $totalPages
            ]
        ];
    }

    /**
     * Waiter Performance & Commission Report
     */
    public static function getWaiterPerformanceReport(int $branchId, ?string $dateFrom = null, ?string $dateTo = null, array $filters = [], int $page = 1, int $perPage = 25): array {
        $db = Database::getConnection();
        $range = self::normalizeDateRange($dateFrom, $dateTo, 'this_month');

        $stmt = $db->prepare("
            SELECT 
                u.id AS waiter_id,
                u.name AS waiter_name,
                u.email,
                COUNT(DISTINCT o.id) AS orders_count,
                COUNT(DISTINCT o.table_id) AS tables_served,
                COALESCE(SUM(CASE WHEN o.order_status NOT IN ('CANCELLED', 'REFUNDED') THEN o.total ELSE 0 END), 0) AS total_sales,
                COALESCE(SUM(CASE WHEN o.order_status = 'CANCELLED' THEN 1 ELSE 0 END), 0) AS cancelled_orders,
                COALESCE(comm.pending_commission, 0) AS pending_commission,
                COALESCE(comm.approved_commission, 0) AS approved_commission,
                COALESCE(comm.paid_commission, 0) AS paid_commission,
                COALESCE(comm.total_commission, 0) AS total_commission
            FROM users u
            JOIN roles r ON u.role_id = r.id AND r.name IN ('waiter', 'reception', 'manager', 'admin')
            LEFT JOIN waiter_profiles wp ON u.id = wp.user_id
            LEFT JOIN orders o ON (o.taken_by_user_id = u.id OR o.served_by_user_id = u.id) 
                AND o.branch_id = :bid1 AND o.created_at BETWEEN :start1 AND :end1
            LEFT JOIN (
                SELECT 
                    waiter_id,
                    SUM(CASE WHEN status = 'PENDING' THEN commission_amount ELSE 0 END) AS pending_commission,
                    SUM(CASE WHEN status = 'APPROVED' THEN commission_amount ELSE 0 END) AS approved_commission,
                    SUM(CASE WHEN status = 'PAID' THEN commission_amount ELSE 0 END) AS paid_commission,
                    SUM(commission_amount) AS total_commission
                FROM commission_transactions
                WHERE branch_id = :bid2 AND created_at BETWEEN :start2 AND :end2
                GROUP BY waiter_id
            ) comm ON u.id = comm.waiter_id
            WHERE (wp.branch_id = :bid3 OR wp.branch_id IS NULL)
            GROUP BY u.id
            ORDER BY total_sales DESC
        ");
        $stmt->execute([
            ':bid1' => $branchId, ':start1' => $range['start'], ':end1' => $range['end'],
            ':bid2' => $branchId, ':start2' => $range['start'], ':end2' => $range['end'],
            ':bid3' => $branchId
        ]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as &$r) {
            $r['orders_count'] = (int)$r['orders_count'];
            $r['tables_served'] = (int)$r['tables_served'];
            $r['total_sales'] = (float)$r['total_sales'];
            $r['pending_commission'] = (float)$r['pending_commission'];
            $r['approved_commission'] = (float)$r['approved_commission'];
            $r['paid_commission'] = (float)$r['paid_commission'];
            $r['total_commission'] = (float)$r['total_commission'];
            $r['average_order_value'] = $r['orders_count'] > 0 ? round($r['total_sales'] / $r['orders_count'], 2) : 0.00;
        }

        return [
            'date_range' => $range,
            'items' => $rows,
            'pagination' => [
                'page' => 1,
                'perPage' => count($rows),
                'total' => count($rows),
                'totalPages' => 1
            ]
        ];
    }

    /**
     * Table & Dining Session Performance Report
     */
    public static function getTablePerformanceReport(int $branchId, ?string $dateFrom = null, ?string $dateTo = null, array $filters = [], int $page = 1, int $perPage = 25): array {
        $db = Database::getConnection();
        $range = self::normalizeDateRange($dateFrom, $dateTo, 'this_month');

        $stmt = $db->prepare("
            SELECT 
                t.id AS table_id,
                t.table_number,
                t.capacity,
                f.name AS floor_name,
                COUNT(DISTINCT s.id) AS total_sessions,
                COUNT(DISTINCT o.id) AS total_orders,
                COALESCE(SUM(CASE WHEN o.order_status NOT IN ('CANCELLED', 'REFUNDED') THEN o.total ELSE 0 END), 0) AS total_sales,
                COALESCE(SUM(s.guest_count), 0) AS total_guests,
                COALESCE(AVG(TIMESTAMPDIFF(MINUTE, s.opened_at, COALESCE(s.closed_at, NOW()))), 0) AS avg_session_duration_minutes
            FROM restaurant_tables t
            JOIN floors f ON t.floor_id = f.id
            LEFT JOIN dining_sessions s ON t.id = s.table_id AND s.opened_at BETWEEN :start1 AND :end1
            LEFT JOIN orders o ON t.id = o.table_id AND o.created_at BETWEEN :start2 AND :end2
            WHERE f.branch_id = :bid
            GROUP BY t.id
            ORDER BY total_sales DESC
        ");
        $stmt->execute([
            ':bid' => $branchId,
            ':start1' => $range['start'], ':end1' => $range['end'],
            ':start2' => $range['start'], ':end2' => $range['end']
        ]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as &$r) {
            $r['total_sessions'] = (int)$r['total_sessions'];
            $r['total_orders'] = (int)$r['total_orders'];
            $r['total_sales'] = (float)$r['total_sales'];
            $r['total_guests'] = (int)$r['total_guests'];
            $r['avg_session_duration_minutes'] = round((float)$r['avg_session_duration_minutes'], 1);
            $r['avg_spend_per_session'] = $r['total_sessions'] > 0 ? round($r['total_sales'] / $r['total_sessions'], 2) : 0.00;
        }

        return [
            'date_range' => $range,
            'items' => $rows,
            'pagination' => [
                'page' => 1,
                'perPage' => count($rows),
                'total' => count($rows),
                'totalPages' => 1
            ]
        ];
    }

    /**
     * Product Performance & Recipe Margin Report
     */
    public static function getProductPerformanceReport(int $branchId, ?string $dateFrom = null, ?string $dateTo = null, array $filters = [], int $page = 1, int $perPage = 25): array {
        $db = Database::getConnection();
        $range = self::normalizeDateRange($dateFrom, $dateTo, 'this_month');

        $stmt = $db->prepare("
            SELECT 
                p.id AS product_id,
                p.name AS product_name,
                c.name AS category_name,
                p.price AS selling_price,
                COALESCE(SUM(oi.quantity), 0) AS total_units_sold,
                COALESCE(SUM(oi.subtotal), 0) AS gross_sales,
                COALESCE(r.total_cost, 0) AS recipe_unit_cost
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN menus m ON c.menu_id = m.id
            LEFT JOIN order_items oi ON p.id = oi.product_id
            LEFT JOIN orders o ON oi.order_id = o.id AND o.branch_id = :bid1 AND o.created_at BETWEEN :start AND :end AND o.order_status NOT IN ('CANCELLED', 'REFUNDED')
            LEFT JOIN recipes r ON p.id = r.product_id AND r.is_active = 1
            WHERE (m.branch_id = :bid2 OR m.branch_id IS NULL OR p.category_id IS NULL)
            GROUP BY p.id
            ORDER BY total_units_sold DESC
        ");
        $stmt->execute([
            ':bid1' => $branchId,
            ':bid2' => $branchId,
            ':start' => $range['start'],
            ':end' => $range['end']
        ]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as &$r) {
            $r['total_units_sold'] = (int)$r['total_units_sold'];
            $r['gross_sales'] = (float)$r['gross_sales'];
            $r['selling_price'] = (float)$r['selling_price'];
            $r['recipe_unit_cost'] = (float)$r['recipe_unit_cost'];
            $r['total_cost'] = round($r['total_units_sold'] * $r['recipe_unit_cost'], 2);
            $r['gross_profit'] = round($r['gross_sales'] - $r['total_cost'], 2);
            $r['margin_percentage'] = $r['gross_sales'] > 0 ? round(($r['gross_profit'] / $r['gross_sales']) * 100, 1) : 0.0;
        }

        return [
            'date_range' => $range,
            'items' => $rows,
            'pagination' => [
                'page' => 1,
                'perPage' => count($rows),
                'total' => count($rows),
                'totalPages' => 1
            ]
        ];
    }

    /**
     * Payment Method Reconciliation & Outstanding Report
     */
    public static function getPaymentReport(int $branchId, ?string $dateFrom = null, ?string $dateTo = null, array $filters = [], int $page = 1, int $perPage = 25): array {
        $db = Database::getConnection();
        $range = self::normalizeDateRange($dateFrom, $dateTo, 'this_month');

        $stmt = $db->prepare("
            SELECT 
                pm.id AS payment_method_id,
                pm.name AS payment_method_name,
                pm.code AS payment_method_code,
                COUNT(p.id) AS transaction_count,
                COALESCE(SUM(CASE WHEN p.status = 'COMPLETED' THEN p.amount ELSE 0 END), 0) AS total_collected,
                COALESCE(SUM(CASE WHEN p.status = 'REFUNDED' THEN p.amount ELSE 0 END), 0) AS total_refunded
            FROM payment_methods pm
            LEFT JOIN payments p ON pm.id = p.payment_method_id AND p.branch_id = :bid AND p.created_at BETWEEN :start AND :end
            GROUP BY pm.id
            ORDER BY total_collected DESC
        ");
        $stmt->execute([':bid' => $branchId, ':start' => $range['start'], ':end' => $range['end']]);
        $methods = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($methods as &$m) {
            $m['transaction_count'] = (int)$m['transaction_count'];
            $m['total_collected'] = (float)$m['total_collected'];
            $m['total_refunded'] = (float)$m['total_refunded'];
            $m['net_collected'] = round($m['total_collected'] - $m['total_refunded'], 2);
        }

        // Outstanding Unpaid Orders
        $stmtUnpaid = $db->prepare("
            SELECT 
                o.id AS order_id,
                o.order_number,
                t.table_number,
                o.total,
                COALESCE(SUM(p.amount), 0) AS paid_amount,
                (o.total - COALESCE(SUM(p.amount), 0)) AS outstanding_balance,
                o.payment_status,
                o.created_at
            FROM orders o
            LEFT JOIN restaurant_tables t ON o.table_id = t.id
            LEFT JOIN payments p ON o.id = p.order_id AND p.status = 'COMPLETED'
            WHERE o.branch_id = :bid AND o.payment_status IN ('UNPAID', 'PARTIAL') AND o.order_status NOT IN ('CANCELLED', 'REFUNDED')
            GROUP BY o.id
            ORDER BY o.created_at DESC
        ");
        $stmtUnpaid->execute([':bid' => $branchId]);
        $unpaid = $stmtUnpaid->fetchAll(PDO::FETCH_ASSOC);

        return [
            'date_range' => $range,
            'payment_methods' => $methods,
            'outstanding_orders' => $unpaid
        ];
    }

    /**
     * KDS SLA & Station Production Report
     */
    public static function getKDSPerformanceReport(int $branchId, ?string $dateFrom = null, ?string $dateTo = null, array $filters = [], int $page = 1, int $perPage = 25): array {
        $db = Database::getConnection();
        $range = self::normalizeDateRange($dateFrom, $dateTo, 'today');

        $stmt = $db->prepare("
            SELECT 
                st.id AS station_id,
                st.name AS station_name,
                st.badge_code AS station_code,
                COUNT(ot.id) AS total_tickets,
                COALESCE(SUM(CASE WHEN ot.status = 'SERVED' THEN 1 ELSE 0 END), 0) AS completed_tickets,
                COALESCE(SUM(CASE WHEN ot.status = 'CANCELLED' THEN 1 ELSE 0 END), 0) AS cancelled_tickets,
                COALESCE(AVG(CASE WHEN ot.status IN ('SERVED', 'COMPLETED') THEN TIMESTAMPDIFF(SECOND, ot.created_at, COALESCE(ot.completed_at, ot.ready_at, NOW())) ELSE NULL END), 0) AS avg_prep_time_seconds,
                COALESCE(SUM(CASE WHEN ot.status IN ('PENDING', 'PREPARING') AND ot.created_at < DATE_SUB(NOW(), INTERVAL 20 MINUTE) THEN 1 ELSE 0 END), 0) AS delayed_tickets_count
            FROM stations st
            LEFT JOIN order_tickets ot ON st.id = ot.station_id AND ot.created_at BETWEEN :start AND :end
            WHERE st.branch_id = :bid
            GROUP BY st.id
            ORDER BY total_tickets DESC
        ");
        $stmt->execute([':bid' => $branchId, ':start' => $range['start'], ':end' => $range['end']]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as &$r) {
            $r['total_tickets'] = (int)$r['total_tickets'];
            $r['completed_tickets'] = (int)$r['completed_tickets'];
            $r['cancelled_tickets'] = (int)$r['cancelled_tickets'];
            $r['delayed_tickets_count'] = (int)$r['delayed_tickets_count'];
            $avgSecs = (float)$r['avg_prep_time_seconds'];
            $r['avg_prep_time_minutes'] = round($avgSecs / 60, 1);
        }

        return [
            'date_range' => $range,
            'stations' => $rows
        ];
    }

    /**
     * Inventory Consumption, Valuation & Wastage Report
     */
    public static function getInventoryReport(int $branchId, ?string $dateFrom = null, ?string $dateTo = null, array $filters = [], int $page = 1, int $perPage = 25): array {
        $db = Database::getConnection();
        $range = self::normalizeDateRange($dateFrom, $dateTo, 'this_month');

        // Total Stock Valuation
        $stmtVal = $db->prepare("
            SELECT 
                COUNT(i.id) AS total_ingredients,
                COALESCE(SUM(s.quantity_on_hand * i.average_cost), 0) AS total_stock_value
            FROM ingredients i
            JOIN inventory_stock s ON i.id = s.ingredient_id
            WHERE i.branch_id = :bid AND i.status = 'ACTIVE'
        ");
        $stmtVal->execute([':bid' => $branchId]);
        $val = $stmtVal->fetch(PDO::FETCH_ASSOC);

        // Wastage Breakdown by Reason
        $stmtWastage = $db->prepare("
            SELECT 
                reason,
                COUNT(id) AS records_count,
                COALESCE(SUM(quantity), 0) AS total_quantity,
                COALESCE(SUM(estimated_cost), 0) AS total_cost
            FROM wastage_records
            WHERE branch_id = :bid AND created_at BETWEEN :start AND :end
            GROUP BY reason
            ORDER BY total_cost DESC
        ");
        $stmtWastage->execute([':bid' => $branchId, ':start' => $range['start'], ':end' => $range['end']]);
        $wastage = $stmtWastage->fetchAll(PDO::FETCH_ASSOC);

        // Ingredient Consumption Summary
        $stmtCons = $db->prepare("
            SELECT 
                i.id AS ingredient_id,
                i.name AS ingredient_name,
                i.base_unit,
                ABS(SUM(t.quantity)) AS total_consumed_qty,
                SUM(t.total_cost) AS total_consumed_cost
            FROM inventory_transactions t
            JOIN ingredients i ON t.ingredient_id = i.id
            WHERE t.branch_id = :bid AND t.type = 'ORDER_CONSUMPTION' AND t.created_at BETWEEN :start AND :end
            GROUP BY i.id
            ORDER BY total_consumed_cost DESC
        ");
        $stmtCons->execute([':bid' => $branchId, ':start' => $range['start'], ':end' => $range['end']]);
        $consumption = $stmtCons->fetchAll(PDO::FETCH_ASSOC);

        return [
            'date_range' => $range,
            'stock_summary' => [
                'total_ingredients' => (int)($val['total_ingredients'] ?? 0),
                'total_stock_value' => (float)($val['total_stock_value'] ?? 0.0)
            ],
            'wastage_summary' => $wastage,
            'consumption_summary' => $consumption
        ];
    }

    /**
     * Comprehensive Order Drill-Down Detail Viewer
     */
    public static function getDrilldownOrderDetails(int $orderId, int $branchId): array {
        $db = Database::getConnection();

        // 1. Order Header
        $stmtOrder = $db->prepare("
            SELECT 
                o.*,
                t.table_number,
                f.name AS floor_name,
                u_taker.name AS order_taker_name,
                u_server.name AS waiter_name
            FROM orders o
            LEFT JOIN restaurant_tables t ON o.table_id = t.id
            LEFT JOIN floors f ON t.floor_id = f.id
            LEFT JOIN users u_taker ON o.taken_by_user_id = u_taker.id
            LEFT JOIN users u_server ON o.served_by_user_id = u_server.id
            WHERE o.id = :oid AND o.branch_id = :bid
        ");
        $stmtOrder->execute([':oid' => $orderId, ':bid' => $branchId]);
        $order = $stmtOrder->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            throw new Exception("Order #{$orderId} not found or access denied.");
        }

        // 2. Order Line Items
        $stmtItems = $db->prepare("
            SELECT 
                oi.*,
                p.name AS product_name
            FROM order_items oi
            LEFT JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = :oid
        ");
        $stmtItems->execute([':oid' => $orderId]);
        $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

        // 3. Station Tickets (KDS)
        $stmtTickets = $db->prepare("
            SELECT 
                ot.*,
                st.name AS station_name
            FROM order_tickets ot
            LEFT JOIN stations st ON ot.station_id = st.id
            WHERE ot.order_id = :oid
            ORDER BY ot.created_at ASC
        ");
        $stmtTickets->execute([':oid' => $orderId]);
        $tickets = $stmtTickets->fetchAll(PDO::FETCH_ASSOC);

        // 4. Payments
        $stmtPay = $db->prepare("
            SELECT 
                p.*,
                pm.name AS payment_method_name,
                u.name AS receiver_name
            FROM payments p
            LEFT JOIN payment_methods pm ON p.payment_method_id = pm.id
            LEFT JOIN users u ON p.received_by_user_id = u.id
            WHERE p.order_id = :oid
            ORDER BY p.created_at ASC
        ");
        $stmtPay->execute([':oid' => $orderId]);
        $payments = $stmtPay->fetchAll(PDO::FETCH_ASSOC);

        // 5. Commissions
        $stmtComm = $db->prepare("
            SELECT 
                c.*,
                u.name AS waiter_name
            FROM commission_transactions c
            LEFT JOIN users u ON c.waiter_id = u.id
            WHERE c.order_id = :oid
        ");
        $stmtComm->execute([':oid' => $orderId]);
        $commissions = $stmtComm->fetchAll(PDO::FETCH_ASSOC);

        // 6. Inventory Consumption Audit Records
        $stmtInv = $db->prepare("
            SELECT 
                t.*,
                i.name AS ingredient_name,
                i.base_unit
            FROM inventory_transactions t
            LEFT JOIN ingredients i ON t.ingredient_id = i.id
            WHERE t.reference_type IN ('order_items', 'orders') AND t.reference_id IN (
                SELECT id FROM order_items WHERE order_id = :oid UNION SELECT :oid2
            )
            ORDER BY t.created_at ASC
        ");
        $stmtInv->execute([':oid' => $orderId, ':oid2' => $orderId]);
        $inventoryLogs = $stmtInv->fetchAll(PDO::FETCH_ASSOC);

        return [
            'order' => $order,
            'items' => $items,
            'tickets' => $tickets,
            'payments' => $payments,
            'commissions' => $commissions,
            'inventory_logs' => $inventoryLogs
        ];
    }

    /**
     * CSV Exporter Generator
     */
    public static function exportCSV(string $reportType, int $branchId, ?string $dateFrom = null, ?string $dateTo = null, array $filters = []): string {
        $output = fopen('php://temp', 'r+');

        switch ($reportType) {
            case 'sales':
                $data = self::getSalesReport($branchId, $dateFrom, $dateTo);
                fputcsv($output, ['Period', 'Order Count', 'Gross Sales (BDT)', 'Discounts (BDT)', 'Tax (BDT)', 'Net Sales (BDT)']);
                foreach ($data['sales_data'] as $row) {
                    fputcsv($output, [$row['period'], $row['order_count'], $row['gross_sales'], $row['discounts'], $row['tax'], $row['net_sales']]);
                }
                break;

            case 'orders':
                $data = self::getOrdersReport($branchId, $dateFrom, $dateTo, $filters, 1, 10000);
                fputcsv($output, ['Order Number', 'Order Type', 'Table', 'Waiter', 'Order Status', 'Payment Status', 'Subtotal', 'Discount', 'Tax', 'Total', 'Paid Amount', 'Created At']);
                foreach ($data['items'] as $row) {
                    fputcsv($output, [$row['order_number'], $row['order_type'], $row['table_number'] ?? 'N/A', $row['waiter_name'] ?? 'N/A', $row['order_status'], $row['payment_status'], $row['subtotal'], $row['discount'], $row['tax'], $row['total'], $row['paid_amount'], $row['created_at']]);
                }
                break;

            case 'waiters':
                $data = self::getWaiterPerformanceReport($branchId, $dateFrom, $dateTo);
                fputcsv($output, ['Waiter Name', 'Email', 'Orders Count', 'Tables Served', 'Total Sales (BDT)', 'AOV (BDT)', 'Pending Comm', 'Approved Comm', 'Paid Comm', 'Total Comm']);
                foreach ($data['items'] as $row) {
                    fputcsv($output, [$row['waiter_name'], $row['email'], $row['orders_count'], $row['tables_served'], $row['total_sales'], $row['average_order_value'], $row['pending_commission'], $row['approved_commission'], $row['paid_commission'], $row['total_commission']]);
                }
                break;

            case 'products':
                $data = self::getProductPerformanceReport($branchId, $dateFrom, $dateTo);
                fputcsv($output, ['Product Name', 'Category', 'Selling Price', 'Units Sold', 'Gross Sales (BDT)', 'Recipe Unit Cost (BDT)', 'Total Cost (BDT)', 'Gross Profit (BDT)', 'Margin (%)']);
                foreach ($data['items'] as $row) {
                    fputcsv($output, [$row['product_name'], $row['category_name'] ?? 'Uncategorized', $row['selling_price'], $row['total_units_sold'], $row['gross_sales'], $row['recipe_unit_cost'], $row['total_cost'], $row['gross_profit'], $row['margin_percentage']]);
                }
                break;

            default:
                fputcsv($output, ['Error', 'Unsupported report type for CSV export']);
                break;
        }

        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);

        AuditLogger::log('REPORT_EXPORTED', 'reports', 0, null, "Report CSV exported: {$reportType} (Branch #{$branchId})");

        return $csvContent;
    }
}
