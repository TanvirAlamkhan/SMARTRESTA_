<?php
/**
 * SMARTRESTA Modifiers API Endpoint
 * GET  /api/v1/modifiers/index.php (List Modifiers)
 * POST /api/v1/modifiers/index.php (Create modifier OR Attach/Detach to Product)
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/MenuEngine.php';
require_once __DIR__ . '/../../../core/Response.php';

Auth::requireAuth();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    Auth::requirePermission('products.view');

    try {
        $modifiers = MenuEngine::getModifiers();
        Response::json(true, 200, "Modifiers retrieved successfully", $modifiers);
    } catch (Exception $e) {
        Response::json(false, 500, "Failed to load modifiers: " . $e->getMessage());
    }

} elseif ($method === 'POST') {
    Auth::requirePermission('modifiers.manage');
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

    $action = trim($input['action'] ?? 'create');
    $userId = Auth::user()['id'] ?? 1;

    try {
        if ($action === 'attach') {
            $productId = (int)($input['product_id'] ?? 0);
            $modifierId = (int)($input['modifier_id'] ?? 0);
            if ($productId <= 0 || $modifierId <= 0) {
                Response::json(false, 422, "Valid product_id and modifier_id are required.");
            }
            MenuEngine::attachModifierToProduct($productId, $modifierId, $userId);
            Response::json(true, 200, "Modifier attached to product successfully");

        } elseif ($action === 'detach') {
            $productId = (int)($input['product_id'] ?? 0);
            $modifierId = (int)($input['modifier_id'] ?? 0);
            if ($productId <= 0 || $modifierId <= 0) {
                Response::json(false, 422, "Valid product_id and modifier_id are required.");
            }
            MenuEngine::detachModifierFromProduct($productId, $modifierId, $userId);
            Response::json(true, 200, "Modifier detached from product successfully");

        } else {
            // Create Modifier
            $name = trim($input['name'] ?? '');
            $price = (float)($input['price'] ?? 0.00);
            $description = !empty($input['description']) ? trim($input['description']) : null;

            if (empty($name)) {
                Response::json(false, 422, "Modifier name is required.");
            }
            $result = MenuEngine::createModifier($name, $price, $description, $userId);
            Response::json(true, 201, "Modifier '{$name}' created successfully", $result);
        }
    } catch (Exception $e) {
        Response::json(false, 500, "Modifier action failed: " . $e->getMessage());
    }

} else {
    Response::json(false, 405, "Method Not Allowed");
}
