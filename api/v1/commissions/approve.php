<?php
/**
 * SMARTRESTA API Endpoint: Batch Approve Pending Commissions
 * POST /api/v1/commissions/approve.php
 */

require_once __DIR__ . '/../../config/env.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/CommissionEngine.php';
require_once __DIR__ . '/../../helpers/response.php';

Auth::requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(false, 405, "Method not allowed.");
    exit;
}

try {
    Auth::requirePermission('commissions.approve');
    $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $user = Auth::getCurrentUser();

    $commissionIds = !empty($data['commission_ids']) && is_array($data['commission_ids']) ? $data['commission_ids'] : [];

    if (empty($commissionIds)) {
        sendJsonResponse(false, 400, "Please select at least one commission transaction to approve.");
        exit;
    }

    $res = CommissionEngine::approveCommissions($commissionIds, (int)$user['id']);
    sendJsonResponse(true, 200, "Commissions approved successfully", $res);

} catch (Exception $e) {
    sendJsonResponse(false, 400, $e->getMessage());
}
