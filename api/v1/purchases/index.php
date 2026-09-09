<?php
/**
 * SMARTRESTA API Endpoint: Purchase Orders Management
 * GET  /api/v1/purchases/index.php?id={poid}&supplier_id={sid}&status={st}
 * POST /api/v1/purchases/index.php
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
        Auth::requirePermission('inventory.view');
        $poId = isset($_GET['id']) ? (int)$_GET['id'] : null;

        if ($poId) {
            $po = InventoryEngine::getPurchaseOrder($poId);
            Response::json(true, 200, "Purchase order details retrieved successfully", ['purchase_order' => $po]);
        } else {
            $bId = isset($_GET['branch_id']) ? (int)$_GET['branch_id'] : $branchId;
            $supplierId = isset($_GET['supplier_id']) ? (int)$_GET['supplier_id'] : null;
            $status = trim($_GET['status'] ?? '');
            $pos = InventoryEngine::getPurchaseOrders($bId, $supplierId, $status);
            Response::json(true, 200, "Purchase orders retrieved successfully", ['purchase_orders' => $pos]);
        }
    } catch (Exception $e) {
        $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
        Response::json(false, $code, "Failed to load purchase orders: " . $e->getMessage());
    }
} else if ($method === 'POST') {
    try {
        Auth::requirePermission('purchases.manage');
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['supplier_id']) || empty($data['items']) || !is_array($data['items'])) {
            Response::json(false, 400, "supplier_id and items array are required.");
            exit;
        }

        $data['branch_id'] = $data['branch_id'] ?? $branchId;
        $poId = InventoryEngine::createPurchaseOrder($data, $userId);
        Response::json(true, 200, "Purchase order created successfully", ['po_id' => $poId]);

    } catch (Exception $e) {
        $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 400;
        Response::json(false, $code, "Failed to create purchase order: " . $e->getMessage());
    }
} else {
    Response::json(false, 405, "Method not allowed");
}
