<?php
/**
 * SMARTRESTA Public API Endpoint: QR Order Submission & Live Tracking
 * POST/GET /api/v1/public/qr_order.php
 */

require_once __DIR__ . '/../../../core/Response.php';
require_once __DIR__ . '/../../../core/CRMEngine.php';
require_once __DIR__ . '/../../../core/OrderEngine.php';

header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $token = $input['token'] ?? $_GET['token'] ?? '';
        if (empty($token)) {
            Response::json(false, 400, "QR token is required.");
        }
        $result = CRMEngine::submitPublicQROrder($token, $input);
        Response::json(true, 201, "Order submitted successfully to kitchen.", $result);
    } elseif ($method === 'GET') {
        $orderId = (int)($_GET['order_id'] ?? 0);
        if (!$orderId) {
            Response::json(false, 400, "Order ID is required for tracking.");
        }
        $order = OrderEngine::getOrderDetails($orderId);
        if (!$order) {
            Response::json(false, 404, "Order not found.");
        }
        // Sanitize sensitive internal waiter/financial data for public view
        $publicOrder = [
            'order_id' => $order['id'],
            'order_number' => $order['order_number'],
            'table_number' => $order['table_number'],
            'order_status' => $order['order_status'],
            'total' => $order['total'],
            'created_at' => $order['created_at'],
            'items' => array_map(function($i) {
                return [
                    'item_name' => $i['item_name'],
                    'variant_name' => $i['variant_name'],
                    'quantity' => $i['quantity'],
                    'subtotal' => $i['subtotal'],
                    'status' => $i['status']
                ];
            }, $order['items'] ?? [])
        ];
        Response::json(true, 200, "Order status retrieved.", $publicOrder);
    } else {
        Response::json(false, 405, "Method Not Allowed");
    }
} catch (Exception $e) {
    $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
    Response::json(false, $code, $e->getMessage());
}
