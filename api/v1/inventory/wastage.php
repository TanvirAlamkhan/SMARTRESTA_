<?php
/**
 * SMARTRESTA API Endpoint: Wastage Recording & Audit Logs
 * GET  /api/v1/inventory/wastage.php?branch_id={bid}&location_id={lid}
 * POST /api/v1/inventory/wastage.php
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
        Auth::requirePermission('inventory.wastage');
        $bId = isset($_GET['branch_id']) ? (int)$_GET['branch_id'] : $branchId;
        $locationId = isset($_GET['location_id']) ? (int)$_GET['location_id'] : null;

        $wastageLogs = InventoryEngine::getWastageLogs($bId, $locationId);
        Response::json(true, 200, "Wastage logs retrieved successfully", ['wastage_records' => $wastageLogs]);
    } catch (Exception $e) {
        $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
        Response::json(false, $code, "Failed to load wastage logs: " . $e->getMessage());
    }
} else if ($method === 'POST') {
    try {
        Auth::requirePermission('inventory.wastage');
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['location_id']) || empty($data['quantity']) || empty($data['reason'])) {
            Response::json(false, 400, "location_id, quantity, and reason are required.");
            exit;
        }

        $data['branch_id'] = $data['branch_id'] ?? $branchId;
        $wastageId = InventoryEngine::recordWastage($data, $userId);
        Response::json(true, 200, "Wastage recorded successfully", ['wastage_id' => $wastageId]);

    } catch (Exception $e) {
        $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 400;
        Response::json(false, $code, "Failed to record wastage: " . $e->getMessage());
    }
} else {
    Response::json(false, 405, "Method not allowed");
}
