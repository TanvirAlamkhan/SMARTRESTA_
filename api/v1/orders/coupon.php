<?php
/**
 * SMARTRESTA Orders Coupon Application API Endpoint
 * POST /api/v1/orders/coupon.php
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/OrderEngine.php';
require_once __DIR__ . '/../../../core/CRMEngine.php';
require_once __DIR__ . '/../../../core/Response.php';

Auth::requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::json(false, 405, "Method Not Allowed");
}

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
$action = $input['action'] ?? 'apply';
$orderId = !empty($input['order_id']) ? (int)$input['order_id'] : 0;
$couponCode = !empty($input['coupon_code']) ? trim($input['coupon_code']) : (!empty($input['code']) ? trim($input['code']) : '');

if ($orderId <= 0) {
    Response::json(false, 422, "Valid order_id is required.");
}

try {
    $userId = Auth::user()['id'] ?? 1;
    if ($action === 'apply') {
        if (empty($couponCode)) {
            Response::json(false, 422, "Coupon code is required.");
        }
        $result = OrderEngine::applyCoupon($orderId, $couponCode, $userId);
        Response::json(true, 200, "Coupon '{$result['coupon_code']}' applied successfully. Discount: ৳{$result['discount_amount']}", $result);
    } elseif ($action === 'remove') {
        $result = OrderEngine::removeCoupon($orderId, $userId);
        Response::json(true, 200, "Coupon removed from order.", $result);
    } else {
        Response::json(false, 400, "Invalid action specified.");
    }
} catch (Exception $e) {
    Response::json(false, 422, $e->getMessage());
}
