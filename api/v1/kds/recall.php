<?php
/**
 * SMARTRESTA API Endpoint: Ticket Recall Action
 * POST /api/v1/kds/recall.php
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
    Auth::requirePermission('kds.recall');
    $userId = $_SESSION['user_id'] ?? 1;
    $data = json_decode(file_get_contents('php://input'), true);

    $ticketId = (int)($data['ticket_id'] ?? 0);
    $reason = trim($data['reason'] ?? '');

    if (!$ticketId) {
        Response::json(false, 400, "ticket_id is required.");
        exit;
    }

    $result = KDSEngine::recallTicket($ticketId, $userId, $reason);
    Response::json(true, 200, "Ticket recalled back to {$result['status']} successfully", $result);

} catch (Exception $e) {
    $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 400;
    Response::json(false, $code, "Failed to recall ticket: " . $e->getMessage());
}
