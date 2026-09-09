<?php
/**
 * SMARTRESTA API Endpoint: Manual Item Rerouting Override
 * POST /api/v1/routing/reroute.php
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
$orderItemId = (int)($data['order_item_id'] ?? 0);
$newStationId = (int)($data['new_station_id'] ?? 0);
$reason = $data['reason'] ?? '';
$userId = $_SESSION['user_id'] ?? 1;

if (!$orderItemId || !$newStationId) {
    Response::json(false, 400, "order_item_id and new_station_id are required.");
    exit;
}

try {
    $result = RoutingEngine::rerouteItem($orderItemId, $newStationId, $userId, $reason);
    Response::json(true, 200, "Item rerouted successfully.", $result);
} catch (Exception $e) {
    Response::json(false, 400, "Rerouting failed: " . $e->getMessage());
}
