<?php
/**
 * SMARTRESTA Atomic Table Transfer API Endpoint
 * POST /api/v1/dining_sessions/transfer.php
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/Validation.php';
require_once __DIR__ . '/../../../core/DiningSessionEngine.php';
require_once __DIR__ . '/../../../core/Response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::json(false, 405, "Method Not Allowed");
}

Auth::requirePermission('dining_sessions.transfer');

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

$validator = new Validation();
$validator->require($input, 'session_id', 'Dining Session ID')
          ->require($input, 'destination_table_id', 'Destination Table');

if (!$validator->isValid()) {
    Response::json(false, 422, "Validation failed", null, $validator->getErrors());
}

$sessionId = (int)$input['session_id'];
$destinationTableId = (int)$input['destination_table_id'];
$userId = Auth::userId() ?: 1;

try {
    $transfer = DiningSessionEngine::transferSession($sessionId, $destinationTableId, $userId);
    Response::json(true, 200, "Session successfully transferred from Table {$transfer['from_table']} to Table {$transfer['to_table']}", $transfer);
} catch (Exception $e) {
    Response::json(false, 422, $e->getMessage());
}
