<?php
/**
 * SMARTRESTA Order Submission API Endpoint
 * POST /api/v1/orders/submit.php
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/OrderEngine.php';
require_once __DIR__ . '/../../../core/Response.php';

Auth::requireAuth();
Auth::requirePermission('orders.submit');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::json(false, 405, "Method Not Allowed");
}

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
$orderId = !empty($input['order_id']) ? (int)$input['order_id'] : 0;

if ($orderId <= 0) {
    Response::json(false, 422, "Valid order ID is required.");
}

try {
    $userId = Auth::user()['id'] ?? 1;
    $result = OrderEngine::submitOrder($orderId, $userId);
    Response::json(true, 200, "Order #{$result['order_number']} successfully submitted for kitchen & counter routing", $result);
} catch (Exception $e) {
    Response::json(false, 422, "Order submission failed: " . $e->getMessage());
}
