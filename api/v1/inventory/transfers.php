<?php
/**
 * SMARTRESTA API Endpoint: Stock Transfers Between Locations
 * GET  /api/v1/inventory/transfers.php?branch_id={bid}
 * POST /api/v1/inventory/transfers.php
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/InventoryEngine.php';
require_once __DIR__ . '/../../../core/Response.php';

Auth::requireAuth();

$method = $_SERVER['REQUEST_METHOD'];
$branchId = $_SESSION['branch_id'] ?? 1;
$userId = $_SESSION['user_id'] ?? 1;

if ($method === 'GET') {
    try {
        Auth::requirePermission('inventory.transfer');
        $bId = isset($_GET['branch_id']) ? (int)$_GET['branch_id'] : $branchId;
        $transfers = InventoryEngine::getTransfers($bId);
        Response::json(true, 200, "Stock transfers retrieved successfully", ['transfers' => $transfers]);
    } catch (Exception $e) {
        $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
        Response::json(false, $code, "Failed to load stock transfers: " . $e->getMessage());
    }
} else if ($method === 'POST') {
    try {
        Auth::requirePermission('inventory.transfer');
        $data = json_decode(file_get_contents('php://input'), true);

        $fromLoc = (int)($data['from_location_id'] ?? 0);
        $toLoc = (int)($data['to_location_id'] ?? 0);
        $items = $data['items'] ?? [];

        if (!$fromLoc || !$toLoc || empty($items) || !is_array($items)) {
            Response::json(false, 400, "from_location_id, to_location_id, and items array are required.");
            exit;
        }

        $transferId = InventoryEngine::transferStock($fromLoc, $toLoc, $items, $userId, $data['notes'] ?? '');
        Response::json(true, 200, "Stock transferred successfully between locations", ['transfer_id' => $transferId]);

    } catch (Exception $e) {
        $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 400;
        Response::json(false, $code, "Failed to transfer stock: " . $e->getMessage());
    }
} else {
    Response::json(false, 405, "Method not allowed");
}
