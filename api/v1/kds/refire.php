<?php
/**
 * SMARTRESTA API Endpoint: Ticket Re-Fire Action
 * POST /api/v1/kds/refire.php
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/KDSEngine.php';
require_once __DIR__ . '/../../../core/Response.php';

Auth::requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::json(false, 405, "Method not allowed");
    exit;
}

try {
    Auth::requirePermission('kds.refire');
    $userId = $_SESSION['user_id'] ?? 1;
    $data = json_decode(file_get_contents('php://input'), true);

    $ticketId = (int)($data['ticket_id'] ?? 0);
    $reason = trim($data['reason'] ?? '');

    if (!$ticketId || !$reason) {
        Response::json(false, 400, "ticket_id and reason are required for ticket re-fire.");
        exit;
    }

    $result = KDSEngine::refireTicket($ticketId, $userId, $reason);
    Response::json(true, 200, "Ticket re-fired successfully. New ticket #{$result['refire_ticket_number']} dispatched.", $result);

} catch (Exception $e) {
    $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 400;
    Response::json(false, $code, "Failed to re-fire ticket: " . $e->getMessage());
}
