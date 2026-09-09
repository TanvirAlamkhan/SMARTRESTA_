<?php
/**
 * SMARTRESTA Core Refund Engine
 * Prompt 08: Payment & Billing Engine, Settlement, Receipts & Refunds
 *
 * Handles partial and full payment reversals with refundable balance checks,
 * transaction safety, and audit logging.
 */

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/AuditLogger.php';
require_once __DIR__ . '/BillingEngine.php';
require_once __DIR__ . '/CommissionEngine.php';
require_once __DIR__ . '/InventoryEngine.php';

class RefundEngine {

    /**
     * Process partial or full refund against a recorded payment
     */
    public static function processRefund(array $refundData, int $userId = 1): array {
        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            $paymentId = (int)($refundData['payment_id'] ?? 0);
            $refundAmount = (float)($refundData['refund_amount'] ?? ($refundData['amount'] ?? 0.00));
            $reason = !empty($refundData['reason']) ? trim($refundData['reason']) : '';

            if (!$paymentId) {
                throw new Exception("Payment ID is required for refund processing.");
            }

            if ($refundAmount <= 0.00) {
                throw new Exception("Refund amount must be greater than zero.");
            }

            if (empty($reason)) {
                throw new Exception("Refund reason is mandatory.");
            }

            // 1. Lock Payment & Order rows
            $payStmt = $db->prepare("
                SELECT p.*, o.order_number, o.branch_id
                FROM payments p
                JOIN orders o ON p.order_id = o.id
                WHERE p.id = :pid
                FOR UPDATE
            ");
            $payStmt->execute([':pid' => $paymentId]);
            $payment = $payStmt->fetch();

            if (!$payment) {
                throw new Exception("Payment #{$paymentId} not found");
            }

            if ($payment['status'] !== 'COMPLETED') {
                throw new Exception("Cannot refund a payment with status '{$payment['status']}'.");
            }

            // 2. Fetch Existing Refunds Sum for this Payment
            $refSumStmt = $db->prepare("
                SELECT COALESCE(SUM(refund_amount), 0.00) 
                FROM refunds 
                WHERE payment_id = :pid AND status IN ('PROCESSED', 'APPROVED')
            ");
            $refSumStmt->execute([':pid' => $paymentId]);
            $previousRefunds = (float)$refSumStmt->fetchColumn();

            $paidAmount = (float)$payment['amount'];
            $maxRefundable = max(0.00, round($paidAmount - $previousRefunds, 2));

            if ($refundAmount > ($maxRefundable + 0.01)) {
                throw new Exception("Requested refund (৳" . number_format($refundAmount, 2) . ") exceeds maximum refundable balance (৳" . number_format($maxRefundable, 2) . ") for this payment.");
            }

            // 3. Create Refund Record
            $refundNum = 'REF-' . date('Ymd') . '-' . sprintf('%06d', rand(100000, 999999));
            $insStmt = $db->prepare("
                INSERT INTO refunds 
                (refund_number, branch_id, payment_id, order_id, refund_amount, reason, status, processed_by_user_id, approved_by_user_id, created_at)
                VALUES (:rnum, :bid, :pid, :oid, :ramt, :reason, 'PROCESSED', :puid, :auid, NOW())
            ");
            $insStmt->execute([
                ':rnum' => $refundNum,
                ':bid' => $payment['branch_id'],
                ':pid' => $paymentId,
                ':oid' => $payment['order_id'],
                ':ramt' => $refundAmount,
                ':reason' => $reason,
                ':puid' => $userId,
                ':auid' => $userId
            ]);
            $refundId = (int)$db->lastInsertId();

            // 4. Recalculate Bill & Update Order Payment Status
            $updatedBill = BillingEngine::calculateOrderBill((int)$payment['order_id']);
            $upOrdStmt = $db->prepare("UPDATE orders SET payment_status = :pst WHERE id = :oid");
            $upOrdStmt->execute([':pst' => $updatedBill['payment_status'], ':oid' => $payment['order_id']]);

            AuditLogger::log($userId, 'REFUND_PROCESSED', 'refunds', $refundId, null, [
                'payment_id' => $paymentId,
                'order_id' => $payment['order_id'],
                'refund_amount' => $refundAmount,
                'reason' => $reason
            ]);

            // Trigger Refund Commission Adjustment
            try {
                CommissionEngine::processRefundCommissionAdjustment($refundId, $userId);
            } catch (Exception $e) {
                // Non-blocking fallback
            }

            // Trigger Inventory Consumption Reversal
            try {
                InventoryEngine::reverseOrderConsumption((int)$payment['order_id'], $userId, $reason);
            } catch (Exception $e) {
                // Non-blocking fallback
            }

            $db->commit();

            return [
                'success' => true,
                'refund_id' => $refundId,
                'refund_number' => $refundNum,
                'refund_amount' => $refundAmount,
                'remaining_refundable' => max(0.00, round($maxRefundable - $refundAmount, 2)),
                'bill' => $updatedBill
            ];

        } catch (Exception $e) {
            if ($db && $db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Retrieve refunds list
     */
    public static function getRefundsList(int $branchId = 1, ?int $orderId = null): array {
        $db = Database::getConnection();
        $params = [':bid' => $branchId];

        $sql = "
            SELECT r.*, p.payment_number, p.amount AS original_payment_amount,
                   o.order_number, u.name AS processed_by_name
            FROM refunds r
            JOIN payments p ON r.payment_id = p.id
            JOIN orders o ON r.order_id = o.id
            LEFT JOIN users u ON r.processed_by_user_id = u.id
            WHERE r.branch_id = :bid
        ";

        if ($orderId) {
            $sql .= " AND r.order_id = :oid";
            $params[':oid'] = $orderId;
        }

        $sql .= " ORDER BY r.created_at DESC LIMIT 100";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
