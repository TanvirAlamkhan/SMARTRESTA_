<?php
/**
 * SMARTRESTA API Endpoint: Order Drill-Down Detail Viewer
 * GET /api/v1/reports/orders_drilldown.php?order_id=12&branch_id=1
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

    $orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
    if (!$orderId) {
        Response::json(false, 400, "order_id query parameter is required.");
        exit;
    }

    $branchId = $_SESSION['branch_id'] ?? 1;
    if (isset($_GET['branch_id']) && Auth::hasPermission('branches.manage')) {
        $branchId = (int)$_GET['branch_id'];
    }

    $details = ReportEngine::getDrilldownOrderDetails($orderId, $branchId);

    Response::json(true, 200, "Order drill-down details loaded successfully", $details);

} catch (Exception $e) {
    $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 404;
    Response::json(false, $code, "Failed to load order drill-down: " . $e->getMessage());
}
