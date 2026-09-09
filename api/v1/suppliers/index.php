<?php
/**
 * SMARTRESTA API Endpoint: Supplier Management
 * GET  /api/v1/suppliers/index.php?search={q}
 * POST /api/v1/suppliers/index.php
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/InventoryEngine.php';
require_once __DIR__ . '/../../../core/Response.php';

Auth::requireAuth();

$method = $_SERVER['REQUEST_METHOD'];
$userId = $_SESSION['user_id'] ?? 1;

if ($method === 'GET') {
    try {
        Auth::requirePermission('inventory.view');
        $search = trim($_GET['search'] ?? '');
        $suppliers = InventoryEngine::getSuppliers($search);
        Response::json(true, 200, "Suppliers retrieved successfully", ['suppliers' => $suppliers]);
    } catch (Exception $e) {
        $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
        Response::json(false, $code, "Failed to load suppliers: " . $e->getMessage());
    }
} else if ($method === 'POST') {
    try {
        Auth::requirePermission('suppliers.manage');
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['name'])) {
            Response::json(false, 400, "Supplier name is required.");
            exit;
        }

        $id = InventoryEngine::saveSupplier($data, $userId);
        Response::json(true, 200, "Supplier saved successfully", ['supplier_id' => $id]);

    } catch (Exception $e) {
        $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 400;
        Response::json(false, $code, "Failed to save supplier: " . $e->getMessage());
    }
} else {
    Response::json(false, 405, "Method not allowed");
}
