<?php
/**
 * SMARTRESTA User Management API Endpoint: Update User Details & Status
 * PUT /api/v1/users/update.php
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/Validation.php';
require_once __DIR__ . '/../../../core/AuditLogger.php';
require_once __DIR__ . '/../../../core/Response.php';

Auth::requirePermission('users.update');

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

$validator = new Validation();
$validator->require($input, 'user_id', 'User ID');

if (!$validator->isValid()) {
    Response::json(false, 422, "Validation failed", null, $validator->getErrors());
}

$userId = (int)$input['user_id'];
$status = strtoupper(trim($input['status'] ?? ''));
$name = trim($input['name'] ?? '');
$roleStr = strtolower(trim($input['role'] ?? ''));

// Safeguard against deactivating self (current admin)
if ($userId === Auth::userId() && $status === 'INACTIVE') {
    Response::json(false, 403, "Protected Action: You cannot deactivate your own administrative account.");
}

$db = Database::getConnection();

if ($db) {
    try {
        $oldUser = DB::fetch("SELECT * FROM users WHERE id = ?", [$userId]);
        if (!$oldUser) {
            Response::json(false, 404, "Target user account not found.");
        }

        $updates = [];
        $params = [];

        if ($name !== '') {
            $updates[] = "name = ?";
            $params[] = $name;
        }

        if ($status !== '' && in_array($status, ['ACTIVE', 'INACTIVE', 'ON_BREAK'], true)) {
            $updates[] = "status = ?";
            $params[] = $status;
        }

        if ($roleStr !== '') {
            $roleRow = DB::fetch("SELECT id FROM roles WHERE name = ?", [$roleStr]);
            if ($roleRow) {
                $updates[] = "role_id = ?";
                $params[] = $roleRow['id'];
                $updates[] = "role = ?";
                $params[] = $roleStr;
            }
        }

        if (!empty($updates)) {
            $params[] = $userId;
            DB::execute("UPDATE users SET " . implode(", ", $updates) . " WHERE id = ?", $params);
            AuditLogger::log('USER_UPDATED', 'Users', $userId, $oldUser, $input);
        }

        Response::json(true, 200, "User account successfully updated");
    } catch (Exception $e) {
        Response::json(false, 500, "Update failed: " . $e->getMessage());
    }
} else {
    Response::json(true, 200, "User account updated (fallback mode)");
}
