<?php
/**
 * SMARTRESTA API Endpoint: Create & Submit Order with Multi-Station Routing
 * POST /api/v1/orders/create.php
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/OrderEngine.php';
require_once __DIR__ . '/../../../core/RoutingEngine.php';
require_once __DIR__ . '/../../../core/Response.php';

Auth::requireAuth();
Auth::requirePermission('orders.create');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::json(false, 405, "Method Not Allowed");
}

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
$userId = Auth::user()['id'] ?? 1;

if (!$input) {
    Response::json(false, 400, "Invalid JSON payload.");
}

try {
    // 1. Create Draft Order
    $draftResult = OrderEngine::createDraftOrder($input, $userId);
    $orderId = (int)$draftResult['order_id'];

    // 2. Add Line Items if provided
    $items = !empty($input['items']) && is_array($input['items']) ? $input['items'] : [];
    foreach ($items as $item) {
        $productId = (int)($item['product_id'] ?? $item['id'] ?? 0);
        $variantId = !empty($item['variant_id']) ? (int)$item['variant_id'] : null;
        $modifiers = !empty($item['modifier_ids']) && is_array($item['modifier_ids']) ? $item['modifier_ids'] : [];
        $quantity = (int)($item['quantity'] ?? 1);
        $notes = !empty($item['notes']) ? trim($item['notes']) : null;

        if ($productId > 0 && $quantity > 0) {
            OrderEngine::addItemToOrder($orderId, $productId, $variantId, $modifiers, $quantity, $notes, $userId);
        }
    }

    // 3. Submit Order & Route to Stations if autoSubmit requested or items present
    $submitResult = null;
    if (!empty($items) || !empty($input['submit'])) {
        $submitResult = OrderEngine::submitOrder($orderId, $userId);
    }

    $routeSummary = RoutingEngine::getOrderRouteSummary($orderId);

    Response::json(true, 201, "Order #{$draftResult['order_number']} successfully created and routed", [
        'order_id' => $orderId,
        'order_number' => $draftResult['order_number'],
        'status' => $submitResult['status'] ?? 'DRAFT',
        'table_id' => $draftResult['table_id'],
        'dining_session_id' => $draftResult['dining_session_id'],
        'route_summary' => $routeSummary
    ]);

} catch (Exception $e) {
    Response::json(false, 422, "Order creation failed: " . $e->getMessage());
}
