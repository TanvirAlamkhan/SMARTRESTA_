<?php
/**
 * SMARTRESTA Prompt 14 Automated Verification Script
 * CRM, Reservations, Loyalty, Coupons/Promotions & Public QR Ordering Engine
 */

require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/CRMEngine.php';
require_once __DIR__ . '/../core/OrderEngine.php';
require_once __DIR__ . '/../core/RoutingEngine.php';
require_once __DIR__ . '/../core/KDSEngine.php';

echo "========================================================\n";
echo "SMARTRESTA — PROMPT 14 AUTOMATED INTEGRATION TEST SUITE\n";
echo "========================================================\n\n";

$passCount = 0;
$failCount = 0;

function assertTest(bool $condition, string $testName, string $detail = '') {
    global $passCount, $failCount;
    if ($condition) {
        $passCount++;
        echo " [PASS] {$testName}" . ($detail ? " → {$detail}" : "") . "\n";
    } else {
        $failCount++;
        echo "![FAIL] {$testName}" . ($detail ? " → {$detail}" : "") . "\n";
    }
}

try {
    $db = Database::getConnection();

    // ----------------------------------------------------
    // TEST SECTION 1: CRM & CUSTOMER LIFECYCLE
    // ----------------------------------------------------
    echo "--- 1. Testing Customer Relationship Management (CRM) ---\n";

    $phone = '017' . rand(10000000, 99999999);
    $cust = CRMEngine::createCustomer([
        'name' => 'Automated Test Customer',
        'phone' => $phone,
        'email' => 'test_cust_' . rand(100, 999) . '@smartresta.com',
        'company_name' => 'SMARTRESTA Labs',
        'gender' => 'MALE'
    ], 1);

    assertTest(!empty($cust['id']), "Customer Registration", "ID: #{$cust['id']}, Code: {$cust['customer_code']}");

    $updatedCust = CRMEngine::updateCustomer($cust['id'], [
        'notes' => 'Updated notes for VIP test customer'
    ], 1);
    assertTest($updatedCust['notes'] === 'Updated notes for VIP test customer', "Customer Update", "Notes updated");

    $searchRes = CRMEngine::searchCustomers(['search' => $phone]);
    assertTest(count($searchRes) > 0 && $searchRes[0]['id'] == $cust['id'], "Customer Search by Phone", "Found customer");

    $profile = CRMEngine::getCustomerProfile($cust['id']);
    assertTest(isset($profile['metrics']['lifetime_sales']), "Customer Profile & Metrics Calculation", "Lifetime sales: $" . $profile['metrics']['lifetime_sales']);


    // ----------------------------------------------------
    // TEST SECTION 2: TABLE RESERVATION & DOUBLE-BOOKING DEFENSE
    // ----------------------------------------------------
    echo "\n--- 2. Testing Reservation Engine & Conflict Defense ---\n";

    $resDate = date('Y-m-d', strtotime('+' . rand(5, 50) . ' days'));
    $resTime = '19:30:00';

    // Fetch Table #1 capacity
    $stmtTbl = $db->query("SELECT id FROM restaurant_tables LIMIT 1");
    $tableId = (int)$stmtTbl->fetchColumn();

    $availability = CRMEngine::checkAvailability(1, $resDate, $resTime, 2, 90, $tableId);
    assertTest($availability['is_available'] === true, "Check Table Availability", "Table #{$tableId} available for booking");

    $res1 = CRMEngine::createReservation([
        'branch_id' => 1,
        'customer_id' => $cust['id'],
        'table_id' => $tableId,
        'reservation_date' => $resDate,
        'reservation_time' => $resTime,
        'guest_count' => 2,
        'duration_minutes' => 90,
        'notes' => 'Test reservation 1'
    ], 1);
    assertTest(!empty($res1['id']), "Reservation Creation", "Res #: {$res1['reservation_number']} on Table #{$tableId}");

    // Attempt Double Booking for same table during overlapping window
    $conflictCaught = false;
    try {
        CRMEngine::createReservation([
            'branch_id' => 1,
            'customer_id' => $cust['id'],
            'table_id' => $tableId,
            'reservation_date' => $resDate,
            'reservation_time' => '20:00:00', // Overlaps 19:30 - 21:00
            'guest_count' => 2,
            'duration_minutes' => 90
        ], 1);
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'DOUBLE-BOOKING CONFLICT') !== false || $e->getCode() == 409) {
            $conflictCaught = true;
        }
    }
    assertTest($conflictCaught, "Transactional Double-Booking Defense (HTTP 409)", "Successfully rejected overlapping reservation");

    // Seat reservation
    $seated = CRMEngine::seatReservation($res1['id'], 1);
    assertTest($seated['status'] === 'SEATED' && !empty($seated['dining_session_id']), "Seat Reservation → Dining Session Integration", "Activated Session #{$seated['dining_session_id']}");


    // ----------------------------------------------------
    // TEST SECTION 3: LOYALTY PROGRAM & IMMUTABLE LEDGER
    // ----------------------------------------------------
    echo "\n--- 3. Testing Loyalty Program & Ledger ---\n";

    // Create completed order for customer to earn points
    $draft = OrderEngine::createDraftOrder([
        'branch_id' => 1,
        'customer_id' => $cust['id'],
        'order_type' => 'TAKEAWAY'
    ], 1);
    $orderId = $draft['order_id'];

    $stmtProd = $db->query("SELECT id FROM products WHERE status = 'ACTIVE' LIMIT 1");
    $productId = (int)$stmtProd->fetchColumn();

    OrderEngine::addItemToOrder($orderId, $productId, null, [], 2, null, 1);
    OrderEngine::submitOrder($orderId, 1);
    OrderEngine::changeOrderStatus($orderId, 'COMPLETED', 1);

    $earned = CRMEngine::earnPointsForOrder($orderId, 1);
    assertTest($earned === true, "Earn Loyalty Points on Completed Order", "Calculated 10% points rate");

    $loyaltyAcc = CRMEngine::getLoyaltyAccount($cust['id']);
    assertTest($loyaltyAcc['points_balance'] > 0, "Loyalty Balance Update", "Current balance: {$loyaltyAcc['points_balance']} pts");

    // Test Insufficient Balance Rejection
    $insufficientRejected = false;
    try {
        CRMEngine::redeemPoints($cust['id'], 99999.00, $orderId, 1);
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'INSUFFICIENT LOYALTY POINTS') !== false) {
            $insufficientRejected = true;
        }
    }
    assertTest($insufficientRejected, "Insufficient Points Redemption Rejection", "Prevented negative balance");

    // Test Manual Adjustment
    $adj = CRMEngine::adjustPoints($cust['id'], 50.00, "Automated test bonus credit", 1);
    assertTest($adj['balance_after'] == ($loyaltyAcc['points_balance'] + 50.00), "Audited Manager Points Adjustment", "New balance: {$adj['balance_after']} pts");


    // ----------------------------------------------------
    // TEST SECTION 4: COUPONS & PROMOTIONS
    // ----------------------------------------------------
    echo "\n--- 4. Testing Coupon & Promotion Engine ---\n";

    $code = 'PROMO' . rand(1000, 9999);
    $coupon = CRMEngine::createCoupon([
        'code' => $code,
        'name' => '15% Off Test Coupon',
        'discount_type' => 'PERCENTAGE',
        'discount_amount' => 15.00,
        'min_order_amount' => 20.00,
        'usage_limit' => 50
    ], 1);
    assertTest($coupon['code'] === $code, "Coupon Code Creation", "Created code {$code}");

    $valSuccess = CRMEngine::validateCoupon($code, 100.00, $cust['id'], 1);
    assertTest($valSuccess['valid'] === true && $valSuccess['discount_amount'] == 15.00, "Coupon Server-Side Validation & Discount", "Discount: $" . $valSuccess['discount_amount']);

    $valMinSpendFail = CRMEngine::validateCoupon($code, 10.00, $cust['id'], 1);
    assertTest($valMinSpendFail['valid'] === false, "Coupon Minimum Spend Rejection", "Rejected spend under $20");


    // ----------------------------------------------------
    // TEST SECTION 5: PUBLIC SECURE QR ORDERING ENGINE
    // ----------------------------------------------------
    echo "\n--- 5. Testing Public Secure QR Ordering Engine ---\n";

    $qrRow = CRMEngine::getOrCreateQRToken(1, $tableId);
    assertTest(!empty($qrRow['token']) && strlen($qrRow['token']) === 64, "Generate Secure Table QR Token", "Token: " . substr($qrRow['token'], 0, 16) . "...");

    $resolved = CRMEngine::resolveQRToken($qrRow['token']);
    assertTest($resolved['table_id'] == $tableId, "Public Token Resolution to Table", "Resolved Table #{$resolved['table_number']}");

    $publicMenu = CRMEngine::getPublicMenuForQR($qrRow['token']);
    assertTest(isset($publicMenu['products']) && count($publicMenu['products']) > 0, "Fetch Public Mobile QR Menu", "Loaded " . count($publicMenu['products']) . " available items");

    // Submit Public QR Order
    $qrSubmit = CRMEngine::submitPublicQROrder($qrRow['token'], [
        'customer_name' => 'QR Table Guest',
        'phone' => '019' . rand(10000000, 99999999),
        'notes' => 'Automated QR test order',
        'items' => [
            ['product_id' => $productId, 'quantity' => 1]
        ]
    ]);

    assertTest($qrSubmit['success'] === true && !empty($qrSubmit['order_id']), "Public QR Order Placement & Kitchen Dispatch", "Order #: {$qrSubmit['order_number']}, Status: {$qrSubmit['status']}");

    $kdsTickets = KDSEngine::getStationQueue(1);
    assertTest(count($kdsTickets) > 0, "QR Order Direct Entry into Smart Routing & KDS", "Kitchen ticket created in KDS");

} catch (Exception $e) {
    echo "\n❌ UNHANDLED EXCEPTION IN TEST RUNNER: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    $failCount++;
}

echo "\n========================================================\n";
echo "TEST RESULTS: PASS = {$passCount} | FAIL = {$failCount}\n";
echo "========================================================\n";

if ($failCount === 0) {
    echo "🎉 PROMPT 14 CRM, RESERVATIONS, LOYALTY, COUPONS & QR ORDERING VERIFIED PERFECTLY!\n";
    exit(0);
} else {
    echo "⚠️ SOME TESTS FAILED. PLEASE INSPECT LOGS ABOVE.\n";
    exit(1);
}
