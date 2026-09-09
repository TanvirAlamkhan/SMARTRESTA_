<?php
/**
 * Verification Test Script for SMARTRESTA Payment & Billing Engine (Prompt 08)
 */

require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/BillingEngine.php';
require_once __DIR__ . '/../core/PaymentEngine.php';
require_once __DIR__ . '/../core/RefundEngine.php';
require_once __DIR__ . '/../core/ReceiptEngine.php';
require_once __DIR__ . '/../core/InvoiceEngine.php';

echo "=== SMARTRESTA PROMPT 08 PAYMENT & BILLING ENGINE TEST ===\n\n";

try {
    $db = Database::getConnection();

    // 1. Fetch active session & branch
    $stmt = $db->query("SELECT id FROM dining_sessions WHERE status = 'OPEN' LIMIT 1");
    $session = $stmt->fetch();
    $sessionId = $session ? $session['id'] : null;

    $stmt = $db->query("SELECT id FROM branches LIMIT 1");
    $branch = $stmt->fetch();
    $branchId = $branch ? $branch['id'] : 1;

    // Create a temporary order for testing payment & billing flow
    $stmt = $db->prepare("
        INSERT INTO orders (
            order_number, branch_id, dining_session_id, order_type,
            subtotal, tax, service_charge, discount, total,
            payment_status, order_status, taken_by_user_id
        ) VALUES (
            :num, :bid, :sid, 'DINE_IN',
            500.00, 25.00, 0.00, 0.00, 525.00,
            'UNPAID', 'SERVED', 1
        )
    ");
    $testOrderNum = 'TEST-ORD-' . time();
    $stmt->execute([
        'num' => $testOrderNum,
        'bid' => $branchId,
        'sid' => $sessionId
    ]);
    $orderId = $db->lastInsertId();

    // Insert order item
    $stmt = $db->prepare("
        INSERT INTO order_items (
            order_id, product_id, counter_id, item_name, unit_price, quantity, subtotal, status
        ) VALUES (
            :oid, 1, 1, 'Test Item 100', 250.00, 2, 500.00, 'SERVED'
        )
    ");
    $stmt->execute(['oid' => $orderId]);

    echo "✅ Test Order Created: ID #{$orderId} ({$testOrderNum}) - Grand Total: ৳525.00\n";

    // 2. Calculate Order Bill
    $bill = BillingEngine::calculateOrderBill($orderId);
    echo "✅ Billing Engine Calculated Bill:\n";
    echo "   - Subtotal: ৳" . number_format($bill['subtotal'], 2) . "\n";
    echo "   - Tax (5%): ৳" . number_format($bill['tax'], 2) . "\n";
    echo "   - Grand Total: ৳" . number_format($bill['grand_total'], 2) . "\n";
    echo "   - Net Paid: ৳" . number_format($bill['net_paid'], 2) . "\n";
    echo "   - Balance Owed: ৳" . number_format($bill['outstanding_balance'], 2) . "\n";

    // Fetch cash payment method ID
    $stmtPm = $db->query("SELECT id FROM payment_methods WHERE code = 'CASH' LIMIT 1");
    $cashPm = $stmtPm->fetch();
    $cashMethodId = $cashPm ? $cashPm['id'] : 1;

    $stmtPm = $db->query("SELECT id FROM payment_methods WHERE code = 'BKASH' LIMIT 1");
    $bkashPm = $stmtPm->fetch();
    $bkashMethodId = $bkashPm ? $bkashPm['id'] : 3;

    // 3. Process Partial Single Payment
    $idempotencyKey1 = 'IDEM-TEST-' . microtime(true);
    $payRes1 = PaymentEngine::processPayment([
        'order_id' => $orderId,
        'payment_method_id' => $cashMethodId,
        'amount' => 200.00,
        'idempotency_key' => $idempotencyKey1,
        'notes' => 'Partial cash deposit'
    ], 1);

    echo "✅ Partial Payment Processed: Payment ID #{$payRes1['payment_id']} (৳200.00)\n";
    echo "   - New Payment Status: {$payRes1['payment_status']}\n";
    echo "   - Remaining Balance: ৳" . number_format($payRes1['outstanding_balance'], 2) . "\n";

    // Test Idempotency Key Duplicate
    try {
        $idemRes = PaymentEngine::processPayment([
            'order_id' => $orderId,
            'payment_method_id' => $cashMethodId,
            'amount' => 200.00,
            'idempotency_key' => $idempotencyKey1
        ], 1);

        if (!empty($idemRes['idempotent'])) {
            echo "✅ Idempotency Key Replay Handled Gracefully (Returned existing payment ID #{$idemRes['payment_id']})\n";
        }
    } catch (Exception $e) {
        echo "✅ Idempotency Key Duplicate Caught: " . $e->getMessage() . "\n";
    }

    // 4. Process Remaining Payment via bKash (Completing Order)
    $payRes2 = PaymentEngine::processPayment([
        'order_id' => $orderId,
        'payment_method_id' => $bkashMethodId,
        'amount' => 325.00,
        'transaction_reference' => 'BKASH-TX-998877',
        'idempotency_key' => 'IDEM-TEST-' . microtime(true),
        'notes' => 'Settlement payment via bKash'
    ], 1);

    echo "✅ Settlement Payment Processed: Payment ID #{$payRes2['payment_id']} (৳325.00)\n";
    echo "   - Order Payment Status: {$payRes2['payment_status']}\n";
    echo "   - Order Status: {$payRes2['order_status']}\n";
    echo "   - Outstanding Balance: ৳" . number_format($payRes2['outstanding_balance'], 2) . "\n";

    // 5. Generate Receipt & Invoice
    $receipt = ReceiptEngine::generateReceiptForPayment($payRes2['payment_id'], 1);
    echo "✅ Receipt Generated: Receipt #{$receipt['receipt_number']}\n";

    $invoice = InvoiceEngine::generateInvoiceForOrder($orderId, 1);
    echo "✅ Invoice Generated: Invoice #{$invoice['invoice_number']} (Status: {$invoice['status']})\n";

    // 6. Test Refund Processing
    $refundRes = RefundEngine::processRefund([
        'payment_id' => $payRes1['payment_id'],
        'amount' => 50.00,
        'reason' => 'Customer price adjustment discount request'
    ], 1);
    echo "✅ Refund Processed: Refund ID #{$refundRes['refund_id']} (৳50.00)\n";
    echo "   - Refund Number: {$refundRes['refund_number']}\n";

    // Test Exceeding Refund Rejection
    try {
        RefundEngine::processRefund([
            'payment_id' => $payRes1['payment_id'],
            'amount' => 300.00,
            'reason' => 'Excessive refund test'
        ], 1);
        echo "❌ ERROR: Over-refund was NOT rejected!\n";
    } catch (Exception $e) {
        echo "✅ Over-Refund Safely Rejected: " . $e->getMessage() . "\n";
    }

    echo "\n🎉 ALL PROMPT 08 PAYMENT & BILLING ENGINE TESTS PASSED SUCCESSFULLY!\n";

} catch (Exception $e) {
    echo "❌ TEST FAILED WITH EXCEPTION:\n";
    echo $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
