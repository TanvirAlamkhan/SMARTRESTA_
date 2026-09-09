<?php
/**
 * SMARTRESTA Orders API Endpoint
 * GET  /api/v1/orders/index.php (Filter, search, list orders)
 * POST /api/v1/orders/index.php (Create draft or dine-in order)
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/OrderEngine.php';
require_once __DIR__ . '/../../../core/Response.php';

Auth::requireAuth();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    Auth::requirePermissionOrEmpty('orders.view');

    $filters = [];
    if (!empty($_GET['branch_id'])) $filters['branch_id'] = (int)$_GET['branch_id'];
    if (!empty($_GET['order_status'])) $filters['order_status'] = trim($_GET['order_status']);
    if (!empty($_GET['dining_session_id'])) $filters['dining_session_id'] = (int)$_GET['dining_session_id'];
    if (!empty($_GET['table_id'])) $filters['table_id'] = (int)$_GET['table_id'];
    if (!empty($_GET['search'])) $filters['search'] = trim($_GET['search']);

    try {
        $orders = OrderEngine::getOrders($filters);
        Response::json(true, 200, "Orders retrieved successfully", $orders);
    } catch (Exception $e) {
        Response::json(false, 500, "Failed to retrieve orders: " . $e->getMessage());
    }

} elseif ($method === 'POST') {
    Auth::requirePermission('orders.create');
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

    try {
        $userId = Auth::user()['id'] ?? 1;
        $result = OrderEngine::createDraftOrder($input, $userId);
        Response::json(true, 201, "Order #{$result['order_number']} initialized", $result);
    } catch (Exception $e) {
        Response::json(false, 422, "Failed to create order: " . $e->getMessage());
    }

} else {
    Response::json(false, 405, "Method Not Allowed");
}
