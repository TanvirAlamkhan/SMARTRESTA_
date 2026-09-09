<?php
/**
 * SMARTRESTA API Endpoint: Refunds Management & Processing
 * GET /api/v1/refunds/index.php
 * POST /api/v1/refunds/index.php
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/RefundEngine.php';
require_once __DIR__ . '/../../../core/Response.php';

Auth::requireAuth();

$method = $_SERVER['REQUEST_METHOD'];
$branchId = $_SESSION['branch_id'] ?? 1;
$userId = $_SESSION['user_id'] ?? 1;

if ($method === 'GET') {
    try {
        $orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : null;
        $refunds = RefundEngine::getRefundsList($branchId, $orderId);
        Response::json(true, 200, "Refunds history retrieved successfully", $refunds);
    } catch (Exception $e) {
        Response::json(false, 500, "Failed to load refunds: " . $e->getMessage());
    }
} else if ($method === 'POST') {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            Response::json(false, 400, "Invalid JSON payload.");
            exit;
        }

        $result = RefundEngine::processRefund($data, $userId);
        Response::json(true, 200, "Refund processed successfully.", $result);
    } catch (Exception $e) {
        Response::json(false, 400, "Refund processing failed: " . $e->getMessage());
    }
} else {
    Response::json(false, 405, "Method not allowed");
}
