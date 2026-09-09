<?php
/**
 * SMARTRESTA API Endpoint: Waiter Performance & Commission Matrix
 * GET /api/v1/waiters/get_matrix.php
 *
 * 100% Server-Authoritative Database-Backed Aggregations.
 */

require_once __DIR__ . '/../../../config/env.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/WaiterEngine.php';
require_once __DIR__ . '/../../../core/Response.php';

Auth::requireAuth();

try {
    Auth::requirePermissionOrEmpty('waiters.view');
    $branchId = isset($_GET['branch_id']) ? (int)$_GET['branch_id'] : 1;
    $startDate = !empty($_GET['start_date']) ? trim($_GET['start_date']) : null;
    $endDate = !empty($_GET['end_date']) ? trim($_GET['end_date']) : null;

    $matrix = WaiterEngine::getPerformanceMatrix($branchId, $startDate, $endDate);
    Response::json(true, 200, "Waiter performance matrix retrieved", $matrix);

} catch (Exception $e) {
    Response::json(false, 500, "Failed to retrieve waiter matrix: " . $e->getMessage());
}
