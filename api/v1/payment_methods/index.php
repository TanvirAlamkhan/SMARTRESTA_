<?php
/**
 * SMARTRESTA API Endpoint: Active Payment Methods
 * GET /api/v1/payment_methods/index.php
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/PaymentEngine.php';
require_once __DIR__ . '/../../../core/Response.php';

Auth::requireAuth();

try {
    $branchId = $_SESSION['branch_id'] ?? 1;
    $methods = PaymentEngine::getPaymentMethods($branchId);
    Response::json(true, 200, "Payment methods retrieved successfully", $methods);
} catch (Exception $e) {
    Response::json(false, 500, "Failed to load payment methods: " . $e->getMessage());
}
