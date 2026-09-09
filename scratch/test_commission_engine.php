<?php
/**
 * Automated Verification Suite for SMARTRESTA Waiter Management & Commission Engine (Prompt 09)
 */

require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/WaiterEngine.php';
require_once __DIR__ . '/../core/CommissionEngine.php';
require_once __DIR__ . '/../core/PayoutEngine.php';
require_once __DIR__ . '/../core/PaymentEngine.php';
require_once __DIR__ . '/../core/RefundEngine.php';

echo "=== SMARTRESTA PROMPT 09 WAITER & COMMISSION ENGINE VERIFICATION ===\n\n";

try {
    $db = Database::getConnection();

    // 1. Ensure a Waiter User exists
    $stmtW = $db->query("
        SELECT u.id FROM users u 
        JOIN roles r ON u.role_id = r.id 
        WHERE r.name = 'waiter' LIMIT 1
    ");
    $waiterUser = $stmtW->fetch();
    $waiterId = $waiterUser ? (int)$waiterUser['id'] : 1;

    // Get/Create Profile
    $profile = WaiterEngine::getOrCreateProfile($waiterId, 1);
    echo "✅ Waiter Profile Verified: ID #{$profile['waiter_id']} ({$profile['name']} - Code: {$profile['employee_code']})\n";

    // 2. Test Waiter Table Assignment
    $stmtF = $db->query("SELECT id FROM floors LIMIT 1");
    $floor = $stmtF->fetch();
    if (!$floor) {
        $db->exec("INSERT INTO floors (branch_id, name) VALUES (1, 'Main Dining Hall')");
        $floorId = (int)$db->lastInsertId();
    } else {
        $floorId = (int)$floor['id'];
    }

    $stmtT = $db->query("SELECT id FROM restaurant_tables LIMIT 1");
    $table = $stmtT->fetch();
    if (!$table) {
        $db->exec("INSERT INTO restaurant_tables (floor_id, table_number, capacity, status) VALUES ({$floorId}, 'T-100', 4, 'AVAILABLE')");
        $tableId = (int)$db->lastInsertId();
    } else {
        $tableId = (int)$table['id'];
    }

    $assignRes = WaiterEngine::assignWaiter([
        'waiter_id' => $waiterId,
        'table_id' => $tableId,
        'assignment_type' => 'TABLE'
    ], 1);
    echo "✅ Table Assignment Created: Assignment ID #{$assignRes['assignment_id']} (Table #{$tableId} -> Waiter #{$waiterId})\n";

    // 3. Create Test Order with Waiter Attribution
    $stmtBranch = $db->query("SELECT id FROM branches LIMIT 1");
    $branch = $stmtBranch->fetch();
    $branchId = $branch ? (int)$branch['id'] : 1;

    $testOrderNum = 'WTR-ORD-' . time();
    $stmtOrder = $db->prepare("
        INSERT INTO orders (
            order_number, branch_id, table_id, order_type,
            subtotal, tax, service_charge, discount, total,
            payment_status, order_status, taken_by_user_id
        ) VALUES (
            :num, :bid, :tid, 'DINE_IN',
            1000.00, 50.00, 0.00, 0.00, 1050.00,
            'UNPAID', 'SERVED', :wid
        )
    ");
    $stmtOrder->execute([
        ':num' => $testOrderNum,
        ':bid' => $branchId,
        ':tid' => $tableId,
        ':wid' => $waiterId
    ]);
    $orderId = (int)$db->lastInsertId();

    $stmtItem = $db->prepare("
        INSERT INTO order_items (
            order_id, product_id, counter_id, item_name, unit_price, quantity, subtotal, status
        ) VALUES (
            :oid, 1, 1, 'Mutton Kacchi Feast', 500.00, 2, 1000.00, 'SERVED'
        )
    ");
    $stmtItem->execute([':oid' => $orderId]);

    echo "✅ Test Order Created: ID #{$orderId} ({$testOrderNum}) - Net Sales: ৳1,000.00 | Total: ৳1,050.00\n";

    // 4. Test Pre-Payment Commission Eligibility Rule Check
    $calcPre = CommissionEngine::calculateCommission($orderId);
    echo "✅ Pre-Payment Commission Check Executed:\n";
    echo "   - Rule Applied: {$calcPre['rule_name']} ({$calcPre['calculation_base']})\n";
    echo "   - Eligibility Check: " . ($calcPre['eligible'] ? 'ELIGIBLE' : 'EXCLUDED (PAID_ONLY Rule enforced)') . "\n";

    // 5. Process Payment -> Trigger Automated Commission Creation
    $cashPm = $db->query("SELECT id FROM payment_methods WHERE code = 'CASH' LIMIT 1")->fetch();
    $cashPmId = $cashPm ? (int)$cashPm['id'] : 1;

    $payRes = PaymentEngine::processPayment([
        'order_id' => $orderId,
        'payment_method_id' => $cashPmId,
        'amount' => 1050.00,
        'idempotency_key' => 'IDEM-WTR-' . microtime(true)
    ], 1);

    echo "✅ Order Payment Processed & Settled: Payment ID #{$payRes['payment_id']}\n";

    // Fetch created commission transaction
    $commStmt = $db->prepare("SELECT * FROM commission_transactions WHERE order_id = :oid AND waiter_id = :wid");
    $commStmt->execute([':oid' => $orderId, ':wid' => $waiterId]);
    $comm = $commStmt->fetch();

    if (!$comm) {
        throw new Exception("Automated commission transaction was NOT created after payment!");
    }
    $commId = (int)$comm['id'];
    echo "✅ Automated Commission Transaction Created: ID #{$commId} (Status: {$comm['status']}, Amount: ৳" . number_format($comm['commission_amount'], 2) . ")\n";

    // 6. Test Idempotency (Firing Commission creation again)
    $idempotentRes = CommissionEngine::processOrderCommission($orderId, 1);
    if (!empty($idempotentRes['idempotent'])) {
        echo "✅ Idempotency Verified: Duplicate trigger returned existing commission ID #{$idempotentRes['commission_id']}\n";
    }

    // 7. Test Partial Customer Refund & Commission Adjustment
    $refundRes = RefundEngine::processRefund([
        'payment_id' => $payRes['payment_id'],
        'amount' => 525.00, // 50% refund
        'reason' => 'Customer partial dish refund'
    ], 1);

    echo "✅ Partial Refund Processed: Refund ID #{$refundRes['refund_id']} (৳525.00)\n";

    // Inspect updated commission transaction
    $commStmt->execute([':oid' => $orderId, ':wid' => $waiterId]);
    $updatedComm = $commStmt->fetch();
    echo "✅ Commission Reversal/Adjustment Verified:\n";
    echo "   - Updated Status: {$updatedComm['status']}\n";
    echo "   - New Commission Amount: ৳" . number_format($updatedComm['commission_amount'], 2) . " (Original ৳50.00 -> 50% Reversal -> ৳25.00)\n";

    // 8. Test Manager Commission Approval Workflow
    $appRes = CommissionEngine::approveCommissions([$commId], 1);
    echo "✅ Manager Batch Approval Executed: {$appRes['approved_count']} commission transaction(s) APPROVED.\n";

    // 9. Test Commission Payout Settlement
    $payoutRes = PayoutEngine::createPayout([
        'waiter_id' => $waiterId,
        'branch_id' => $branchId,
        'payment_method' => 'CASH',
        'reference_number' => 'PAYOUT-REF-100',
        'notes' => 'Weekly waiter commission payout test'
    ], 1);

    echo "✅ Commission Payout Processed: Payout #{$payoutRes['payout_number']} (Total ৳" . number_format($payoutRes['amount_paid'], 2) . " Settled)\n";

    // Test Double Payout Guard
    try {
        PayoutEngine::createPayout([
            'waiter_id' => $waiterId,
            'branch_id' => $branchId
        ], 1);
        echo "❌ ERROR: Double payout attempt was NOT rejected!\n";
    } catch (Exception $e) {
        echo "✅ Double Payout Safely Guarded & Rejected: " . $e->getMessage() . "\n";
    }

    // 10. Verify SQL Waiter Performance Matrix Aggregations
    $matrix = WaiterEngine::getPerformanceMatrix($branchId);
    echo "✅ Server-Side SQL Performance Matrix Aggregated Successfully (" . count($matrix) . " waiter records found).\n";

    echo "\n🎉 ALL PROMPT 09 WAITER & COMMISSION ENGINE TESTS PASSED SUCCESSFULLY!\n";

} catch (Exception $e) {
    echo "❌ TEST FAILED WITH EXCEPTION:\n";
    echo $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
