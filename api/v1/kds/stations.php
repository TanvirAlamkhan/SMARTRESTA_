<?php
/**
 * SMARTRESTA API Endpoint: Operational Stations List & Management
 * GET  /api/v1/kds/stations.php
 * POST /api/v1/kds/stations.php
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/KDSEngine.php';
require_once __DIR__ . '/../../../core/RoutingEngine.php';
require_once __DIR__ . '/../../../core/Response.php';

Auth::requireAuth();

$method = $_SERVER['REQUEST_METHOD'];
$branchId = $_SESSION['branch_id'] ?? 1;
$userId = $_SESSION['user_id'] ?? 1;

if ($method === 'GET') {
    try {
        Auth::requirePermission('stations.view');
        $stations = RoutingEngine::getStations($branchId);

        // Attach live ticket counters to each station
        foreach ($stations as &$st) {
            $st['counters'] = KDSEngine::getKDSHeaderCounters($branchId, (int)$st['id']);
        }

        Response::json(true, 200, "Operational stations retrieved successfully", $stations);
    } catch (Exception $e) {
        Response::json(false, 500, "Failed to load stations: " . $e->getMessage());
    }
} else if ($method === 'POST') {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        $action = $data['action'] ?? 'create';

        if (in_array($action, ['pause', 'resume'])) {
            Auth::requirePermission('stations.pause');
        } else {
            Auth::requirePermission('stations.manage');
        }

        $result = KDSEngine::manageStation($data, $userId);
        Response::json(true, 200, "Station state updated successfully", $result);

    } catch (Exception $e) {
        Response::json(false, 400, "Failed to manage station: " . $e->getMessage());
    }
} else {
    Response::json(false, 405, "Method not allowed");
}
