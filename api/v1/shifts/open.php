<?php
/**
 * SMARTRESTA API Endpoint: Open Cashier Shift
 * POST /api/v1/shifts/open.php
 */

require_once __DIR__ . '/../../../config/env.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/FinanceEngine.php';
require_once __DIR__ . '/../../../core/Response.php';

Auth::requireAuth();

try {
    Auth::requirePermission('shifts.open');
    $rawInput = file_get_contents('php://input');
    $data = json_decode($rawInput, true) ?: $_POST;

    $branchId = (int)($data['branch_id'] ?? 1);
    $drawerId = (int)($data['cash_drawer_id'] ?? 1);
    $openingCash = (float)($data['opening_cash'] ?? 0);
    $notes = isset($data['notes']) ? trim($data['notes']) : null;
    $userId = Auth::userId();

    $shift = FinanceEngine::openShift($branchId, $userId, $drawerId, $openingCash, $notes);
    Response::json(true, 201, "Cashier shift opened successfully", $shift);

} catch (Exception $e) {
    Response::json(false, 400, $e->getMessage());
}
