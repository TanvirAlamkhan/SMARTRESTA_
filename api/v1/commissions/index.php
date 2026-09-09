<?php
/**
 * SMARTRESTA API Endpoint: List Commission Transactions
 * GET /api/v1/commissions/index.php
 */

require_once __DIR__ . '/../../config/env.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/CommissionEngine.php';
require_once __DIR__ . '/../../helpers/response.php';

Auth::requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendJsonResponse(false, 405, "Method not allowed.");
    exit;
}

try {
    Auth::requirePermission('commissions.view');

    $branchId = isset($_GET['branch_id']) ? (int)$_GET['branch_id'] : 1;
    $waiterId = !empty($_GET['waiter_id']) ? (int)$_GET['waiter_id'] : null;
    $status = !empty($_GET['status']) ? trim($_GET['status']) : null;
    $startDate = !empty($_GET['start_date']) ? trim($_GET['start_date']) : null;
    $endDate = !empty($_GET['end_date']) ? trim($_GET['end_date']) : null;

    $commissions = CommissionEngine::getCommissionsList($branchId, $waiterId, $status, $startDate, $endDate);
    sendJsonResponse(true, 200, "Commission transactions retrieved successfully", $commissions);

} catch (Exception $e) {
    sendJsonResponse(false, 400, $e->getMessage());
}
