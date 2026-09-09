<?php
/**
 * SMARTRESTA API Endpoint: Approve Purchase Order
 * POST /api/v1/purchases/approve.php
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/InventoryEngine.php';
require_once __DIR__ . '/../../../core/Response.php';

Auth::requireAuth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        Auth::requirePermission('purchases.approve');
        $data = json_decode(file_get_contents('php://input'), true);

        $poId = (int)($data['po_id'] ?? 0);
        if (!$poId) {
            Response::json(false, 400, "po_id is required for approval.");
            exit;
        }

        $userId = $_SESSION['user_id'] ?? 1;
        $result = InventoryEngine::approvePurchaseOrder($poId, $userId);
        Response::json(true, 200, "Purchase order approved successfully", $result);

    } catch (Exception $e) {
        $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 400;
        Response::json(false, $code, "Failed to approve purchase order: " . $e->getMessage());
    }
} else {
    Response::json(false, 405, "Method not allowed");
}
