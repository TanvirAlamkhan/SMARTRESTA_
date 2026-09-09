<?php
/**
 * SMARTRESTA API Endpoint: Finance Summary & Overview KPIs
 * GET /api/v1/finance/summary.php
 */

require_once __DIR__ . '/../../config/env.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/FinanceEngine.php';
require_once __DIR__ . '/../../helpers/response.php';

Auth::requireAuth();

try {
    Auth::requirePermission('finance.view');

    $branchId = isset($_GET['branch_id']) ? (int)$_GET['branch_id'] : 1;
    $date = isset($_GET['date']) ? trim($_GET['date']) : null;

    $summary = FinanceEngine::getFinanceSummary($branchId, $date);
    sendJsonResponse(true, 200, "Finance summary retrieved", $summary);

} catch (Exception $e) {
    sendJsonResponse(false, 500, "Failed to retrieve finance summary: " . $e->getMessage());
}
