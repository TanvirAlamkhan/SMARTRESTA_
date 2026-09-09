<?php
/**
 * SMARTRESTA API Endpoint: Payment Receipts Lookup
 * GET /api/v1/receipts/index.php?id={receipt_id}
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/ReceiptEngine.php';
require_once __DIR__ . '/../../../core/Response.php';

Auth::requireAuth();

try {
    $receiptId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $paymentId = isset($_GET['payment_id']) ? (int)$_GET['payment_id'] : 0;
    $orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
    $userId = $_SESSION['user_id'] ?? 1;

    if ($receiptId) {
        $receipt = ReceiptEngine::getReceipt($receiptId);
        Response::json(true, 200, "Receipt retrieved successfully", $receipt);
    } else if ($paymentId && $orderId) {
        $receipt = ReceiptEngine::generateReceipt($paymentId, $orderId, $userId);
        Response::json(true, 200, "Receipt retrieved successfully", $receipt);
    } else {
        Response::json(false, 400, "Receipt ID or payment_id & order_id required.");
    }
} catch (Exception $e) {
    Response::json(false, 404, "Receipt error: " . $e->getMessage());
}
