<?php
/**
 * SMARTRESTA API Endpoint: Ticket Status Audit History
 * GET /api/v1/kds/history.php?ticket_id={tid}
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/KDSEngine.php';
require_once __DIR__ . '/../../../core/Response.php';

Auth::requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    Response::json(false, 405, "Method not allowed");
    exit;
}

try {
    Auth::requirePermission('kds.history.view');
    $ticketId = isset($_GET['ticket_id']) ? (int)$_GET['ticket_id'] : 0;

    if (!$ticketId) {
        Response::json(false, 400, "ticket_id is required.");
        exit;
    }

    $data = KDSEngine::getTicketHistory($ticketId);
    Response::json(true, 200, "Ticket history retrieved successfully", $data);

} catch (Exception $e) {
    Response::json(false, 500, "Failed to load ticket history: " . $e->getMessage());
}
