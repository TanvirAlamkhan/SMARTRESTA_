<?php
/**
 * SMARTRESTA API Endpoint: Ticket Cancel Action
 * POST /api/v1/kds/cancel.php
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
    Auth::requirePermission('kds.cancel');
    $userId = $_SESSION['user_id'] ?? 1;
    $data = json_decode(file_get_contents('php://input'), true);

    $ticketId = (int)($data['ticket_id'] ?? 0);
    $reason = trim($data['reason'] ?? '');

    if (!$ticketId || !$reason) {
        Response::json(false, 400, "ticket_id and cancellation reason are required.");
        exit;
    }

    $result = KDSEngine::cancelTicket($ticketId, $userId, $reason);
    Response::json(true, 200, "Ticket cancelled successfully", $result);

} catch (Exception $e) {
    $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 400;
    Response::json(false, $code, "Failed to cancel ticket: " . $e->getMessage());
}
