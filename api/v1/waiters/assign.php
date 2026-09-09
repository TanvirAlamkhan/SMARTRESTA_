<?php
/**
 * SMARTRESTA API Endpoint: Waiter Table & Session Assignment
 * POST /api/v1/waiters/assign.php
 */

require_once __DIR__ . '/../../config/env.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/WaiterEngine.php';
require_once __DIR__ . '/../../helpers/response.php';

Auth::requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(false, 405, "Method not allowed.");
    exit;
}

try {
    Auth::requirePermission('waiters.assign');
    $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $user = Auth::getCurrentUser();

    $res = WaiterEngine::assignWaiter($data, (int)$user['id']);
    sendJsonResponse(true, 200, "Waiter assigned successfully", $res);

} catch (Exception $e) {
    sendJsonResponse(false, 400, $e->getMessage());
}
