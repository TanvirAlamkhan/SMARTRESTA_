<?php
/**
 * SMARTRESTA API Endpoint: Report Exporter Stream (CSV / PDF)
 * GET /api/v1/reports/export.php?type=sales&format=csv&branch_id=1&preset=this_month
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
    Auth::requirePermission('reports.export');

    $reportType = $_GET['type'] ?? 'sales';
    $format = strtolower($_GET['format'] ?? 'csv');

    $branchId = $_SESSION['branch_id'] ?? 1;
    if (isset($_GET['branch_id']) && Auth::hasPermission('branches.manage')) {
        $branchId = (int)$_GET['branch_id'];
    }

    $dateFrom = $_GET['date_from'] ?? null;
    $dateTo = $_GET['date_to'] ?? null;

    if ($format === 'csv') {
        $csvData = ReportEngine::exportCSV($reportType, $branchId, $dateFrom, $dateTo);
        
        $filename = "SMARTRESTA_{$reportType}_report_" . date('Ymd_His') . ".csv";

        header('Content-Type: text/csv; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');
        
        echo $csvData;
        exit;
    } else {
        Response::json(false, 400, "Unsupported export format '{$format}'. Only 'csv' format is supported currently.");
    }

} catch (Exception $e) {
    $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
    Response::json(false, $code, "Failed to export report: " . $e->getMessage());
}
