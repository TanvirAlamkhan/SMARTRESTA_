<?php
/**
 * SMARTRESTA API Endpoint: Close Cashier Shift
 * POST /api/v1/shifts/close.php
 */

require_once __DIR__ . '/../../../config/env.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/FinanceEngine.php';
require_once __DIR__ . '/../../../core/Response.php';

Auth::requireAuth();

try {
    Auth::requirePermission('shifts.close');
    $rawInput = file_get_contents('php://input');
    $data = json_decode($rawInput, true) ?: $_POST;

    $shiftId = (int)($data['shift_id'] ?? 0);
    $actualCash = (float)($data['actual_cash'] ?? 0);
    $denominations = is_array($data['denominations'] ?? null) ? $data['denominations'] : [];
    $notes = isset($data['notes']) ? trim($data['notes']) : null;
    $userId = Auth::userId();

    if ($shiftId <= 0) {
        throw new Exception("Valid shift_id required.");
    }

    $result = FinanceEngine::closeShift($shiftId, $actualCash, $denominations, $userId, $notes);
    Response::json(true, 200, "Cashier shift closed successfully", $result);

} catch (Exception $e) {
    Response::json(false, 400, $e->getMessage());
}
