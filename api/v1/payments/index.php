<?php
/**
 * SMARTRESTA API Endpoint: Payments Management & Processing
 * GET /api/v1/payments/index.php
 * POST /api/v1/payments/index.php
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/PaymentEngine.php';
require_once __DIR__ . '/../../../core/Response.php';

Auth::requireAuth();

$method = $_SERVER['REQUEST_METHOD'];
$branchId = $_SESSION['branch_id'] ?? 1;
$userId = $_SESSION['user_id'] ?? 1;

if ($method === 'GET') {
    try {
        $filters = [
            'branch_id' => $branchId,
            'order_id' => $_GET['order_id'] ?? null,
            'status' => $_GET['status'] ?? null
        ];
        $payments = PaymentEngine::getPaymentsList($filters);
        Response::json(true, 200, "Payment history retrieved successfully", $payments);
    } catch (Exception $e) {
        Response::json(false, 500, "Failed to load payment history: " . $e->getMessage());
    }
} else if ($method === 'POST') {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            Response::json(false, 400, "Invalid JSON payload.");
            exit;
        }

        $result = PaymentEngine::processPayment($data, $userId);
        Response::json(true, 200, "Payment processed successfully.", $result);
    } catch (Exception $e) {
        Response::json(false, 400, "Payment failed: " . $e->getMessage());
    }
} else {
    Response::json(false, 405, "Method not allowed");
}
