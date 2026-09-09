<?php
/**
 * SMARTRESTA Order Status Transition API Endpoint
 * POST /api/v1/orders/status.php
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/OrderEngine.php';
require_once __DIR__ . '/../../../core/Response.php';

Auth::requireAuth();
Auth::requirePermission('orders.change_status');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::json(false, 405, "Method Not Allowed");
}

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
$orderId = !empty($input['order_id']) ? (int)$input['order_id'] : 0;
$newStatus = !empty($input['status']) ? strtoupper(trim($input['status'])) : '';
$notes = !empty($input['notes']) ? trim($input['notes']) : null;

if ($orderId <= 0 || empty($newStatus)) {
    Response::json(false, 422, "Valid order ID and target status are required.");
}

try {
    $userId = Auth::user()['id'] ?? 1;
    OrderEngine::changeOrderStatus($orderId, $newStatus, $userId, $notes);
    Response::json(true, 200, "Order status advanced to {$newStatus}");
} catch (Exception $e) {
    Response::json(false, 422, "Failed to change order status: " . $e->getMessage());
}
