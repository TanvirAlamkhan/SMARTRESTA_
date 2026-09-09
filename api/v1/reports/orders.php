<?php
/**
 * SMARTRESTA API Endpoint: Orders Report & Filtering
 * GET /api/v1/reports/orders.php?branch_id=1&preset=today&order_status=COMPLETED&page=1&per_page=25
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/ReportEngine.php';
require_once __DIR__ . '/../../../core/Response.php';

Auth::requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    Response::json(false, 405, "Method not allowed");
    exit;
}

try {
    Auth::requirePermission('reports.view');

    $branchId = $_SESSION['branch_id'] ?? 1;
    if (isset($_GET['branch_id']) && Auth::hasPermission('branches.manage')) {
        $branchId = (int)$_GET['branch_id'];
    }

    $dateFrom = $_GET['date_from'] ?? null;
    $dateTo = $_GET['date_to'] ?? null;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $perPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 25;

    $filters = [
        'order_status' => $_GET['order_status'] ?? null,
        'payment_status' => $_GET['payment_status'] ?? null,
        'order_type' => $_GET['order_type'] ?? null,
        'table_id' => $_GET['table_id'] ?? null,
        'waiter_id' => $_GET['waiter_id'] ?? null,
        'search' => $_GET['search'] ?? null
    ];

    $ordersData = ReportEngine::getOrdersReport($branchId, $dateFrom, $dateTo, $filters, $page, $perPage);

    Response::json(true, 200, "Orders report retrieved successfully", $ordersData);

} catch (Exception $e) {
    $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
    Response::json(false, $code, "Failed to load orders report: " . $e->getMessage());
}
