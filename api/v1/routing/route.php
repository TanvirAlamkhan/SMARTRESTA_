<?php
/**
 * SMARTRESTA API Endpoint: Dispatch & Route Order to Stations
 * POST /api/v1/routing/route.php
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/RoutingEngine.php';
require_once __DIR__ . '/../../../core/Response.php';

Auth::requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::json(false, 405, "Method not allowed");
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$orderId = (int)($data['order_id'] ?? 0);
$mode = $data['mode'] ?? 'AUTOMATIC';
$userId = $_SESSION['user_id'] ?? 1;

if (!$orderId) {
    Response::json(false, 400, "Order ID is required");
    exit;
}

try {
    $result = RoutingEngine::routeOrder($orderId, $userId, $mode);
    Response::json(true, 200, "Order successfully routed to station tickets.", $result);
} catch (Exception $e) {
    Response::json(false, 400, "Routing failed: " . $e->getMessage());
}
