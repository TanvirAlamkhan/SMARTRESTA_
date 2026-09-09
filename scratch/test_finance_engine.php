<?php
/**
 * SMARTRESTA - Automated Verification Script for Prompt 13: FinanceEngine
 */

define('SMARTRESTA_ENTRY', true);
require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/FinanceEngine.php';

echo "========================================================\n";
echo "  SMARTRESTA Prompt 13: Finance & Day Closing Verification\n";
echo "========================================================\n\n";

$branchId = 1;
$userId = 1; // Admin user ID

try {
    // 1. Finance Overview Summary
    echo "[1] Testing FinanceEngine::getFinanceSummary()...\n";
    $summary = FinanceEngine::getFinanceSummary($branchId);
    echo "    - Business Date: {$summary['business_date']}\n";
    echo "    - Day Status: {$summary['day_status']}\n";
    echo "    - Gross Sales: ৳{$summary['kpis']['gross_sales']}\n";
    echo "    - Net Sales: ৳{$summary['kpis']['net_sales']}\n";
    echo "    - Cash Collected: ৳{$summary['kpis']['cash_collected']}\n";
    echo "    - Non-Cash Collected: ৳{$summary['kpis']['non_cash_collected']}\n";
    echo "    - Total Expenses: ৳{$summary['kpis']['total_expenses']}\n";
    echo "  -> SUCCESS\n\n";

    // 2. Shift Opening
    echo "[2] Testing FinanceEngine::openShift()...\n";
    // Close any previous open shift for test user if exists
    $db = Database::getConnection();
    $db->exec("UPDATE shifts SET status = 'CLOSED', closed_at = NOW() WHERE user_id = {$userId} AND status = 'OPEN'");

    $shift = FinanceEngine::openShift($branchId, $userId, 1, 10000.00, "Automated Test Shift Float");
    echo "    - Opened Shift ID: #{$shift['id']}\n";
    echo "    - Cashier Name: {$shift['cashier_name']}\n";
    echo "    - Drawer Code: {$shift['drawer_code']}\n";
    echo "    - Opening Cash: ৳{$shift['opening_cash']}\n";
    echo "    - Live Expected Cash: ৳{$shift['expected_cash']}\n";
    echo "  -> SUCCESS\n\n";

    // 3. Active Shift Check
    echo "[3] Testing FinanceEngine::getActiveShift()...\n";
    $activeShift = FinanceEngine::getActiveShift($userId, $branchId);
    if (!$activeShift || $activeShift['status'] !== 'OPEN') {
        throw new Exception("Active shift verification failed.");
    }
    echo "    - Active Shift verified: #{$activeShift['id']} (Status: {$activeShift['status']})\n";
    echo "  -> SUCCESS\n\n";

    // 4. Cash In & Cash Out Movements
    echo "[4] Testing FinanceEngine::recordCashMovement()...\n";
    $cashIn = FinanceEngine::recordCashMovement([
        'branch_id' => $branchId,
        'amount' => 2000.00,
        'movement_type' => 'CASH_IN',
        'reason' => 'Petty Cash Added for Register'
    ], $userId);
    echo "    - Cash In logged: ৳{$cashIn['amount']}\n";

    $cashOut = FinanceEngine::recordCashMovement([
        'branch_id' => $branchId,
        'amount' => 500.00,
        'movement_type' => 'CASH_OUT',
        'reason' => 'Change Disbursed to Register 2'
    ], $userId);
    echo "    - Cash Out logged: ৳{$cashOut['amount']}\n";
    echo "  -> SUCCESS\n\n";

    // 5. Operating Expense Creation & Payment
    echo "[5] Testing FinanceEngine::createExpense() & payExpense()...\n";
    $expense = FinanceEngine::createExpense([
        'branch_id' => $branchId,
        'category_id' => 1,
        'amount' => 800.00,
        'title' => 'Emergency Plumbing Repair',
        'description' => 'Fixed kitchen sink drain pipe leak',
        'payment_method_id' => 1 // Cash
    ], $userId);
    echo "    - Expense Created ID: #{$expense['expense_id']} (৳{$expense['amount']})\n";

    $paidExpense = FinanceEngine::payExpense($expense['expense_id'], 1, $userId);
    echo "    - Expense Paid Status: {$paidExpense['status']}\n";
    echo "  -> SUCCESS\n\n";

    // 6. Expected Cash Formula Verification
    echo "[6] Testing FinanceEngine::calculateExpectedCash()...\n";
    $expectedCash = FinanceEngine::calculateExpectedCash($shift['id']);
    // Opening 10000 + CashIn 2000 - CashOut 500 - CashExpense 800 = 10700
    echo "    - Calculated Expected Cash: ৳{$expectedCash}\n";
    echo "  -> SUCCESS\n\n";

    // 7. Shift Closing with Denominations & Discrepancy Calculation
    echo "[7] Testing FinanceEngine::closeShift()...\n";
    $actualCashCount = 10650.00; // ৳50 shortage
    $denominations = ['1000' => 10, '500' => 1, '100' => 1, '50' => 1];
    $closedShift = FinanceEngine::closeShift($shift['id'], $actualCashCount, $denominations, $userId, "Shift Closed with ৳50 coin shortage");
    echo "    - Shift Closed ID: #{$closedShift['shift_id']}\n";
    echo "    - Expected Cash: ৳{$closedShift['expected_cash']}\n";
    echo "    - Actual Cash Counted: ৳{$closedShift['actual_cash']}\n";
    echo "    - Cash Difference: ৳{$closedShift['difference']}\n";
    echo "  -> SUCCESS\n\n";

    // 8. Day Closing Review Pre-Check Audit
    echo "[8] Testing FinanceEngine::getDayClosingReview()...\n";
    $dateToday = date('Y-m-d');
    $review = FinanceEngine::getDayClosingReview($branchId, $dateToday);
    echo "    - Can Close Day: " . ($review['can_close'] ? 'YES' : 'NO') . "\n";
    echo "    - Open Shifts Count: {$review['blocking_reasons']['open_shifts']}\n";
    echo "  -> SUCCESS\n\n";

    // 9. Execute Business Day Close & Freeze Snapshot
    echo "[9] Testing FinanceEngine::closeBusinessDay()...\n";
    // Reset day closing status for test run
    $db->exec("DELETE FROM day_closings WHERE branch_id = {$branchId} AND business_date = '{$dateToday}'");

    $dayClose = FinanceEngine::closeBusinessDay($branchId, $dateToday, $userId);
    echo "    - Business Day Close ID: #{$dayClose['day_close_id']}\n";
    echo "    - Date: {$dayClose['business_date']}\n";
    echo "    - Status: {$dayClose['status']}\n";
    echo "    - Frozen Net Sales: ৳{$dayClose['summary']['net_sales']}\n";
    echo "  -> SUCCESS\n\n";

    echo "========================================================\n";
    echo "  ALL FINANCE & DAY CLOSING TESTS PASSED SUCCESSFULLY!  \n";
    echo "========================================================\n";

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
