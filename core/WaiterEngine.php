<?php
/**
 * SMARTRESTA Core Waiter Management Engine
 * Prompt 09: Waiter Management, Commission Engine & Performance Tracking
 *
 * Manages staff waiter profiles, employee code generation, table/session assignments,
 * and server-side SQL performance matrix aggregations.
 */

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/AuditLogger.php';

class WaiterEngine {

    /**
     * Get or create a waiter profile for a given staff user ID
     */
    public static function getOrCreateProfile(int $userId, int $branchId = 1): array {
        $db = Database::getConnection();

        // 1. Fetch User Record
        $userStmt = $db->prepare("
            SELECT u.*, r.name AS role_name
            FROM users u
            JOIN roles r ON u.role_id = r.id
            WHERE u.id = :uid
        ");
        $userStmt->execute([':uid' => $userId]);
        $user = $userStmt->fetch();

        if (!$user) {
            throw new Exception("Staff user #{$userId} not found.");
        }

        // 2. Fetch or Create Waiter Profile
        $profStmt = $db->prepare("SELECT * FROM waiter_profiles WHERE user_id = :uid");
        $profStmt->execute([':uid' => $userId]);
        $profile = $profStmt->fetch();

        if (!$profile) {
            $employeeCode = 'WTR-' . sprintf('%03d', $userId);
            $insStmt = $db->prepare("
                INSERT INTO waiter_profiles (user_id, employee_code, branch_id, commission_enabled, status, created_at)
                VALUES (:uid, :ecode, :bid, 1, 'ACTIVE', NOW())
            ");
            $insStmt->execute([
                ':uid' => $userId,
                ':ecode' => $employeeCode,
                ':bid' => $branchId
            ]);

            $profStmt->execute([':uid' => $userId]);
            $profile = $profStmt->fetch();

            AuditLogger::log($userId, 'WAITER_PROFILE_CREATED', 'waiter_profiles', (int)$profile['id'], null, [
                'employee_code' => $employeeCode,
                'user_name' => $user['name']
            ]);
        }

        return array_merge($user, [
            'waiter_id' => (int)$user['id'],
            'profile_id' => (int)$profile['id'],
            'branch_id' => (int)($user['branch_id'] ?? ($profile['branch_id'] ?? $branchId)),
            'employee_code' => $profile['employee_code'],
            'commission_enabled' => (bool)$profile['commission_enabled'],
            'default_commission_rule_id' => $profile['default_commission_rule_id'] ? (int)$profile['default_commission_rule_id'] : null,
            'waiter_status' => $profile['status']
        ]);
    }

    /**
     * Get list of all waiters for a branch
     */
    public static function getWaitersList(int $branchId = 1, ?string $statusFilter = null): array {
        $db = Database::getConnection();

        $sql = "
            SELECT u.id AS waiter_id, u.name AS waiter_name, u.email, u.phone,
                   wp.id AS profile_id, wp.employee_code, wp.commission_enabled, wp.status AS waiter_status,
                   cr.name AS default_rule_name
            FROM users u
            JOIN roles r ON u.role_id = r.id
            LEFT JOIN waiter_profiles wp ON u.id = wp.user_id
            LEFT JOIN commission_rules cr ON wp.default_commission_rule_id = cr.id
            WHERE r.name IN ('waiter', 'reception', 'manager', 'admin') AND (wp.branch_id = :bid OR wp.branch_id IS NULL)
        ";

        $params = [':bid' => $branchId];

        if ($statusFilter) {
            $sql .= " AND (wp.status = :st1 OR (wp.status IS NULL AND :st2 = 'ACTIVE'))";
            $params[':st1'] = $statusFilter;
            $params[':st2'] = $statusFilter;
        }

        $sql .= " ORDER BY u.name ASC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $waiters = $stmt->fetchAll();

        // Ensure every waiter user has a profile record initialized
        foreach ($waiters as &$w) {
            if (empty($w['profile_id'])) {
                $prof = self::getOrCreateProfile((int)$w['waiter_id'], $branchId);
                $w['profile_id'] = $prof['profile_id'];
                $w['employee_code'] = $prof['employee_code'];
                $w['waiter_status'] = $prof['waiter_status'];
                $w['commission_enabled'] = $prof['commission_enabled'];
            }
        }

        return $waiters;
    }

    /**
     * Assign a waiter to a dining table or session
     */
    public static function assignWaiter(array $data, int $assignedBy = 1): array {
        $db = Database::getConnection();

        $waiterId = (int)($data['waiter_id'] ?? 0);
        $tableId = (int)($data['table_id'] ?? 0);
        $sessionId = !empty($data['dining_session_id']) ? (int)$data['dining_session_id'] : null;
        $orderId = !empty($data['order_id']) ? (int)$data['order_id'] : null;
        $type = !empty($data['assignment_type']) ? strtoupper(trim($data['assignment_type'])) : 'TABLE';

        if (!$waiterId) {
            throw new Exception("Waiter ID is required for assignment.");
        }

        // Verify Waiter existence
        $waiterProf = self::getOrCreateProfile($waiterId);

        if ($waiterProf['waiter_status'] !== 'ACTIVE') {
            throw new Exception("Waiter '{$waiterProf['name']}' is {$waiterProf['waiter_status']} and cannot be assigned.");
        }

        // Deactivate existing active assignment on same table/session if needed
        if ($tableId) {
            $deactStmt = $db->prepare("UPDATE waiter_assignments SET status = 'COMPLETED', unassigned_at = NOW() WHERE table_id = :tid AND status = 'ACTIVE'");
            $deactStmt->execute([':tid' => $tableId]);
        }

        // Create new assignment
        $insStmt = $db->prepare("
            INSERT INTO waiter_assignments
            (branch_id, waiter_id, table_id, dining_session_id, order_id, assignment_type, assigned_by_user_id, status, assigned_at)
            VALUES (:bid, :wid, :tid, :sid, :oid, :atype, :uid, 'ACTIVE', NOW())
        ");
        $insStmt->execute([
            ':bid' => $waiterProf['branch_id'],
            ':wid' => $waiterId,
            ':tid' => $tableId ?: null,
            ':sid' => $sessionId,
            ':oid' => $orderId,
            ':atype' => $type,
            ':uid' => $assignedBy
        ]);
        $assignId = (int)$db->lastInsertId();

        AuditLogger::log($assignedBy, 'WAITER_ASSIGNED', 'waiter_assignments', $assignId, null, [
            'waiter_id' => $waiterId,
            'table_id' => $tableId,
            'dining_session_id' => $sessionId,
            'assignment_type' => $type
        ]);

        return [
            'assignment_id' => $assignId,
            'waiter_id' => $waiterId,
            'waiter_name' => $waiterProf['name'],
            'employee_code' => $waiterProf['employee_code'],
            'table_id' => $tableId,
            'dining_session_id' => $sessionId,
            'status' => 'ACTIVE'
        ];
    }

    /**
     * Calculate server-side Waiter Performance & Commission Matrix
     */
    public static function getPerformanceMatrix(int $branchId = 1, ?string $startDate = null, ?string $endDate = null): array {
        $db = Database::getConnection();

        $waiters = self::getWaitersList($branchId, 'ACTIVE');

        $dateWhereOrders = "";
        $dateWhereComm = "";
        $paramsOrders = [':bid' => $branchId];
        $paramsComm = [':bid' => $branchId];

        if ($startDate) {
            $dateWhereOrders .= " AND o.created_at >= :sdate";
            $dateWhereComm .= " AND ct.created_at >= :sdate";
            $paramsOrders[':sdate'] = $startDate . ' 00:00:00';
            $paramsComm[':sdate'] = $startDate . ' 00:00:00';
        }

        if ($endDate) {
            $dateWhereOrders .= " AND o.created_at <= :edate";
            $dateWhereComm .= " AND ct.created_at <= :edate";
            $paramsOrders[':edate'] = $endDate . ' 23:59:59';
            $paramsComm[':edate'] = $endDate . ' 23:59:59';
        }

        $matrix = [];

        foreach ($waiters as $w) {
            $wid = (int)$w['waiter_id'];

            // 1. Order & Sales Aggregations
            $ordStmt = $db->prepare("
                SELECT 
                    COUNT(id) AS total_orders,
                    COUNT(DISTINCT table_id) AS tables_served,
                    COALESCE(SUM(total), 0.00) AS total_sales,
                    COALESCE(SUM(CASE WHEN payment_status = 'PAID' THEN total ELSE 0.00 END), 0.00) AS paid_sales,
                    COALESCE(SUM(CASE WHEN payment_status = 'PAID' THEN 1 ELSE 0 END), 0) AS paid_orders,
                    COALESCE(SUM(CASE WHEN payment_status != 'PAID' THEN 1 ELSE 0 END), 0) AS pending_orders
                FROM orders o
                WHERE o.taken_by_user_id = :wid AND o.branch_id = :bid AND o.order_status != 'CANCELLED' {$dateWhereOrders}
            ");
            $ordParams = array_merge([':wid' => $wid], $paramsOrders);
            $ordStmt->execute($ordParams);
            $ordStats = $ordStmt->fetch();

            // 2. Commission Aggregations
            $commStmt = $db->prepare("
                SELECT 
                    COALESCE(SUM(CASE WHEN ct.status IN ('PENDING', 'APPROVED', 'PAID') THEN ct.commission_amount ELSE 0.00 END), 0.00) AS total_commission_accrued,
                    COALESCE(SUM(CASE WHEN ct.status = 'PENDING' THEN ct.commission_amount ELSE 0.00 END), 0.00) AS pending_commission,
                    COALESCE(SUM(CASE WHEN ct.status = 'APPROVED' THEN ct.commission_amount ELSE 0.00 END), 0.00) AS approved_commission,
                    COALESCE(SUM(CASE WHEN ct.status = 'PAID' THEN ct.commission_amount ELSE 0.00 END), 0.00) AS paid_commission
                FROM commission_transactions ct
                WHERE ct.waiter_id = :wid AND ct.branch_id = :bid {$dateWhereComm}
            ");
            $commParams = array_merge([':wid' => $wid], $paramsComm);
            $commStmt->execute($commParams);
            $commStats = $commStmt->fetch();

            $totalOrders = (int)($ordStats['total_orders'] ?? 0);
            $totalSales = (float)($ordStats['total_sales'] ?? 0.00);
            $avgOrderValue = $totalOrders > 0 ? round($totalSales / $totalOrders, 2) : 0.00;

            $matrix[] = [
                'waiter_id' => $wid,
                'waiter_name' => $w['waiter_name'],
                'employee_code' => $w['employee_code'] ?? ('WTR-' . sprintf('%03d', $wid)),
                'total_orders' => $totalOrders,
                'tables_served' => (int)($ordStats['tables_served'] ?? 0),
                'total_sales' => round($totalSales, 2),
                'paid_sales' => round((float)($ordStats['paid_sales'] ?? 0.00), 2),
                'paid_orders' => (int)($ordStats['paid_orders'] ?? 0),
                'pending_orders' => (int)($ordStats['pending_orders'] ?? 0),
                'avg_order_value' => $avgOrderValue,
                'commission_rate' => '5.0%',
                'total_commission' => round((float)($commStats['total_commission_accrued'] ?? 0.00), 2),
                'pending_commission' => round((float)($commStats['pending_commission'] ?? 0.00), 2),
                'approved_commission' => round((float)($commStats['approved_commission'] ?? 0.00), 2),
                'paid_commission' => round((float)($commStats['paid_commission'] ?? 0.00), 2),
                'status' => $w['waiter_status'] ?? 'ACTIVE'
            ];
        }

        return $matrix;
    }

    /**
     * Get comprehensive waiter drill-down performance details
     */
    public static function getWaiterDetails(int $waiterId): array {
        $db = Database::getConnection();

        $prof = self::getOrCreateProfile($waiterId);

        // Fetch Orders List
        $ordStmt = $db->prepare("
            SELECT o.*, rt.table_number, c.name AS customer_name
            FROM orders o
            LEFT JOIN restaurant_tables rt ON o.table_id = rt.id
            LEFT JOIN customers c ON o.customer_id = c.id
            WHERE o.taken_by_user_id = :wid
            ORDER BY o.created_at DESC LIMIT 50
        ");
        $ordStmt->execute([':wid' => $waiterId]);
        $orders = $ordStmt->fetchAll();

        // Fetch Commissions List
        $commStmt = $db->prepare("
            SELECT ct.*, o.order_number, cr.name AS rule_name
            FROM commission_transactions ct
            JOIN orders o ON ct.order_id = o.id
            LEFT JOIN commission_rules cr ON ct.commission_rule_id = cr.id
            WHERE ct.waiter_id = :wid
            ORDER BY ct.created_at DESC LIMIT 50
        ");
        $commStmt->execute([':wid' => $waiterId]);
        $commissions = $commStmt->fetchAll();

        return [
            'waiter' => $prof,
            'orders' => $orders,
            'commissions' => $commissions
        ];
    }
}
