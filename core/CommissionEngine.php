<?php
/**
 * SMARTRESTA Core Commission Engine
 * Prompt 09: Waiter Management, Commission Engine & Performance Tracking
 *
 * Centralized server-authoritative commission calculation engine, rule resolver,
 * transaction generator, refund adjustment calculator, and approval workflow.
 */

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/AuditLogger.php';
require_once __DIR__ . '/BillingEngine.php';
require_once __DIR__ . '/WaiterEngine.php';

class CommissionEngine {

    /**
     * Resolve applicable commission rule for an order & waiter
     */
    public static function resolveCommissionRule(int $waiterId, int $branchId = 1): array {
        $db = Database::getConnection();

        // 1. Check Waiter Profile Default Rule
        $profStmt = $db->prepare("
            SELECT cr.*
            FROM waiter_profiles wp
            JOIN commission_rules cr ON wp.default_commission_rule_id = cr.id
            WHERE wp.user_id = :wid AND cr.is_active = 1
        ");
        $profStmt->execute([':wid' => $waiterId]);
        $rule = $profStmt->fetch();

        if ($rule) {
            return $rule;
        }

        // 2. Check Branch Active Rule
        $branchStmt = $db->prepare("
            SELECT * FROM commission_rules
            WHERE branch_id = :bid AND is_active = 1
            ORDER BY id ASC LIMIT 1
        ");
        $branchStmt->execute([':bid' => $branchId]);
        $rule = $branchStmt->fetch();

        if ($rule) {
            return $rule;
        }

        // 3. System Fallback Rule
        return [
            'id' => 1,
            'name' => 'Default 5% Net Sales Commission Rule',
            'code' => 'COMM-DEFAULT-5',
            'calculation_base' => 'PERCENTAGE',
            'rate' => 5.00,
            'fixed_amount' => 0.00,
            'minimum_sales' => 0.00,
            'maximum_commission' => null,
            'commission_eligible' => 'PAID_ONLY'
        ];
    }

    /**
     * Calculate server-authoritative commission for an order
     */
    public static function calculateCommission(int $orderId): array {
        $db = Database::getConnection();

        // 1. Fetch Order Financial Bill
        $bill = BillingEngine::calculateOrderBill($orderId);

        $stmtOrd = $db->prepare("SELECT taken_by_user_id, branch_id FROM orders WHERE id = :oid");
        $stmtOrd->execute([':oid' => $orderId]);
        $ordData = $stmtOrd->fetch();

        $waiterId = (int)($ordData['taken_by_user_id'] ?? 0);
        $branchId = (int)($ordData['branch_id'] ?? 1);

        if (!$waiterId) {
            return [
                'order_id' => $orderId,
                'eligible' => false,
                'reason' => 'No order taker waiter assigned to order.',
                'base_amount' => 0.00,
                'commission_amount' => 0.00
            ];
        }

        // 2. Resolve Commission Rule
        $rule = self::resolveCommissionRule($waiterId, $branchId);

        // 3. Determine Eligibility
        $netSales = $bill['subtotal'] - $bill['discount'];
        $grandTotal = $bill['grand_total'];
        $paymentStatus = $bill['payment_status'];

        $isEligible = true;
        $reason = 'Eligible for commission';

        if ($rule['commission_eligible'] === 'PAID_ONLY' && $paymentStatus !== 'PAID') {
            $isEligible = false;
            $reason = "Order payment status is {$paymentStatus} (Rule requires fully PAID).";
        }

        if ($netSales < (float)($rule['minimum_sales'] ?? 0.00)) {
            $isEligible = false;
            $reason = "Net sales (৳" . number_format($netSales, 2) . ") below minimum threshold (৳" . number_format($rule['minimum_sales'], 2) . ").";
        }

        // 4. Calculate Commission Amount based on Rule Basis
        $baseAmount = round($netSales, 2);
        $rate = (float)($rule['rate'] ?? 5.00);
        $fixedAmount = (float)($rule['fixed_amount'] ?? 0.00);
        $commissionAmount = 0.00;

        $calcBase = $rule['calculation_base'] ?? 'PERCENTAGE';

        if ($calcBase === 'PERCENTAGE') {
            $commissionAmount = round($baseAmount * ($rate / 100.00), 2);
        } else if ($calcBase === 'PER_ORDER' || $calcBase === 'FIXED_AMOUNT') {
            $commissionAmount = $fixedAmount > 0 ? $fixedAmount : $rate;
        } else if ($calcBase === 'PER_ITEM') {
            $itemCount = 0;
            foreach (($bill['items'] ?? []) as $it) {
                $itemCount += (int)$it['quantity'];
            }
            $commissionAmount = round($itemCount * ($fixedAmount > 0 ? $fixedAmount : $rate), 2);
        }

        // Apply maximum commission cap if defined
        if (!empty($rule['maximum_commission']) && (float)$rule['maximum_commission'] > 0.00) {
            $maxCap = (float)$rule['maximum_commission'];
            if ($commissionAmount > $maxCap) {
                $commissionAmount = $maxCap;
            }
        }

        return [
            'order_id' => $orderId,
            'waiter_id' => $waiterId,
            'branch_id' => $branchId,
            'eligible' => $isEligible,
            'reason' => $reason,
            'rule_id' => (int)$rule['id'],
            'rule_name' => $rule['name'],
            'calculation_base' => $calcBase,
            'base_amount' => $baseAmount,
            'rate' => $rate,
            'fixed_amount' => $fixedAmount,
            'commission_amount' => $isEligible ? round($commissionAmount, 2) : 0.00
        ];
    }

    /**
     * Process & persist commission transaction for an order (Idempotent)
     */
    public static function processOrderCommission(int $orderId, int $userId = 1): array {
        $db = Database::getConnection();

        // 1. Calculate Commission
        $calc = self::calculateCommission($orderId);

        if (!$calc['eligible'] || $calc['commission_amount'] <= 0.00) {
            return [
                'success' => false,
                'message' => $calc['reason'],
                'calculation' => $calc
            ];
        }

        $waiterId = $calc['waiter_id'];
        $idempotencyKey = "COMM-ORD-{$orderId}-WTR-{$waiterId}";

        // 2. Check Idempotency (Existing Transaction)
        $stmtExist = $db->prepare("SELECT * FROM commission_transactions WHERE order_id = :oid AND waiter_id = :wid");
        $stmtExist->execute([':oid' => $orderId, ':wid' => $waiterId]);
        $existing = $stmtExist->fetch();

        if ($existing) {
            return [
                'success' => true,
                'idempotent' => true,
                'commission_id' => (int)$existing['id'],
                'status' => $existing['status'],
                'commission_amount' => (float)$existing['commission_amount']
            ];
        }

        // 3. Insert Commission Transaction
        $insStmt = $db->prepare("
            INSERT INTO commission_transactions
            (branch_id, order_id, waiter_id, commission_rule_id, base_amount, rate, fixed_amount, commission_amount, status, idempotency_key, created_at)
            VALUES (:bid, :oid, :wid, :crid, :base, :rate, :famt, :camt, 'PENDING', :ikey, NOW())
        ");
        $insStmt->execute([
            ':bid' => $calc['branch_id'],
            ':oid' => $orderId,
            ':wid' => $waiterId,
            ':crid' => $calc['rule_id'],
            ':base' => $calc['base_amount'],
            ':rate' => $calc['rate'],
            ':famt' => $calc['fixed_amount'],
            ':camt' => $calc['commission_amount'],
            ':ikey' => $idempotencyKey
        ]);
        $commId = (int)$db->lastInsertId();

        AuditLogger::log($userId, 'COMMISSION_CREATED', 'commission_transactions', $commId, null, [
            'order_id' => $orderId,
            'waiter_id' => $waiterId,
            'base_amount' => $calc['base_amount'],
            'commission_amount' => $calc['commission_amount']
        ]);

        return [
            'success' => true,
            'commission_id' => $commId,
            'order_id' => $orderId,
            'waiter_id' => $waiterId,
            'status' => 'PENDING',
            'commission_amount' => $calc['commission_amount']
        ];
    }

    /**
     * Process Commission Reversal/Adjustment when customer refund occurs
     */
    public static function processRefundCommissionAdjustment(int $refundId, int $userId = 1): array {
        $db = Database::getConnection();

        // 1. Fetch Refund Details
        $refStmt = $db->prepare("SELECT * FROM refunds WHERE id = :rid");
        $refStmt->execute([':rid' => $refundId]);
        $refund = $refStmt->fetch();

        if (!$refund) {
            throw new Exception("Refund #{$refundId} not found.");
        }

        $orderId = (int)$refund['order_id'];
        $refundAmount = (float)$refund['refund_amount'];

        // 2. Fetch Original Commission Transactions for Order
        $commStmt = $db->prepare("SELECT * FROM commission_transactions WHERE order_id = :oid AND status IN ('PENDING', 'APPROVED', 'PAID')");
        $commStmt->execute([':oid' => $orderId]);
        $commissions = $commStmt->fetchAll();

        if (empty($commissions)) {
            return [
                'success' => true,
                'message' => 'No active commission transactions found for refunded order.'
            ];
        }

        $bill = BillingEngine::calculateOrderBill($orderId);
        $grandTotal = $bill['grand_total'];

        $adjustments = [];

        foreach ($commissions as $comm) {
            $commId = (int)$comm['id'];
            $origCommission = (float)$comm['commission_amount'];

            // Proportional Reversal Ratio
            $reversalRatio = $grandTotal > 0 ? min(1.0, $refundAmount / $grandTotal) : 1.0;
            $reversalAmount = round($origCommission * $reversalRatio, 2);

            if ($reversalAmount <= 0.00) continue;

            // Insert Adjustment Record
            $adjStmt = $db->prepare("
                INSERT INTO commission_adjustments
                (commission_transaction_id, refund_id, reason, adjustment_amount, adjustment_type, created_by, created_at)
                VALUES (:cid, :rid, :reason, :amt, 'REFUND_REVERSAL', :uid, NOW())
            ");
            $reasonText = "Customer refund #{$refund['refund_number']} adjustment (-৳" . number_format($reversalAmount, 2) . ")";
            $adjStmt->execute([
                ':cid' => $commId,
                ':rid' => $refundId,
                ':reason' => $reasonText,
                ':amt' => -$reversalAmount,
                ':uid' => $userId
            ]);

            // Update Commission Record Amount & Status
            $newCommissionAmt = max(0.00, round($origCommission - $reversalAmount, 2));
            $newStatus = $newCommissionAmt <= 0.00 ? 'REVERSED' : 'ADJUSTED';

            $upStmt = $db->prepare("UPDATE commission_transactions SET commission_amount = :camt, status = :st, reason = :reason WHERE id = :cid");
            $upStmt->execute([
                ':camt' => $newCommissionAmt,
                ':st' => $newStatus,
                ':reason' => $reasonText,
                ':cid' => $commId
            ]);

            AuditLogger::log($userId, 'COMMISSION_ADJUSTED', 'commission_transactions', $commId, null, [
                'refund_id' => $refundId,
                'original_commission' => $origCommission,
                'reversal_amount' => $reversalAmount,
                'new_commission_amount' => $newCommissionAmt
            ]);

            $adjustments[] = [
                'commission_id' => $commId,
                'reversal_amount' => $reversalAmount,
                'new_commission_amount' => $newCommissionAmt,
                'new_status' => $newStatus
            ];
        }

        return [
            'success' => true,
            'refund_id' => $refundId,
            'adjustments' => $adjustments
        ];
    }

    /**
     * Batch Approve Pending Commissions
     */
    public static function approveCommissions(array $commissionIds, int $managerUserId = 1): array {
        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            $approvedCount = 0;
            foreach ($commissionIds as $cid) {
                $cid = (int)$cid;

                $stmtLock = $db->prepare("SELECT * FROM commission_transactions WHERE id = :cid FOR UPDATE");
                $stmtLock->execute([':cid' => $cid]);
                $comm = $stmtLock->fetch();

                if ($comm && in_array($comm['status'], ['PENDING', 'ADJUSTED'])) {
                    $upStmt = $db->prepare("
                        UPDATE commission_transactions
                        SET status = 'APPROVED', approved_by_user_id = :muid, approved_at = NOW()
                        WHERE id = :cid
                    ");
                    $upStmt->execute([':muid' => $managerUserId, ':cid' => $cid]);
                    $approvedCount++;

                    AuditLogger::log($managerUserId, 'COMMISSION_APPROVED', 'commission_transactions', $cid, null, [
                        'waiter_id' => $comm['waiter_id'],
                        'amount' => $comm['commission_amount']
                    ]);
                }
            }

            $db->commit();
            return ['success' => true, 'approved_count' => $approvedCount];

        } catch (Exception $e) {
            if ($db && $db->inTransaction()) $db->rollBack();
            throw $e;
        }
    }

    /**
     * Batch Reject Pending Commissions with Reason
     */
    public static function rejectCommissions(array $commissionIds, string $reason, int $managerUserId = 1): array {
        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            $rejectedCount = 0;
            foreach ($commissionIds as $cid) {
                $cid = (int)$cid;

                $stmtLock = $db->prepare("SELECT * FROM commission_transactions WHERE id = :cid FOR UPDATE");
                $stmtLock->execute([':cid' => $cid]);
                $comm = $stmtLock->fetch();

                if ($comm && in_array($comm['status'], ['PENDING', 'APPROVED'])) {
                    $upStmt = $db->prepare("
                        UPDATE commission_transactions
                        SET status = 'REJECTED', reason = :reason
                        WHERE id = :cid
                    ");
                    $upStmt->execute([':reason' => $reason, ':cid' => $cid]);
                    $rejectedCount++;

                    AuditLogger::log($managerUserId, 'COMMISSION_REJECTED', 'commission_transactions', $cid, null, [
                        'reason' => $reason
                    ]);
                }
            }

            $db->commit();
            return ['success' => true, 'rejected_count' => $rejectedCount];

        } catch (Exception $e) {
            if ($db && $db->inTransaction()) $db->rollBack();
            throw $e;
        }
    }

    /**
     * Get list of commission transactions with filters
     */
    public static function getCommissionsList(int $branchId = 1, ?int $waiterId = null, ?string $status = null, ?string $startDate = null, ?string $endDate = null): array {
        $db = Database::getConnection();

        $sql = "
            SELECT ct.*, o.order_number, o.total AS order_total, u.name AS waiter_name, wp.employee_code,
                   cr.name AS rule_name, m.name AS approved_by_name
            FROM commission_transactions ct
            JOIN orders o ON ct.order_id = o.id
            JOIN users u ON ct.waiter_id = u.id
            LEFT JOIN waiter_profiles wp ON u.id = wp.user_id
            LEFT JOIN commission_rules cr ON ct.commission_rule_id = cr.id
            LEFT JOIN users m ON ct.approved_by_user_id = m.id
            WHERE ct.branch_id = :bid
        ";

        $params = [':bid' => $branchId];

        if ($waiterId) {
            $sql .= " AND ct.waiter_id = :wid";
            $params[':wid'] = $waiterId;
        }

        if ($status) {
            $sql .= " AND ct.status = :st";
            $params[':st'] = $status;
        }

        if ($startDate) {
            $sql .= " AND ct.created_at >= :sdate";
            $params[':sdate'] = $startDate . ' 00:00:00';
        }

        if ($endDate) {
            $sql .= " AND ct.created_at <= :edate";
            $params[':edate'] = $endDate . ' 23:59:59';
        }

        $sql .= " ORDER BY ct.created_at DESC LIMIT 100";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
