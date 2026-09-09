<?php
/**
 * SMARTRESTA API Endpoint: Coupons & Promotions Management
 * GET/POST /api/v1/crm/coupons.php
 */

require_once __DIR__ . '/../../../core/Response.php';
require_once __DIR__ . '/../../../core/CRMEngine.php';

header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        $coupons = CRMEngine::getCoupons($_GET);
        Response::json(true, 200, "Coupons retrieved.", ['coupons' => $coupons]);
    } elseif ($method === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $action = $input['action'] ?? 'create';

        if ($action === 'validate') {
            $code = $input['code'] ?? '';
            $amount = (float)($input['order_amount'] ?? 0);
            $cust = !empty($input['customer_id']) ? (int)$input['customer_id'] : null;
            $branch = !empty($input['branch_id']) ? (int)$input['branch_id'] : null;

            if (empty($code)) Response::json(false, 400, "Coupon code is required.");
            $res = CRMEngine::validateCoupon($code, $amount, $cust, $branch);
            if ($res['valid']) {
                Response::json(true, 200, "Coupon '{$code}' is valid.", $res);
            } else {
                Response::json(false, 422, $res['message'], null, ['coupon' => $res['message']]);
            }
        } elseif ($action === 'create') {
            $coupon = CRMEngine::createCoupon($input, 1);
            Response::json(true, 201, "Coupon created successfully.", $coupon);
        } else {
            Response::json(false, 400, "Invalid action specified.");
        }
    } else {
        Response::json(false, 405, "Method Not Allowed");
    }
} catch (Exception $e) {
    $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
    Response::json(false, $code, $e->getMessage());
}
