<?php
/**
 * SMARTRESTA Core Receipt Engine
 * Prompt 08: Payment & Billing Engine, Settlement, Receipts & Refunds
 *
 * Generates server-authoritative payment receipts with unique receipt numbers
 * and historical JSON snapshots.
 */

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/BillingEngine.php';

class ReceiptEngine {

    /**
     * Generate or retrieve persisted receipt for a completed payment
     */
    public static function generateReceipt(int $paymentId, ?int $orderId = null, int $userId = 1): array {
        $db = Database::getConnection();

        // 1. Check existing receipt for this payment
        $stmtExist = $db->prepare("SELECT * FROM receipts WHERE payment_id = :pid");
        $stmtExist->execute([':pid' => $paymentId]);
        $existing = $stmtExist->fetch();
        if ($existing) {
            $existing['receipt_data'] = json_decode($existing['receipt_data'], true);
            return $existing;
        }

        // 2. Fetch Payment Details
        $payStmt = $db->prepare("
            SELECT p.*, pm.name AS payment_method_name 
            FROM payments p 
            JOIN payment_methods pm ON p.payment_method_id = pm.id 
            WHERE p.id = :pid
        ");
        $payStmt->execute([':pid' => $paymentId]);
        $payment = $payStmt->fetch();

        if (!$payment) {
            throw new Exception("Payment #{$paymentId} not found");
        }

        if (!$orderId) {
            $orderId = (int)$payment['order_id'];
        }

        // 3. Fetch Bill Snapshot
        $bill = BillingEngine::calculateOrderBill($orderId);

        // 4. Generate Unique Receipt Number
        $receiptNumber = 'RCP-' . date('Y') . '-' . sprintf('%06d', rand(100000, 999999));

        $snapshotData = [
            'receipt_number' => $receiptNumber,
            'payment_number' => $payment['payment_number'],
            'order_number' => $bill['order_number'],
            'branch_name' => $bill['branch_name'],
            'table_number' => $bill['table_number'],
            'waiter_name' => $bill['waiter_name'],
            'customer_name' => $bill['customer_name'],
            'items' => array_map(fn($i) => [
                'name' => $i['item_name'],
                'quantity' => $i['quantity'],
                'unit_price' => $i['unit_price'],
                'subtotal' => $i['subtotal']
            ], $bill['items']),
            'financials' => [
                'subtotal' => $bill['subtotal'],
                'discount' => $bill['discount'],
                'tax' => $bill['tax'],
                'service_charge' => $bill['service_charge'],
                'grand_total' => $bill['grand_total'],
                'amount_paid_this_txn' => (float)$payment['amount'],
                'total_net_paid' => $bill['net_paid'],
                'outstanding_balance' => $bill['outstanding_balance'],
                'payment_method' => $payment['payment_method_name'],
                'transaction_reference' => $payment['transaction_reference']
            ],
            'timestamp' => date('Y-m-d H:i:s')
        ];

        $insStmt = $db->prepare("
            INSERT INTO receipts (receipt_number, branch_id, order_id, payment_id, amount_paid, receipt_data, created_by, created_at)
            VALUES (:rnum, :bid, :oid, :pid, :amt, :snap, :uid, NOW())
        ");
        $insStmt->execute([
            ':rnum' => $receiptNumber,
            ':bid' => $bill['branch_id'],
            ':oid' => $orderId,
            ':pid' => $paymentId,
            ':amt' => (float)$payment['amount'],
            ':snap' => json_encode($snapshotData),
            ':uid' => $userId
        ]);
        $receiptId = (int)$db->lastInsertId();

        return [
            'id' => $receiptId,
            'receipt_number' => $receiptNumber,
            'payment_id' => $paymentId,
            'order_id' => $orderId,
            'amount_paid' => (float)$payment['amount'],
            'receipt_data' => $snapshotData,
            'created_at' => date('Y-m-d H:i:s')
        ];
    }

    public static function generateReceiptForPayment(int $paymentId, int $userId = 1): array {
        return self::generateReceipt($paymentId, null, $userId);
    }

    /**
     * Get persisted receipt by ID
     */
    public static function getReceipt(int $receiptId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM receipts WHERE id = :rid");
        $stmt->execute([':rid' => $receiptId]);
        $receipt = $stmt->fetch();

        if (!$receipt) {
            throw new Exception("Receipt #{$receiptId} not found");
        }

        $receipt['receipt_data'] = json_decode($receipt['receipt_data'], true);
        return $receipt;
    }
}
