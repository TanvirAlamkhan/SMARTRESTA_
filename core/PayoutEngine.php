<?php
/**
 * SMARTRESTA Core Commission Payout Engine
 * Prompt 09: Waiter Management, Commission Engine & Performance Tracking
 *
 * Manages approved commission payout settlements, row locking concurrency protection,
 * itemized payout linking, and double-payout prevention guards.
 */

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/AuditLogger.php';

class PayoutEngine {

    /**
     * Process commission payout settlement for a waiter
     */
    public static function createPayout(array $payoutData, int $managerUserId = 1): array {
        $db = Database::getConnection();

        $waiterId = (int)($payoutData['waiter_id'] ?? 0);
        $branchId = (int)($payoutData['branch_id'] ?? 1);
        $paymentMethod = !empty($payoutData['payment_method']) ? strtoupper(trim($payoutData['payment_method'])) : 'CASH';
        $referenceNumber = !empty($payoutData['reference_number']) ? trim($payoutData['reference_number']) : null;
        $notes = !empty($payoutData['notes']) ? trim($payoutData['notes']) : null;
        $requestedIds = !empty($payoutData['commission_ids']) && is_array($payoutData['commission_ids']) ? array_map('intval', $payoutData['commission_ids']) : [];

        if (!$waiterId) {
            throw new Exception("Waiter ID is required for payout settlement.");
        }

        $db->beginTransaction();

        try {
            // 1. Fetch & Lock Approved Unpaid Commission Transactions
            $sql = "
                SELECT * FROM commission_transactions
                WHERE waiter_id = :wid AND branch_id = :bid AND status = 'APPROVED'
            ";

            $params = [':wid' => $waiterId, ':bid' => $branchId];

            if (!empty($requestedIds)) {
                $inClause = implode(',', $requestedIds);
                $sql .= " AND id IN ({$inClause})";
            }

            $sql .= " FOR UPDATE";

            $stmtLock = $db->prepare($sql);
            $stmtLock->execute($params);
            $approvedCommissions = $stmtLock->fetchAll();

            if (empty($approvedCommissions)) {
                throw new Exception("No approved unpaid commission transactions available for waiter ID #{$waiterId}.");
            }

            $totalPayoutAmount = 0.00;
            foreach ($approvedCommissions as $comm) {
                $totalPayoutAmount += (float)$comm['commission_amount'];
            }

            if ($totalPayoutAmount <= 0.00) {
                throw new Exception("Approved commission payout balance is zero.");
            }

            // 2. Generate Unique Payout Number
            $payoutNum = 'PAYOUT-' . date('Ymd') . '-' . sprintf('%06d', rand(100000, 999999));

            // 3. Create Payout Record
            $insPayoutStmt = $db->prepare("
                INSERT INTO commission_payouts
                (payout_number, branch_id, waiter_id, amount, payment_method, reference_number, period_start, period_end, status, processed_by_user_id, processed_at, notes, created_at)
                VALUES (:pnum, :bid, :wid, :amt, :pmeth, :ref, NOW(), NOW(), 'PROCESSED', :puid, NOW(), :notes, NOW())
            ");
            $insPayoutStmt->execute([
                ':pnum' => $payoutNum,
                ':bid' => $branchId,
                ':wid' => $waiterId,
                ':amt' => round($totalPayoutAmount, 2),
                ':pmeth' => $paymentMethod,
                ':ref' => $referenceNumber,
                ':puid' => $managerUserId,
                ':notes' => $notes
            ]);
            $payoutId = (int)$db->lastInsertId();

            // 4. Link Payout Items & Mark Commission Status = PAID
            $insItemStmt = $db->prepare("INSERT INTO commission_payout_items (payout_id, commission_transaction_id, amount, created_at) VALUES (:pid, :cid, :amt, NOW())");
            $upCommStmt = $db->prepare("UPDATE commission_transactions SET status = 'PAID', paid_by = :puid, paid_at = NOW() WHERE id = :cid");

            foreach ($approvedCommissions as $comm) {
                $cid = (int)$comm['id'];
                $amt = (float)$comm['commission_amount'];

                $insItemStmt->execute([':pid' => $payoutId, ':cid' => $cid, ':amt' => $amt]);
                $upCommStmt->execute([':puid' => $managerUserId, ':cid' => $cid]);
            }

            AuditLogger::log($managerUserId, 'COMMISSION_PAYOUT_PROCESSED', 'commission_payouts', $payoutId, null, [
                'payout_number' => $payoutNum,
                'waiter_id' => $waiterId,
                'total_amount' => $totalPayoutAmount,
                'item_count' => count($approvedCommissions)
            ]);

            $db->commit();

            return [
                'success' => true,
                'payout_id' => $payoutId,
                'payout_number' => $payoutNum,
                'waiter_id' => $waiterId,
                'amount_paid' => round($totalPayoutAmount, 2),
                'settled_commission_count' => count($approvedCommissions),
                'status' => 'PROCESSED'
            ];

        } catch (Exception $e) {
            if ($db && $db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Get list of commission payouts
     */
    public static function getPayoutsList(int $branchId = 1, ?int $waiterId = null): array {
        $db = Database::getConnection();

        $sql = "
            SELECT cp.*, u.name AS waiter_name, wp.employee_code, m.name AS processed_by_name,
                   (SELECT COUNT(*) FROM commission_payout_items WHERE payout_id = cp.id) AS item_count
            FROM commission_payouts cp
            JOIN users u ON cp.waiter_id = u.id
            LEFT JOIN waiter_profiles wp ON u.id = wp.user_id
            LEFT JOIN users m ON cp.processed_by_user_id = m.id
            WHERE cp.branch_id = :bid
        ";

        $params = [':bid' => $branchId];

        if ($waiterId) {
            $sql .= " AND cp.waiter_id = :wid";
            $params[':wid'] = $waiterId;
        }

        $sql .= " ORDER BY cp.created_at DESC LIMIT 100";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Get detailed payout with linked commission items
     */
    public static function getPayoutDetails(int $payoutId): array {
        $db = Database::getConnection();

        $stmt = $db->prepare("
            SELECT cp.*, u.name AS waiter_name, wp.employee_code, m.name AS processed_by_name
            FROM commission_payouts cp
            JOIN users u ON cp.waiter_id = u.id
            LEFT JOIN waiter_profiles wp ON u.id = wp.user_id
            LEFT JOIN users m ON cp.processed_by_user_id = m.id
            WHERE cp.id = :pid
        ");
        $stmt->execute([':pid' => $payoutId]);
        $payout = $stmt->fetch();

        if (!$payout) {
            throw new Exception("Payout #{$payoutId} not found.");
        }

        $itemsStmt = $db->prepare("
            SELECT cpi.*, ct.order_id, ct.commission_amount, o.order_number
            FROM commission_payout_items cpi
            JOIN commission_transactions ct ON cpi.commission_transaction_id = ct.id
            JOIN orders o ON ct.order_id = o.id
            WHERE cpi.payout_id = :pid
        ");
        $itemsStmt->execute([':pid' => $payoutId]);
        $items = $itemsStmt->fetchAll();

        return [
            'payout' => $payout,
            'items' => $items
        ];
    }
}
