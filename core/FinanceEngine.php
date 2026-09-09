<?php
/**
 * SMARTRESTA Core Finance Engine
 * Prompt 13: Finance, Expenses, Shifts, Cash Management, Reconciliation & Day Closing
 */

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/AuditLogger.php';

class FinanceEngine {

    /**
     * Get Real-Time Finance Overview KPIs for Branch & Business Date
     */
    public static function getFinanceSummary(int $branchId = 1, ?string $businessDate = null): array {
        $db = Database::getConnection();
        $date = !empty($businessDate) ? date('Y-m-d', strtotime($businessDate)) : date('Y-m-d');

        $start = $date . ' 00:00:00';
        $end = $date . ' 23:59:59';

        // 1. Sales & Orders Aggregates
        $stmtSales = $db->prepare("
            SELECT 
                COUNT(id) AS total_orders,
                COALESCE(SUM(CASE WHEN order_status NOT IN ('CANCELLED', 'REFUNDED') THEN subtotal + service_charge + delivery_charge ELSE 0 END), 0) AS gross_sales,
                COALESCE(SUM(CASE WHEN order_status NOT IN ('CANCELLED', 'REFUNDED') THEN discount ELSE 0 END), 0) AS total_discounts,
                COALESCE(SUM(CASE WHEN order_status NOT IN ('CANCELLED', 'REFUNDED') THEN tax ELSE 0 END), 0) AS total_tax,
                COALESCE(SUM(CASE WHEN order_status NOT IN ('CANCELLED', 'REFUNDED') THEN total ELSE 0 END), 0) AS net_sales,
                COALESCE(SUM(CASE WHEN order_status = 'REFUNDED' THEN total ELSE 0 END), 0) AS total_refunds
            FROM orders
            WHERE branch_id = :bid AND created_at BETWEEN :start AND :end
        ");
        $stmtSales->execute([':bid' => $branchId, ':start' => $start, ':end' => $end]);
        $sales = $stmtSales->fetch(PDO::FETCH_ASSOC);

        // 2. Payments Collected by Category (Cash vs Non-Cash)
        $stmtPay = $db->prepare("
            SELECT 
                pm.type AS method_type,
                COALESCE(SUM(p.amount), 0) AS total_amount
            FROM payments p
            JOIN payment_methods pm ON p.payment_method_id = pm.id
            WHERE p.branch_id = :bid AND p.status = 'COMPLETED' AND p.created_at BETWEEN :start AND :end
            GROUP BY pm.type
        ");
        $stmtPay->execute([':bid' => $branchId, ':start' => $start, ':end' => $end]);
        $payRows = $stmtPay->fetchAll(PDO::FETCH_ASSOC);

        $cashCollected = 0.00;
        $nonCashCollected = 0.00;
        foreach ($payRows as $r) {
            if ($r['method_type'] === 'CASH') {
                $cashCollected += (float)$r['total_amount'];
            } else {
                $nonCashCollected += (float)$r['total_amount'];
            }
        }

        // 3. Operating Expenses
        $stmtExp = $db->prepare("
            SELECT COALESCE(SUM(amount), 0) FROM expenses 
            WHERE branch_id = :bid AND status IN ('APPROVED', 'PAID') AND (expense_date = :date OR (expense_date IS NULL AND created_at BETWEEN :start AND :end))
        ");
        $stmtExp->execute([':bid' => $branchId, ':date' => $date, ':start' => $start, ':end' => $end]);
        $totalExpenses = (float)$stmtExp->fetchColumn();

        // 4. Open Shifts Count
        $stmtShifts = $db->prepare("
            SELECT COUNT(id) FROM shifts WHERE branch_id = :bid AND status = 'OPEN'
        ");
        $stmtShifts->execute([':bid' => $branchId]);
        $openShiftsCount = (int)$stmtShifts->fetchColumn();

        // 5. Day Closing Status
        $stmtDayClose = $db->prepare("
            SELECT status FROM day_closings WHERE branch_id = :bid AND business_date = :bdate
        ");
        $stmtDayClose->execute([':bid' => $branchId, ':bdate' => $date]);
        $dayStatus = $stmtDayClose->fetchColumn() ?: 'OPEN';

        return [
            'business_date' => $date,
            'day_status' => $dayStatus,
            'kpis' => [
                'gross_sales' => (float)$sales['gross_sales'],
                'discounts' => (float)$sales['total_discounts'],
                'tax' => (float)$sales['total_tax'],
                'refunds' => (float)$sales['total_refunds'],
                'net_sales' => (float)$sales['net_sales'],
                'cash_collected' => round($cashCollected, 2),
                'non_cash_collected' => round($nonCashCollected, 2),
                'total_collected' => round($cashCollected + $nonCashCollected, 2),
                'total_expenses' => round($totalExpenses, 2),
                'net_cash_flow' => round($cashCollected - $totalExpenses, 2),
                'open_shifts_count' => $openShiftsCount
            ]
        ];
    }

    // =========================================================================
    // SHIFT & CASH DRAWER MANAGEMENT
    // =========================================================================

    /**
     * Fetch active open shift for user / drawer / branch
     */
    public static function getActiveShift(int $userId, int $branchId = 1): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT s.*, cd.name AS drawer_name, cd.code AS drawer_code, u.name AS cashier_name
            FROM shifts s
            JOIN cash_drawers cd ON s.cash_drawer_id = cd.id
            JOIN users u ON s.user_id = u.id
            WHERE s.user_id = :uid AND s.branch_id = :bid AND s.status = 'OPEN'
            ORDER BY s.id DESC LIMIT 1
        ");
        $stmt->execute([':uid' => $userId, ':bid' => $branchId]);
        $shift = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$shift) return null;

        // Calculate live expected cash
        $shift['expected_cash'] = self::calculateExpectedCash((int)$shift['id']);
        return $shift;
    }

    /**
     * Open a new cashier shift (Transactional)
     */
    public static function openShift(int $branchId, int $userId, int $drawerId, float $openingCash, ?string $notes = null): array {
        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            // Check for existing active shift for user
            $checkStmt = $db->prepare("
                SELECT id FROM shifts WHERE user_id = :uid AND branch_id = :bid AND status = 'OPEN' FOR UPDATE
            ");
            $checkStmt->execute([':uid' => $userId, ':bid' => $branchId]);
            if ($checkStmt->fetch()) {
                throw new Exception("Cashier user already has an active open shift. Please close active shift first.");
            }

            // Insert Shift Record
            $insStmt = $db->prepare("
                INSERT INTO shifts (branch_id, user_id, cash_drawer_id, opened_at, opening_balance, opening_cash, expected_cash, status, notes, created_at)
                VALUES (:bid, :uid, :did, NOW(), :opencash1, :opencash2, :opencash3, 'OPEN', :notes, NOW())
            ");
            $insStmt->execute([
                ':bid' => $branchId,
                ':uid' => $userId,
                ':did' => $drawerId,
                ':opencash1' => $openingCash,
                ':opencash2' => $openingCash,
                ':opencash3' => $openingCash,
                ':notes' => $notes
            ]);
            $shiftId = (int)$db->lastInsertId();

            // Record Opening Cash Movement
            $cmStmt = $db->prepare("
                INSERT INTO cash_movements (branch_id, shift_id, cash_drawer_id, movement_type, amount, direction, reference_type, reference_id, user_id, reason, created_at)
                VALUES (:bid, :sid1, :did, 'OPENING_BALANCE', :amt, 'IN', 'shifts', :sid2, :uid, 'Opening shift cash balance', NOW())
            ");
            $cmStmt->execute([
                ':bid' => $branchId,
                ':sid1' => $shiftId,
                ':did' => $drawerId,
                ':amt' => $openingCash,
                ':sid2' => $shiftId,
                ':uid' => $userId
            ]);

            $db->commit();

            AuditLogger::log('SHIFT_OPENED', 'shifts', $shiftId, null, [
                'user_id' => $userId,
                'opening_cash' => $openingCash
            ]);

            return self::getActiveShift($userId, $branchId);

        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * Calculate Expected Cash for Shift based on immutable cash movements
     */
    public static function calculateExpectedCash(int $shiftId): float {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT 
                COALESCE(SUM(CASE WHEN direction = 'IN' THEN amount ELSE 0 END), 0) -
                COALESCE(SUM(CASE WHEN direction = 'OUT' AND movement_type != 'CLOSING_BALANCE' THEN amount ELSE 0 END), 0) AS expected_cash
            FROM cash_movements
            WHERE shift_id = :sid
        ");
        $stmt->execute([':sid' => $shiftId]);
        return round((float)$stmt->fetchColumn(), 2);
    }

    /**
     * Close Cashier Shift & Record Physical Cash Count (Transactional)
     */
    public static function closeShift(int $shiftId, float $actualCash, array $denominations, int $userId, ?string $notes = null): array {
        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            $shiftStmt = $db->prepare("SELECT * FROM shifts WHERE id = :sid FOR UPDATE");
            $shiftStmt->execute([':sid' => $shiftId]);
            $shift = $shiftStmt->fetch(PDO::FETCH_ASSOC);

            if (!$shift) {
                throw new Exception("Shift #{$shiftId} not found.");
            }
            if ($shift['status'] === 'CLOSED') {
                throw new Exception("Shift #{$shiftId} is already closed.");
            }

            $expectedCash = self::calculateExpectedCash($shiftId);
            $cashDifference = round($actualCash - $expectedCash, 2);

            // Update Shift Status
            $updStmt = $db->prepare("
                UPDATE shifts 
                SET closed_at = NOW(),
                    closing_balance = :actcash1,
                    actual_cash = :actcash2,
                    expected_cash = :expcash,
                    cash_difference = :diff,
                    status = 'CLOSED',
                    notes = :notes
                WHERE id = :sid
            ");
            $updStmt->execute([
                ':actcash1' => $actualCash,
                ':actcash2' => $actualCash,
                ':expcash' => $expectedCash,
                ':diff' => $cashDifference,
                ':notes' => $notes,
                ':sid' => $shiftId
            ]);

            // Record Closing Cash Movement
            $cmStmt = $db->prepare("
                INSERT INTO cash_movements (branch_id, shift_id, cash_drawer_id, movement_type, amount, direction, reference_type, reference_id, user_id, reason, notes, created_at)
                VALUES (:bid, :sid1, :did, 'CLOSING_BALANCE', :amt, 'OUT', 'shifts', :sid2, :uid, 'Shift closing physical cash count', :denoms, NOW())
            ");
            $cmStmt->execute([
                ':bid' => $shift['branch_id'],
                ':sid1' => $shiftId,
                ':did' => $shift['cash_drawer_id'],
                ':amt' => $actualCash,
                ':sid2' => $shiftId,
                ':uid' => $userId,
                ':denoms' => json_encode($denominations)
            ]);

            $db->commit();

            AuditLogger::log('SHIFT_CLOSED', 'shifts', $shiftId, null, [
                'expected_cash' => $expectedCash,
                'actual_cash' => $actualCash,
                'difference' => $cashDifference
            ]);

            return [
                'shift_id' => $shiftId,
                'expected_cash' => $expectedCash,
                'actual_cash' => $actualCash,
                'difference' => $cashDifference,
                'status' => 'CLOSED'
            ];

        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * Record Cash Movement (Cash In / Cash Out / Petty Cash)
     */
    public static function recordCashMovement(array $data, int $userId): array {
        $db = Database::getConnection();

        $branchId = (int)($data['branch_id'] ?? 1);
        $amount = (float)($data['amount'] ?? 0);
        $type = strtoupper(trim($data['movement_type'] ?? 'CASH_IN'));
        $reason = trim($data['reason'] ?? 'Manual Cash Movement');
        $notes = trim($data['notes'] ?? '');

        if ($amount <= 0) {
            throw new Exception("Cash movement amount must be greater than zero.");
        }
        if (!in_array($type, ['CASH_IN', 'CASH_OUT', 'CASH_EXPENSE', 'CASH_ADJUSTMENT', 'CASH_TRANSFER'])) {
            throw new Exception("Invalid cash movement type.");
        }

        $direction = in_array($type, ['CASH_OUT', 'CASH_EXPENSE']) ? 'OUT' : 'IN';
        if ($type === 'CASH_ADJUSTMENT' && isset($data['direction'])) {
            $direction = strtoupper($data['direction']);
        }

        // Get Active Shift if open
        $activeShift = self::getActiveShift($userId, $branchId);
        $shiftId = $activeShift ? (int)$activeShift['id'] : null;
        $drawerId = $activeShift ? (int)$activeShift['cash_drawer_id'] : 1;

        $stmt = $db->prepare("
            INSERT INTO cash_movements (branch_id, shift_id, cash_drawer_id, movement_type, amount, direction, user_id, reason, notes, created_at)
            VALUES (:bid, :sid, :did, :mtype, :amt, :dir, :uid, :reason, :notes, NOW())
        ");
        $stmt->execute([
            ':bid' => $branchId,
            ':sid' => $shiftId,
            ':did' => $drawerId,
            ':mtype' => $type,
            ':amt' => $amount,
            ':dir' => $direction,
            ':uid' => $userId,
            ':reason' => $reason,
            ':notes' => $notes
        ]);

        $movementId = (int)$db->lastInsertId();

        AuditLogger::log('CASH_MOVEMENT_RECORDED', 'cash_movements', $movementId, null, [
            'type' => $type,
            'amount' => $amount,
            'direction' => $direction
        ]);

        return [
            'movement_id' => $movementId,
            'shift_id' => $shiftId,
            'amount' => $amount,
            'direction' => $direction
        ];
    }

    // =========================================================================
    // EXPENSE MANAGEMENT WORKFLOW
    // =========================================================================

    /**
     * Get Server-Side Paginated Expenses List
     */
    public static function getExpensesList(int $branchId = 1, array $filters = [], int $page = 1, int $perPage = 25): array {
        $db = Database::getConnection();

        $where = ["e.branch_id = :bid"];
        $params = [':bid' => $branchId];

        if (!empty($filters['status'])) {
            $where[] = "e.status = :status";
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['category_id'])) {
            $where[] = "e.category_id = :catid";
            $params[':catid'] = (int)$filters['category_id'];
        }
        if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
            $where[] = "(e.expense_date BETWEEN :dfrom AND :dto OR (e.expense_date IS NULL AND e.created_at BETWEEN :start AND :end))";
            $params[':dfrom'] = $filters['date_from'];
            $params[':dto'] = $filters['date_to'];
            $params[':start'] = $filters['date_from'] . ' 00:00:00';
            $params[':end'] = $filters['date_to'] . ' 23:59:59';
        }

        $whereClause = implode(" AND ", $where);

        $countStmt = $db->prepare("SELECT COUNT(e.id) FROM expenses e WHERE {$whereClause}");
        $countStmt->execute($params);
        $totalItems = (int)$countStmt->fetchColumn();

        $totalPages = $totalItems > 0 ? (int)ceil($totalItems / $perPage) : 1;
        $page = max(1, min($page, $totalPages));
        $offset = ($page - 1) * $perPage;

        $stmt = $db->prepare("
            SELECT 
                e.*,
                ec.name AS category_name,
                pm.name AS payment_method_name,
                u_rec.name AS recorded_by_name,
                u_app.name AS approved_by_name
            FROM expenses e
            LEFT JOIN expense_categories ec ON e.category_id = ec.id
            LEFT JOIN payment_methods pm ON e.payment_method_id = pm.id
            LEFT JOIN users u_rec ON e.recorded_by_user_id = u_rec.id
            LEFT JOIN users u_app ON e.approved_by_user_id = u_app.id
            WHERE {$whereClause}
            ORDER BY e.id DESC
            LIMIT {$perPage} OFFSET {$offset}
        ");
        $stmt->execute($params);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
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
     * Create Operating Expense
     */
    public static function createExpense(array $data, int $userId): array {
        $db = Database::getConnection();

        $branchId = (int)($data['branch_id'] ?? 1);
        $categoryId = (int)($data['category_id'] ?? 1);
        $amount = (float)($data['amount'] ?? 0);
        $title = trim($data['title'] ?? 'Operating Expense');
        $description = trim($data['description'] ?? '');
        $expenseDate = !empty($data['expense_date']) ? $data['expense_date'] : date('Y-m-d');
        $paymentMethodId = (int)($data['payment_method_id'] ?? 1);

        if ($amount <= 0) {
            throw new Exception("Expense amount must be greater than zero.");
        }

        $stmt = $db->prepare("
            INSERT INTO expenses (branch_id, category_id, amount, expense_date, payment_method_id, title, description, status, recorded_by_user_id, created_at)
            VALUES (:bid, :catid, :amt, :edate, :pmid, :title, :desc, 'APPROVED', :uid, NOW())
        ");
        $stmt->execute([
            ':bid' => $branchId,
            ':catid' => $categoryId,
            ':amt' => $amount,
            ':edate' => $expenseDate,
            ':pmid' => $paymentMethodId,
            ':title' => $title,
            ':desc' => $description,
            ':uid' => $userId
        ]);

        $expenseId = (int)$db->lastInsertId();

        AuditLogger::log('EXPENSE_CREATED', 'expenses', $expenseId, null, [
            'title' => $title,
            'amount' => $amount
        ]);

        return ['expense_id' => $expenseId, 'amount' => $amount, 'status' => 'APPROVED'];
    }

    /**
     * Pay Operating Expense & Deduct Cash if Payment Method is Cash
     */
    public static function payExpense(int $expenseId, int $paymentMethodId, int $userId): array {
        $db = Database::getConnection();

        $stmt = $db->prepare("SELECT * FROM expenses WHERE id = :eid");
        $stmt->execute([':eid' => $expenseId]);
        $expense = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$expense) throw new Exception("Expense #{$expenseId} not found.");

        $upd = $db->prepare("
            UPDATE expenses 
            SET status = 'PAID', payment_method_id = :pmid, paid_at = NOW(), approved_by_user_id = :uid
            WHERE id = :eid
        ");
        $upd->execute([':pmid' => $paymentMethodId, ':uid' => $userId, ':eid' => $expenseId]);

        // Check if payment method is CASH
        $pmStmt = $db->prepare("SELECT type FROM payment_methods WHERE id = :pmid");
        $pmStmt->execute([':pmid' => $paymentMethodId]);
        $pmType = $pmStmt->fetchColumn();

        if ($pmType === 'CASH') {
            self::recordCashMovement([
                'branch_id' => $expense['branch_id'],
                'amount' => $expense['amount'],
                'movement_type' => 'CASH_EXPENSE',
                'reason' => "Paid Expense: " . $expense['title'],
                'notes' => "Expense #" . $expenseId
            ], $userId);
        }

        AuditLogger::log('EXPENSE_PAID', 'expenses', $expenseId, null, ['amount' => $expense['amount']]);

        return ['expense_id' => $expenseId, 'status' => 'PAID'];
    }

    // =========================================================================
    // DAY CLOSING & IMMUTABLE SNAPSHOT ENGINE
    // =========================================================================

    /**
     * Pre-Check Business Day Close Audit
     */
    public static function getDayClosingReview(int $branchId = 1, ?string $businessDate = null): array {
        $date = !empty($businessDate) ? date('Y-m-d', strtotime($businessDate)) : date('Y-m-d');
        $summary = self::getFinanceSummary($branchId, $date);

        $db = Database::getConnection();

        // Blocking checks
        $openShiftsCount = $summary['kpis']['open_shifts_count'];

        $stmtUnpaid = $db->prepare("
            SELECT COUNT(id) FROM orders 
            WHERE branch_id = :bid AND created_at BETWEEN :start AND :end AND payment_status = 'UNPAID' AND order_status NOT IN ('CANCELLED', 'REFUNDED')
        ");
        $stmtUnpaid->execute([':bid' => $branchId, ':start' => $date . ' 00:00:00', ':end' => $date . ' 23:59:59']);
        $unpaidOrdersCount = (int)$stmtUnpaid->fetchColumn();

        $canClose = ($openShiftsCount === 0);

        return [
            'business_date' => $date,
            'can_close' => $canClose,
            'blocking_reasons' => [
                'open_shifts' => $openShiftsCount,
                'unpaid_orders' => $unpaidOrdersCount
            ],
            'financial_summary' => $summary['kpis']
        ];
    }

    /**
     * Execute Business Day Close & Freeze Immutable Financial Snapshot (Transactional)
     */
    public static function closeBusinessDay(int $branchId, string $businessDate, int $userId): array {
        $db = Database::getConnection();
        $date = date('Y-m-d', strtotime($businessDate));

        $db->beginTransaction();

        try {
            // Check existing day close status
            $checkStmt = $db->prepare("
                SELECT id, status FROM day_closings WHERE branch_id = :bid AND business_date = :bdate FOR UPDATE
            ");
            $checkStmt->execute([':bid' => $branchId, ':bdate' => $date]);
            $existing = $checkStmt->fetch(PDO::FETCH_ASSOC);

            if ($existing && $existing['status'] === 'CLOSED') {
                throw new Exception("Business day {$date} is already closed.");
            }

            // Run pre-check
            $review = self::getDayClosingReview($branchId, $date);
            if (!$review['can_close']) {
                throw new Exception("Cannot close business day {$date}. There are still {$review['blocking_reasons']['open_shifts']} active open shifts.");
            }

            $kpis = $review['financial_summary'];
            $snapshotJson = json_encode([
                'business_date' => $date,
                'closed_by_user_id' => $userId,
                'closed_at' => date('Y-m-d H:i:s'),
                'kpis' => $kpis
            ]);

            if ($existing) {
                $stmt = $db->prepare("
                    UPDATE day_closings 
                    SET status = 'CLOSED',
                        gross_sales = :gross, discounts = :disc, tax = :tax, net_sales = :net,
                        cash_collected = :cash, non_cash_collected = :noncash, total_expenses = :exp,
                        snapshot_data = :snap, closed_at = NOW(), closed_by_user_id = :uid
                    WHERE id = :id
                ");
                $stmt->execute([
                    ':gross' => $kpis['gross_sales'], ':disc' => $kpis['discounts'], ':tax' => $kpis['tax'], ':net' => $kpis['net_sales'],
                    ':cash' => $kpis['cash_collected'], ':noncash' => $kpis['non_cash_collected'], ':exp' => $kpis['total_expenses'],
                    ':snap' => $snapshotJson, ':uid' => $userId, ':id' => $existing['id']
                ]);
                $dayCloseId = (int)$existing['id'];
            } else {
                $stmt = $db->prepare("
                    INSERT INTO day_closings (branch_id, business_date, status, gross_sales, discounts, tax, net_sales, cash_collected, non_cash_collected, total_expenses, snapshot_data, closed_at, closed_by_user_id, created_at)
                    VALUES (:bid, :bdate, 'CLOSED', :gross, :disc, :tax, :net, :cash, :noncash, :exp, :snap, NOW(), :uid, NOW())
                ");
                $stmt->execute([
                    ':bid' => $branchId, ':bdate' => $date,
                    ':gross' => $kpis['gross_sales'], ':disc' => $kpis['discounts'], ':tax' => $kpis['tax'], ':net' => $kpis['net_sales'],
                    ':cash' => $kpis['cash_collected'], ':noncash' => $kpis['non_cash_collected'], ':exp' => $kpis['total_expenses'],
                    ':snap' => $snapshotJson, ':uid' => $userId
                ]);
                $dayCloseId = (int)$db->lastInsertId();
            }

            $db->commit();

            AuditLogger::log('DAY_CLOSED', 'day_closings', $dayCloseId, null, [
                'business_date' => $date,
                'net_sales' => $kpis['net_sales'],
                'closed_by' => $userId
            ]);

            return [
                'day_close_id' => $dayCloseId,
                'business_date' => $date,
                'status' => 'CLOSED',
                'summary' => $kpis
            ];

        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }
}
