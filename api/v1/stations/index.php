<?php
/**
 * SMARTRESTA API Endpoint: Get Operational Stations
 * GET /api/v1/stations/index.php
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/RoutingEngine.php';
require_once __DIR__ . '/../../../core/Response.php';

Auth::requireAuth();

try {
    $branchId = $_SESSION['branch_id'] ?? 1;
    $stations = RoutingEngine::getStations($branchId);
    Response::json(true, 200, "Operational stations retrieved successfully", $stations);
} catch (Exception $e) {
    Response::json(false, 500, "Failed to load stations: " . $e->getMessage());
}
