<?php
/**
 * SMARTRESTA API Endpoint: Manual Stock Adjustment (IN / OUT)
 * POST /api/v1/inventory/adjust.php
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/InventoryEngine.php';
require_once __DIR__ . '/../../../core/Response.php';

Auth::requireAuth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        Auth::requirePermission('inventory.adjust');
        $data = json_decode(file_get_contents('php://input'), true);

        $locationId = (int)($data['location_id'] ?? 1);
        $ingredientId = (int)($data['ingredient_id'] ?? 0);
        $type = strtoupper(trim($data['type'] ?? ''));
        $direction = str_contains($type, 'IN') ? 'IN' : 'OUT';
        $quantity = (float)($data['quantity'] ?? 0);
        $reason = trim($data['reason'] ?? '');
        $notes = trim($data['notes'] ?? '');

        if (!$ingredientId || $quantity <= 0) {
            Response::json(false, 400, "Valid ingredient_id and positive quantity are required.");
            exit;
        }

        $userId = $_SESSION['user_id'] ?? 1;
        $result = InventoryEngine::adjustStock($ingredientId, $locationId, $quantity, $direction, $reason, $userId, $notes);
        Response::json(true, 200, "Stock adjustment recorded successfully", $result);

    } catch (Exception $e) {
        $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 400;
        Response::json(false, $code, "Failed to adjust stock: " . $e->getMessage());
    }
} else {
    Response::json(false, 405, "Method not allowed");
}
