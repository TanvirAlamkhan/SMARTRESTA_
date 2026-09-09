<?php
/**
 * SMARTRESTA API Endpoint: Get Active Orders
 * GET /api/v1/orders/get_active.php
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/OrderEngine.php';
require_once __DIR__ . '/../../../core/Response.php';

Auth::requireAuth();

try {
    $orders = OrderEngine::getOrders();
    Response::json(true, 200, "Active orders retrieved successfully", $orders);
} catch (Exception $e) {
    Response::json(false, 500, "Failed to load active orders: " . $e->getMessage());
}
