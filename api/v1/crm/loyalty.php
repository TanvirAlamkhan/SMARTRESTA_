<?php
/**
 * SMARTRESTA API Endpoint: Loyalty Account & Immutable Ledger
 * GET/POST /api/v1/crm/loyalty.php
 */

require_once __DIR__ . '/../../../core/Response.php';
require_once __DIR__ . '/../../../core/CRMEngine.php';

header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        $customerId = (int)($_GET['customer_id'] ?? 0);
        if (!$customerId) Response::json(false, 400, "Customer ID is required.");
        $account = CRMEngine::getLoyaltyAccount($customerId);
        Response::json(true, 200, "Loyalty account retrieved.", $account);
    } elseif ($method === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $action = $input['action'] ?? '';

        if ($action === 'adjust') {
            $customerId = (int)($input['customer_id'] ?? 0);
            $points = (float)($input['points'] ?? 0);
            $reason = $input['reason'] ?? '';
            if (!$customerId || !$reason) Response::json(false, 400, "Customer ID, points and audit reason required.");
            $res = CRMEngine::adjustPoints($customerId, $points, $reason, 1);
            Response::json(true, 200, "Loyalty points adjusted successfully.", $res);
        } elseif ($action === 'redeem') {
            $customerId = (int)($input['customer_id'] ?? 0);
            $points = (float)($input['points'] ?? 0);
            $orderId = (int)($input['order_id'] ?? 0);
            if (!$customerId || !$orderId || $points <= 0) Response::json(false, 400, "Customer ID, order ID, and points required.");
            $discount = CRMEngine::redeemPoints($customerId, $points, $orderId, 1);
            Response::json(true, 200, "Loyalty points redeemed successfully.", ['discount_amount' => $discount]);
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
