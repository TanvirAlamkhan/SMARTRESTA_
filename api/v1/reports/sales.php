<?php
/**
 * SMARTRESTA API Endpoint: Sales Analytics Report
 * GET /api/v1/reports/sales.php?branch_id=1&preset=this_month&group_by=date
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
    $groupBy = $_GET['group_by'] ?? 'date';

    $salesData = ReportEngine::getSalesReport($branchId, $dateFrom, $dateTo, $groupBy);

    Response::json(true, 200, "Sales report retrieved successfully", $salesData);

} catch (Exception $e) {
    $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
    Response::json(false, $code, "Failed to load sales report: " . $e->getMessage());
}
