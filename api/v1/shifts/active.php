<?php
/**
 * SMARTRESTA API Endpoint: Active Cashier Shift
 * GET /api/v1/shifts/active.php
 */

require_once __DIR__ . '/../../config/env.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/FinanceEngine.php';
require_once __DIR__ . '/../../helpers/response.php';

Auth::requireAuth();

try {
    Auth::requirePermission('shifts.view');

    $branchId = isset($_GET['branch_id']) ? (int)$_GET['branch_id'] : 1;
    $userId = Auth::getUserId();

    $shift = FinanceEngine::getActiveShift($userId, $branchId);
    sendJsonResponse(true, 200, "Active shift state retrieved", $shift);

} catch (Exception $e) {
    sendJsonResponse(false, 500, "Failed to retrieve active shift: " . $e->getMessage());
}
