<?php
/**
 * SMARTRESTA API Endpoint: Station Tickets Queue & Status Management
 * GET /api/v1/routing/tickets.php?station_id={sid}&status={status}
 * POST /api/v1/routing/tickets.php
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/RoutingEngine.php';
require_once __DIR__ . '/../../../core/Response.php';

Auth::requireAuth();

$method = $_SERVER['REQUEST_METHOD'];
$branchId = $_SESSION['branch_id'] ?? 1;
$userId = $_SESSION['user_id'] ?? 1;

if ($method === 'GET') {
    try {
        $stationId = isset($_GET['station_id']) ? (int)$_GET['station_id'] : null;
        $status = $_GET['status'] ?? '';
        $tickets = RoutingEngine::getStationQueue($branchId, $stationId, $status);
        Response::json(true, 200, "Station tickets retrieved successfully", $tickets);
    } catch (Exception $e) {
        Response::json(false, 500, "Failed to load station tickets: " . $e->getMessage());
    }
} else if ($method === 'POST') {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        $ticketId = (int)($data['ticket_id'] ?? 0);
        $status = $data['status'] ?? '';

        if (!$ticketId || !$status) {
            Response::json(false, 400, "ticket_id and status are required.");
            exit;
        }

        $res = RoutingEngine::updateTicketStatus($ticketId, $status, $userId);
        Response::json(true, 200, "Ticket status updated successfully", $res);
    } catch (Exception $e) {
        Response::json(false, 400, "Failed to update ticket status: " . $e->getMessage());
    }
} else {
    Response::json(false, 405, "Method not allowed");
}
