<?php
/**
 * SMARTRESTA API Endpoint: Expenses Listing & Creation
 * GET / POST /api/v1/expenses/index.php
 */

require_once __DIR__ . '/../../config/env.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/FinanceEngine.php';
require_once __DIR__ . '/../../helpers/response.php';

Auth::requireAuth();

$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        Auth::requirePermission('expenses.view');
        $branchId = isset($_GET['branch_id']) ? (int)$_GET['branch_id'] : 1;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 25;

        $filters = [
            'status' => isset($_GET['status']) ? trim($_GET['status']) : null,
            'category_id' => isset($_GET['category_id']) ? (int)$_GET['category_id'] : null,
            'date_from' => isset($_GET['date_from']) ? trim($_GET['date_from']) : null,
            'date_to' => isset($_GET['date_to']) ? trim($_GET['date_to']) : null
        ];

        $report = FinanceEngine::getExpensesList($branchId, $filters, $page, $perPage);
        sendJsonResponse(true, 200, "Expenses list retrieved", $report);

    } elseif ($method === 'POST') {
        Auth::requirePermission('expenses.create');
        $rawInput = file_get_contents('php://input');
        $data = json_decode($rawInput, true) ?: $_POST;
        $userId = Auth::getUserId();

        $result = FinanceEngine::createExpense($data, $userId);
        sendJsonResponse(true, 201, "Operating expense recorded successfully", $result);
    } else {
        sendJsonResponse(false, 405, "Method not allowed");
    }

} catch (Exception $e) {
    sendJsonResponse(false, 400, $e->getMessage());
}
