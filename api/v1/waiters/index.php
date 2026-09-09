<?php
/**
 * SMARTRESTA API Endpoint: Waiters List & Profile Management
 * GET /api/v1/waiters/index.php
 * POST /api/v1/waiters/index.php
 */

require_once __DIR__ . '/../../../config/env.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/WaiterEngine.php';
require_once __DIR__ . '/../../../core/Response.php';

Auth::requireAuth();

$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        Auth::requirePermission('waiters.view');
        $branchId = isset($_GET['branch_id']) ? (int)$_GET['branch_id'] : 1;
        $status = !empty($_GET['status']) ? trim($_GET['status']) : null;

        $waiters = WaiterEngine::getWaitersList($branchId, $status);
        Response::json(true, 200, "Waiters list retrieved successfully", $waiters);

    } else if ($method === 'POST') {
        Auth::requirePermission('waiters.manage');
        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;

        $userId = (int)($data['user_id'] ?? 0);
        $branchId = (int)($data['branch_id'] ?? 1);

        if (!$userId) {
            Response::json(false, 400, "User ID is required.");
        }

        $profile = WaiterEngine::getOrCreateProfile($userId, $branchId);
        Response::json(true, 200, "Waiter profile configured successfully", $profile);

    } else {
        Response::json(false, 405, "Method not allowed.");
    }
} catch (Exception $e) {
    Response::json(false, 400, $e->getMessage());
}
