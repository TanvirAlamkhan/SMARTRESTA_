<?php
/**
 * SMARTRESTA API Endpoint: Execute Business Day Close
 * POST /api/v1/day_closing/close.php
 */

require_once __DIR__ . '/../../config/env.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/FinanceEngine.php';
require_once __DIR__ . '/../../helpers/response.php';

Auth::requireAuth();

try {
    Auth::requirePermission('day_closing.close');
    $rawInput = file_get_contents('php://input');
    $data = json_decode($rawInput, true) ?: $_POST;

    $branchId = (int)($data['branch_id'] ?? 1);
    $businessDate = !empty($data['business_date']) ? trim($data['business_date']) : date('Y-m-d');
    $userId = Auth::getUserId();

    $result = FinanceEngine::closeBusinessDay($branchId, $businessDate, $userId);
    sendJsonResponse(true, 200, "Business day closed successfully", $result);

} catch (Exception $e) {
    sendJsonResponse(false, 400, $e->getMessage());
}
