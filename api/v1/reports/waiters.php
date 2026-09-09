<?php
/**
 * SMARTRESTA API Endpoint: Waiter Performance Report
 * GET /api/v1/reports/waiters.php?branch_id=1&preset=this_month
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

    $data = ReportEngine::getWaiterPerformanceReport($branchId, $dateFrom, $dateTo, [], $page, $perPage);

    Response::json(true, 200, "Waiter performance report loaded successfully", $data);

} catch (Exception $e) {
    $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
    Response::json(false, $code, "Failed to load waiter performance report: " . $e->getMessage());
}
