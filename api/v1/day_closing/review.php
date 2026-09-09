<?php
/**
 * SMARTRESTA API Endpoint: Day Closing Pre-Check Audit
 * GET /api/v1/day_closing/review.php
 */

require_once __DIR__ . '/../../config/env.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/FinanceEngine.php';
require_once __DIR__ . '/../../helpers/response.php';

Auth::requireAuth();

try {
    Auth::requirePermission('day_closing.review');

    $branchId = isset($_GET['branch_id']) ? (int)$_GET['branch_id'] : 1;
    $date = isset($_GET['business_date']) ? trim($_GET['business_date']) : null;

    $review = FinanceEngine::getDayClosingReview($branchId, $date);
    sendJsonResponse(true, 200, "Day closing review audit retrieved", $review);

} catch (Exception $e) {
    sendJsonResponse(false, 500, "Failed to retrieve day closing review: " . $e->getMessage());
}
