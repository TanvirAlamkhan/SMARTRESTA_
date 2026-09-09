<?php
/**
 * SMARTRESTA API Endpoint: Record Cash Movement (Cash In / Cash Out)
 * POST /api/v1/cash/in_out.php
 */

require_once __DIR__ . '/../../config/env.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/FinanceEngine.php';
require_once __DIR__ . '/../../helpers/response.php';

Auth::requireAuth();

try {
    Auth::requirePermission('cash.in');
    $rawInput = file_get_contents('php://input');
    $data = json_decode($rawInput, true) ?: $_POST;
    $userId = Auth::getUserId();

    $result = FinanceEngine::recordCashMovement($data, $userId);
    sendJsonResponse(true, 201, "Cash movement logged successfully", $result);

} catch (Exception $e) {
    sendJsonResponse(false, 400, $e->getMessage());
}
