<?php
/**
 * SMARTRESTA API Endpoint: Commission Payout Settlements
 * GET /api/v1/commissions/payout.php
 * POST /api/v1/commissions/payout.php
 */

require_once __DIR__ . '/../../config/env.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/PayoutEngine.php';
require_once __DIR__ . '/../../helpers/response.php';

Auth::requireAuth();

$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        Auth::requirePermission('commissions.view');
        $branchId = isset($_GET['branch_id']) ? (int)$_GET['branch_id'] : 1;
        $waiterId = !empty($_GET['waiter_id']) ? (int)$_GET['waiter_id'] : null;
        $payoutId = !empty($_GET['id']) ? (int)$_GET['id'] : null;

        if ($payoutId) {
            $details = PayoutEngine::getPayoutDetails($payoutId);
            sendJsonResponse(true, 200, "Payout details retrieved", $details);
        } else {
            $payouts = PayoutEngine::getPayoutsList($branchId, $waiterId);
            sendJsonResponse(true, 200, "Payouts list retrieved", $payouts);
        }

    } else if ($method === 'POST') {
        Auth::requirePermission('commissions.pay');
        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $user = Auth::getCurrentUser();

        $payout = PayoutEngine::createPayout($data, (int)$user['id']);
        sendJsonResponse(true, 201, "Commission payout processed successfully", $payout);

    } else {
        sendJsonResponse(false, 405, "Method not allowed.");
    }
} catch (Exception $e) {
    sendJsonResponse(false, 400, $e->getMessage());
}
