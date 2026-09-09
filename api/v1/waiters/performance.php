<?php
/**
 * SMARTRESTA API Endpoint: Detailed Waiter Performance & History Drill-down
 * GET /api/v1/waiters/performance.php?waiter_id=X
 */

require_once __DIR__ . '/../../config/env.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/WaiterEngine.php';
require_once __DIR__ . '/../../helpers/response.php';

Auth::requireAuth();

try {
    Auth::requirePermission('waiters.view');
    $waiterId = isset($_GET['waiter_id']) ? (int)$_GET['waiter_id'] : 0;

    if (!$waiterId) {
        sendJsonResponse(false, 400, "Waiter ID is required.");
        exit;
    }

    $details = WaiterEngine::getWaiterDetails($waiterId);
    sendJsonResponse(true, 200, "Waiter performance details retrieved", $details);

} catch (Exception $e) {
    sendJsonResponse(false, 400, $e->getMessage());
}
