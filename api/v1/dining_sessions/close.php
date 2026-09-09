<?php
/**
 * SMARTRESTA Dining Session Close API Endpoint
 * POST /api/v1/dining_sessions/close.php
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/Validation.php';
require_once __DIR__ . '/../../../core/DiningSessionEngine.php';
require_once __DIR__ . '/../../../core/Response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::json(false, 405, "Method Not Allowed");
}

Auth::requirePermission('dining_sessions.close');

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

$validator = new Validation();
$validator->require($input, 'session_id', 'Dining Session ID');

if (!$validator->isValid()) {
    Response::json(false, 422, "Validation failed", null, $validator->getErrors());
}

$sessionId = (int)$input['session_id'];
$userId = Auth::userId() ?: 1;

try {
    $result = DiningSessionEngine::closeSession($sessionId, $userId);
    Response::json(true, 200, "Dining session #{$sessionId} successfully closed", $result);
} catch (Exception $e) {
    Response::json(false, 422, $e->getMessage());
}
