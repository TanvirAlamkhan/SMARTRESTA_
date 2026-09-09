<?php
/**
 * SMARTRESTA API Endpoint: Active KDS Tickets Queue & Status Transitions
 * GET  /api/v1/kds/tickets.php?station_id={sid}&status={st}&priority={prio}
 * POST /api/v1/kds/tickets.php
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/KDSEngine.php';
require_once __DIR__ . '/../../../core/Response.php';

Auth::requireAuth();

$method = $_SERVER['REQUEST_METHOD'];
$branchId = $_SESSION['branch_id'] ?? 1;
$userId = $_SESSION['user_id'] ?? 1;

if ($method === 'GET') {
    try {
        Auth::requirePermissionOrEmpty('kds.view');
        $stationId = isset($_GET['station_id']) ? (int)$_GET['station_id'] : null;
        $status = $_GET['status'] ?? '';
        $priority = $_GET['priority'] ?? '';

        $tickets = KDSEngine::getStationQueue($branchId, $stationId, $status, $priority);
        $counters = KDSEngine::getKDSHeaderCounters($branchId, $stationId);

        Response::json(true, 200, "KDS station tickets queue retrieved successfully", [
            'tickets' => $tickets,
            'counters' => $counters
        ]);
    } catch (Exception $e) {
        $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
        Response::json(false, $code, "Failed to load KDS tickets: " . $e->getMessage());
    }
} else if ($method === 'POST') {
    try {
        Auth::requirePermission('kds.manage');
        $data = json_decode(file_get_contents('php://input'), true);
        $ticketId = (int)($data['ticket_id'] ?? 0);
        $status = strtoupper(trim($data['status'] ?? ''));
        $reason = trim($data['reason'] ?? '');

        if (!$ticketId || !$status) {
            Response::json(false, 400, "ticket_id and status are required.");
            exit;
        }

        // Validate state-specific permissions
        if ($status === 'PREPARING') Auth::requirePermission('kds.start');
        if ($status === 'READY') Auth::requirePermission('kds.ready');
        if ($status === 'SERVED') Auth::requirePermission('kds.served');

        $result = KDSEngine::updateTicketStatus($ticketId, $status, $userId, $reason);
        Response::json(true, 200, "Ticket status updated to {$status}", $result);

    } catch (Exception $e) {
        $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 400;
        Response::json(false, $code, "Failed to update ticket status: " . $e->getMessage());
    }
} else {
    Response::json(false, 405, "Method not allowed");
}
