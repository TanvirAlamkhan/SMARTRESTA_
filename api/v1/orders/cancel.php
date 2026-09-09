<?php
/**
 * SMARTRESTA Order Cancellation API Endpoint
 * POST /api/v1/orders/cancel.php
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/OrderEngine.php';
require_once __DIR__ . '/../../../core/Response.php';

Auth::requireAuth();
Auth::requirePermission('orders.cancel');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::json(false, 405, "Method Not Allowed");
}

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
$orderId = !empty($input['order_id']) ? (int)$input['order_id'] : 0;
$reason = !empty($input['reason']) ? trim($input['reason']) : '';

if ($orderId <= 0 || empty($reason)) {
    Response::json(false, 422, "Valid order ID and cancellation reason are required.");
}

try {
    $userId = Auth::user()['id'] ?? 1;
    OrderEngine::cancelOrder($orderId, $reason, $userId);
    Response::json(true, 200, "Order #{$orderId} has been cancelled.");
} catch (Exception $e) {
    Response::json(false, 422, "Order cancellation failed: " . $e->getMessage());
}
