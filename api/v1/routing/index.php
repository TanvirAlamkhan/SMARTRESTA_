<?php
/**
 * SMARTRESTA API Endpoint: Routing Summary / Routes List
 * GET /api/v1/routing/index.php?order_id={id}
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/RoutingEngine.php';
require_once __DIR__ . '/../../../core/Response.php';

Auth::requireAuth();

try {
    $orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
    if (!$orderId) {
        Response::json(false, 400, "Order ID parameter is required.");
        exit;
    }

    $summary = RoutingEngine::getOrderRouteSummary($orderId);
    Response::json(true, 200, "Routing summary retrieved successfully", $summary);
} catch (Exception $e) {
    Response::json(false, 500, "Failed to load routing summary: " . $e->getMessage());
}
