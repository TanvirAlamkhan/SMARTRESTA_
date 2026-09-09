<?php
/**
 * SMARTRESTA Core Payment Engine
 * Prompt 08: Payment & Billing Engine, Settlement, Receipts & Refunds
 *
 * Handles single & split payments, idempotency key validation, concurrency protection,
 * payment allocations, receipt generation, and payment history.
 */

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/AuditLogger.php';
require_once __DIR__ . '/BillingEngine.php';
require_once __DIR__ . '/ReceiptEngine.php';
require_once __DIR__ . '/CommissionEngine.php';

class PaymentEngine {

    /**
     * Process single or split payment against an order or dining session
     */
    public static function processPayment(array $paymentData, int $userId = 1): array {
        $db = Database::getConnection();

        // 1. Check Idempotency Key
        $idempotencyKey = !empty($paymentData['idempotency_key']) ? trim($paymentData['idempotency_key']) : null;
        if ($idempotencyKey) {
            $stmtIdem = $db->prepare("SELECT id, order_id, amount, status FROM payments WHERE idempotency_key = :ikey");
            $stmtIdem->execute([':ikey' => $idempotencyKey]);
            $existing = $stmtIdem->fetch();
            if ($existing) {
                // Idempotent Return
                $bill = BillingEngine::calculateOrderBill((int)$existing['order_id']);
                return [
                    'payment_id' => (int)$existing['id'],
                    'order_id' => (int)$existing['order_id'],
                    'amount_paid' => (float)$existing['amount'],
                    'idempotent' => true,
                    'bill' => $bill
                ];
            }
        }

        $db->beginTransaction();

        try {
            $orderId = (int)($paymentData['order_id'] ?? 0);
            if (!$orderId) {
                throw new Exception("Order ID is required for payment processing.");
            }

            // 2. Lock Order row for concurrency protection
            $stmtLock = $db->prepare("SELECT id, branch_id, dining_session_id, order_number, order_status, payment_status FROM orders WHERE id = :oid FOR UPDATE");
            $stmtLock->execute([':oid' => $orderId]);
            $order = $stmtLock->fetch();

            if (!$order) {
                throw new Exception("Order #{$orderId} not found");
            }

            if (in_array($order['order_status'], ['CANCELLED', 'REFUNDED'])) {
                throw new Exception("Order #{$order['order_number']} is {$order['order_status']} and cannot accept payments.");
            }

            // 3. Calculate Server-Authoritative Bill & Outstanding Balance
            $currentBill = BillingEngine::calculateOrderBill($orderId);
            $outstandingBalance = $currentBill['outstanding_balance'];

            if ($outstandingBalance <= 0.00) {
                throw new Exception("Order #{$order['order_number']} is already fully settled.");
            }

            // 4. Extract Payment Items (support split payment array or single payment)
            $paymentItems = [];
            if (!empty($paymentData['split_payments']) && is_array($paymentData['split_payments'])) {
                $paymentItems = $paymentData['split_payments'];
            } else {
                $paymentItems[] = [
                    'payment_method_id' => (int)($paymentData['payment_method_id'] ?? 1),
                    'amount' => (float)($paymentData['amount'] ?? 0.00),
                    'transaction_reference' => $paymentData['transaction_reference'] ?? ($paymentData['reference'] ?? null),
                    'notes' => $paymentData['notes'] ?? null
                ];
            }

            $totalPaymentAttempt = 0.00;
            foreach ($paymentItems as $pi) {
                $amt = (float)($pi['amount'] ?? 0);
                if ($amt <= 0.00) {
                    throw new Exception("Payment amount must be greater than zero.");
                }
                $totalPaymentAttempt += $amt;
            }

            // Overpayment Guard
            if ($totalPaymentAttempt > ($outstandingBalance + 0.01)) {
                throw new Exception("Total payment amount (৳" . number_format($totalPaymentAttempt, 2) . ") exceeds remaining balance (৳" . number_format($outstandingBalance, 2) . ").");
            }

            $processedPayments = [];

            // 5. Process Each Payment Item
            foreach ($paymentItems as $index => $pi) {
                $pmId = (int)($pi['payment_method_id'] ?? 1);
                $amt = (float)$pi['amount'];
                $ref = !empty($pi['transaction_reference']) ? trim($pi['transaction_reference']) : null;
                $notes = !empty($pi['notes']) ? trim($pi['notes']) : null;

                // Validate Payment Method
                $pmStmt = $db->prepare("SELECT name, code, requires_reference FROM payment_methods WHERE id = :pmid AND is_active = 1");
                $pmStmt->execute([':pmid' => $pmId]);
                $pmInfo = $pmStmt->fetch();

                if (!$pmInfo) {
                    throw new Exception("Invalid or inactive payment method ID {$pmId}");
                }

                if ($pmInfo['requires_reference'] && empty($ref)) {
                    throw new Exception("Payment method '{$pmInfo['name']}' requires a transaction reference number.");
                }

                // Generate Unique Payment Number
                $paymentNum = 'PM-' . date('Ymd') . '-' . sprintf('%06d', rand(100000, 999999));
                $itemKey = ($idempotencyKey && count($paymentItems) > 1) ? ($idempotencyKey . '-' . $index) : $idempotencyKey;

                // Insert Payment Record
                $payStmt = $db->prepare("
                    INSERT INTO payments 
                    (payment_number, branch_id, order_id, dining_session_id, payment_method_id, amount, currency, transaction_reference, received_by_user_id, status, idempotency_key, notes, created_at)
                    VALUES (:pnum, :bid, :oid, :sid, :pmid, :amt, 'BDT', :ref, :uid, 'COMPLETED', :ikey, :notes, NOW())
                ");
                $payStmt->execute([
                    ':pnum' => $paymentNum,
                    ':bid' => $order['branch_id'],
                    ':oid' => $orderId,
                    ':sid' => $order['dining_session_id'] ?? null,
                    ':pmid' => $pmId,
                    ':amt' => $amt,
                    ':ref' => $ref,
                    ':uid' => $userId,
                    ':ikey' => $itemKey,
                    ':notes' => $notes
                ]);
                $paymentId = (int)$db->lastInsertId();

                // Insert Payment Allocation
                $allocStmt = $db->prepare("INSERT INTO payment_allocations (payment_id, order_id, amount, created_at) VALUES (:pid, :oid, :amt, NOW())");
                $allocStmt->execute([':pid' => $paymentId, ':oid' => $orderId, ':amt' => $amt]);

                // Auto-generate Receipt
                $receiptInfo = ReceiptEngine::generateReceipt($paymentId, $orderId, $userId);

                $processedPayments[] = [
                    'payment_id' => $paymentId,
                    'payment_number' => $paymentNum,
                    'method' => $pmInfo['name'],
                    'amount' => $amt,
                    'receipt_number' => $receiptInfo['receipt_number'] ?? null
                ];

                AuditLogger::log($userId, 'PAYMENT_CREATED', 'payments', $paymentId, null, [
                    'order_id' => $orderId,
                    'amount' => $amt,
                    'method' => $pmInfo['name']
                ]);
            }

            // 6. Recalculate Final Bill & Synchronize Order Status
            $updatedBill = BillingEngine::calculateOrderBill($orderId);
            $newPaymentStatus = $updatedBill['payment_status'];

            $upOrdStmt = $db->prepare("UPDATE orders SET payment_status = :pst WHERE id = :oid");
            $upOrdStmt->execute([':pst' => $newPaymentStatus, ':oid' => $orderId]);

            // Pay-First Workflow: If order was in DRAFT or WAITING_PAYMENT and now PAID, confirm order
            if (in_array($order['order_status'], ['DRAFT', 'WAITING_PAYMENT']) && $newPaymentStatus === 'PAID') {
                $upOrdState = $db->prepare("UPDATE orders SET order_status = 'SUBMITTED' WHERE id = :oid");
                $upOrdState->execute([':oid' => $orderId]);
            }

            // Trigger Waiter Commission Processing
            try {
                CommissionEngine::processOrderCommission($orderId, $userId);
            } catch (Exception $e) {
                // Non-blocking fallback
            }

            $db->commit();

            $firstPaymentId = !empty($processedPayments[0]['payment_id']) ? $processedPayments[0]['payment_id'] : null;

            return [
                'success' => true,
                'payment_id' => $firstPaymentId,
                'order_id' => $orderId,
                'payment_status' => $updatedBill['payment_status'],
                'order_status' => $updatedBill['order_status'],
                'outstanding_balance' => $updatedBill['outstanding_balance'],
                'total_paid_now' => $totalPaymentAttempt,
                'payments' => $processedPayments,
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
     * Get active payment methods for a branch
     */
    public static function getPaymentMethods(int $branchId = 1): array {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM payment_methods WHERE is_active = 1 ORDER BY display_order ASC, name ASC");
        return $stmt->fetchAll();
    }

    /**
     * Get payment history for branch
     */
    public static function getPaymentsList(array $filters = []): array {
        $db = Database::getConnection();
        $branchId = (int)($filters['branch_id'] ?? 1);
        $params = [':bid' => $branchId];

        $sql = "
            SELECT p.*, pm.name AS payment_method_name, pm.code AS payment_method_code,
                   o.order_number, o.order_type, rt.table_number, u.name AS received_by_name
            FROM payments p
            JOIN payment_methods pm ON p.payment_method_id = pm.id
            JOIN orders o ON p.order_id = o.id
            LEFT JOIN restaurant_tables rt ON o.table_id = rt.id
            LEFT JOIN users u ON p.received_by_user_id = u.id
            WHERE p.branch_id = :bid
        ";

        if (!empty($filters['order_id'])) {
            $sql .= " AND p.order_id = :oid";
            $params[':oid'] = (int)$filters['order_id'];
        }

        if (!empty($filters['status'])) {
            $sql .= " AND p.status = :st";
            $params[':st'] = $filters['status'];
        }

        $sql .= " ORDER BY p.created_at DESC LIMIT 100";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
