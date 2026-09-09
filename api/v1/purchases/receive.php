<?php
/**
 * SMARTRESTA API Endpoint: Receive Goods against Purchase Order
 * POST /api/v1/purchases/receive.php
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/InventoryEngine.php';
require_once __DIR__ . '/../../../core/Response.php';

Auth::requireAuth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        Auth::requirePermission('purchases.receive');
        $data = json_decode(file_get_contents('php://input'), true);

        $poId = (int)($data['po_id'] ?? 0);
        $locationId = (int)($data['location_id'] ?? 0);
        $items = $data['items'] ?? [];

        if (!$poId || !$locationId || empty($items) || !is_array($items)) {
            Response::json(false, 400, "po_id, location_id, and items array are required.");
            exit;
        }

        $userId = $_SESSION['user_id'] ?? 1;
        $receiptId = InventoryEngine::receiveGoods($poId, $locationId, $items, $userId, $data['invoice_number'] ?? '', $data['notes'] ?? '');
        Response::json(true, 200, "Goods received and inventory updated successfully", ['receipt_id' => $receiptId]);

    } catch (Exception $e) {
        $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 400;
        Response::json(false, $code, "Failed to receive goods: " . $e->getMessage());
    }
} else {
    Response::json(false, 405, "Method not allowed");
}
