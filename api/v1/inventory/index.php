<?php
/**
 * SMARTRESTA API Endpoint: Stock Balances & Locations Summary
 * GET /api/v1/inventory/index.php?branch_id={bid}&location_id={lid}
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/InventoryEngine.php';
require_once __DIR__ . '/../../../core/Response.php';

Auth::requireAuth();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        Auth::requirePermission('inventory.view');
        $branchId = $_SESSION['branch_id'] ?? 1;
        $bId = isset($_GET['branch_id']) ? (int)$_GET['branch_id'] : $branchId;
        $locationId = isset($_GET['location_id']) ? (int)$_GET['location_id'] : null;

        $balances = InventoryEngine::getStockBalances($bId, $locationId);
        $locations = InventoryEngine::getLocations($bId);

        Response::json(true, 200, "Stock balances retrieved successfully", [
            'balances' => $balances,
            'locations' => $locations
        ]);

    } catch (Exception $e) {
        $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
        Response::json(false, $code, "Failed to load stock balances: " . $e->getMessage());
    }
} else {
    Response::json(false, 405, "Method not allowed");
}
