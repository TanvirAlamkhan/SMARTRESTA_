<?php
/**
 * SMARTRESTA API Endpoint: Financial Bill Calculation
 * GET /api/v1/billing/index.php?order_id={id}
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/BillingEngine.php';
require_once __DIR__ . '/../../../core/Response.php';

Auth::requireAuth();

try {
    $orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
    $sessionId = isset($_GET['session_id']) ? (int)$_GET['session_id'] : 0;

    if ($orderId) {
        $bill = BillingEngine::calculateOrderBill($orderId);
        Response::json(true, 200, "Order bill calculated successfully", $bill);
    } else if ($sessionId) {
        $bill = BillingEngine::calculateSessionBill($sessionId);
        Response::json(true, 200, "Session bill calculated successfully", $bill);
    } else {
        Response::json(false, 400, "order_id or session_id parameter is required.");
    }
} catch (Exception $e) {
    Response::json(false, 500, "Billing calculation failed: " . $e->getMessage());
}
