<?php
/**
 * SMARTRESTA Order Detail API Endpoint
 * GET /api/v1/orders/detail.php?id=101 (Get complete order with snapshots & history)
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/OrderEngine.php';
require_once __DIR__ . '/../../../core/Response.php';

Auth::requireAuth();
Auth::requirePermission('orders.view');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    Response::json(false, 405, "Method Not Allowed");
}

$orderId = !empty($_GET['id']) ? (int)$_GET['id'] : 0;
if ($orderId <= 0) {
    Response::json(false, 422, "Valid order ID is required.");
}

try {
    $order = OrderEngine::getOrderDetails($orderId);
    if (!$order) {
        Response::json(false, 404, "Order not found.");
    }
    Response::json(true, 200, "Order details retrieved successfully", $order);
} catch (Exception $e) {
    Response::json(false, 500, "Failed to load order detail: " . $e->getMessage());
}
