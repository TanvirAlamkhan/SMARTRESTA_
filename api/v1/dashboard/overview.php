<?php
/**
 * SMARTRESTA API Endpoint: Dashboard Overview & Real-Time Alerts
 * GET /api/v1/dashboard/overview.php?branch_id=1&preset=today&date_from=2026-09-01&date_to=2026-09-09
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
    Auth::requirePermission('dashboard.view');

    $branchId = $_SESSION['branch_id'] ?? 1;
    if (isset($_GET['branch_id']) && Auth::hasPermission('branches.manage')) {
        $branchId = (int)$_GET['branch_id'];
    }

    $dateFrom = $_GET['date_from'] ?? null;
    $dateTo = $_GET['date_to'] ?? null;
    $preset = $_GET['preset'] ?? 'today';

    $overview = ReportEngine::getDashboardOverview($branchId, $dateFrom, $dateTo, $preset);

    Response::json(true, 200, "Dashboard overview retrieved successfully", $overview);

} catch (Exception $e) {
    $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
    Response::json(false, $code, "Failed to load dashboard overview: " . $e->getMessage());
}
