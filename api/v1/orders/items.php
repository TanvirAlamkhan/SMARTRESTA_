<?php
/**
 * SMARTRESTA Order Items API Endpoint
 * POST   /api/v1/orders/items.php (Add item with variant & modifier snapshots to order)
 * PUT    /api/v1/orders/items.php (Update item quantity)
 * DELETE /api/v1/orders/items.php (Remove item from order)
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/OrderEngine.php';
require_once __DIR__ . '/../../../core/Response.php';

Auth::requireAuth();

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
$userId = Auth::user()['id'] ?? 1;

if ($method === 'POST') {
    Auth::requirePermission('orders.update');

    $orderId = !empty($input['order_id']) ? (int)$input['order_id'] : 0;
    $productId = !empty($input['product_id']) ? (int)$input['product_id'] : 0;
    $variantId = !empty($input['variant_id']) ? (int)$input['variant_id'] : null;
    $selectedModifiers = !empty($input['modifier_ids']) && is_array($input['modifier_ids']) ? $input['modifier_ids'] : [];
    $quantity = !empty($input['quantity']) ? (int)$input['quantity'] : 1;
    $notes = !empty($input['notes']) ? trim($input['notes']) : null;

    if ($orderId <= 0 || $productId <= 0) {
        Response::json(false, 422, "Valid order_id and product_id are required.");
    }

    try {
        $result = OrderEngine::addItemToOrder($orderId, $productId, $variantId, $selectedModifiers, $quantity, $notes, $userId);
        Response::json(true, 201, "Item added to order successfully", $result);
    } catch (Exception $e) {
        Response::json(false, 422, "Failed to add item: " . $e->getMessage());
    }

} elseif ($method === 'PUT') {
    Auth::requirePermission('orders.update');

    $orderId = !empty($input['order_id']) ? (int)$input['order_id'] : 0;
    $orderItemId = !empty($input['order_item_id']) ? (int)$input['order_item_id'] : 0;
    $quantity = isset($input['quantity']) ? (int)$input['quantity'] : 1;

    if ($orderId <= 0 || $orderItemId <= 0) {
        Response::json(false, 422, "Valid order_id and order_item_id are required.");
    }

    try {
        OrderEngine::updateItemQuantity($orderId, $orderItemId, $quantity, $userId);
        Response::json(true, 200, "Item quantity updated successfully");
    } catch (Exception $e) {
        Response::json(false, 500, "Failed to update item quantity: " . $e->getMessage());
    }

} elseif ($method === 'DELETE') {
    Auth::requirePermission('orders.update');

    $orderId = !empty($input['order_id']) ? (int)$input['order_id'] : 0;
    $orderItemId = !empty($input['order_item_id']) ? (int)$input['order_item_id'] : 0;

    if ($orderId <= 0 || $orderItemId <= 0) {
        Response::json(false, 422, "Valid order_id and order_item_id are required.");
    }

    try {
        OrderEngine::removeItemFromOrder($orderId, $orderItemId, $userId);
        Response::json(true, 200, "Item removed from order successfully");
    } catch (Exception $e) {
        Response::json(false, 500, "Failed to remove item: " . $e->getMessage());
    }

} else {
    Response::json(false, 405, "Method Not Allowed");
}
