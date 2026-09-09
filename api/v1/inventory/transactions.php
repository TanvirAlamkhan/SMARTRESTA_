<?php
/**
 * SMARTRESTA API Endpoint: Inventory Transaction History / Audit Ledger
 * GET /api/v1/inventory/transactions.php?ingredient_id={iid}&location_id={lid}&type={t}&limit=100
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/InventoryEngine.php';
require_once __DIR__ . '/../../../core/Response.php';

Auth::requireAuth();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        if (!Auth::hasPermission('inventory.history.view') && !Auth::hasPermission('inventory.view')) {
            Response::json(false, 403, "Permission denied: inventory.history.view");
            exit;
        }

        $ingredientId = isset($_GET['ingredient_id']) ? (int)$_GET['ingredient_id'] : null;
        $locationId = isset($_GET['location_id']) ? (int)$_GET['location_id'] : null;
        $type = trim($_GET['type'] ?? '');
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 100;

        $ledger = InventoryEngine::getTransactionsLedger($ingredientId, $locationId, $type, $limit);
        Response::json(true, 200, "Inventory audit transactions ledger retrieved successfully", ['transactions' => $ledger]);

    } catch (Exception $e) {
        $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
        Response::json(false, $code, "Failed to load inventory transactions ledger: " . $e->getMessage());
    }
} else {
    Response::json(false, 405, "Method not allowed");
}
