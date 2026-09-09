<?php
/**
 * SMARTRESTA API Endpoint: Pay Operating Expense
 * POST /api/v1/expenses/pay.php
 */

require_once __DIR__ . '/../../config/env.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/FinanceEngine.php';
require_once __DIR__ . '/../../helpers/response.php';

Auth::requireAuth();

try {
    Auth::requirePermission('expenses.pay');
    $rawInput = file_get_contents('php://input');
    $data = json_decode($rawInput, true) ?: $_POST;

    $expenseId = (int)($data['expense_id'] ?? 0);
    $paymentMethodId = (int)($data['payment_method_id'] ?? 1);
    $userId = Auth::getUserId();

    if ($expenseId <= 0) {
        throw new Exception("Valid expense_id required.");
    }

    $result = FinanceEngine::payExpense($expenseId, $paymentMethodId, $userId);
    sendJsonResponse(true, 200, "Expense paid successfully", $result);

} catch (Exception $e) {
    sendJsonResponse(false, 400, $e->getMessage());
}
