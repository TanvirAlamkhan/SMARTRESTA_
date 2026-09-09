<?php
/**
 * SMARTRESTA Atomic Dining Session Open API Endpoint
 * POST /api/v1/dining_sessions/open.php
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/Validation.php';
require_once __DIR__ . '/../../../core/DiningSessionEngine.php';
require_once __DIR__ . '/../../../core/Response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::json(false, 405, "Method Not Allowed");
}

Auth::requirePermission('dining_sessions.create');

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

$validator = new Validation();
$validator->require($input, 'table_id', 'Table ID')
          ->numeric($input, 'guest_count', 1, 'Guest Count');

if (!$validator->isValid()) {
    Response::json(false, 422, "Validation failed", null, $validator->getErrors());
}

$tableId = (int)$input['table_id'];
$guestCount = (int)$input['guest_count'];
$customerId = !empty($input['customer_id']) ? (int)$input['customer_id'] : null;
$notes = trim($input['notes'] ?? '');
$userId = Auth::userId() ?: 1;

try {
    $session = DiningSessionEngine::openSession($tableId, $guestCount, $userId, $customerId, $notes);
    Response::json(true, 201, "Dining session successfully opened on Table {$session['table_number']}", $session);
} catch (Exception $e) {
    Response::json(false, 422, $e->getMessage());
}
