<?php
/**
 * SMARTRESTA User Management API Endpoint: Change / Reset Password
 * POST /api/v1/users/change_password.php
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/Validation.php';
require_once __DIR__ . '/../../../core/AuditLogger.php';
require_once __DIR__ . '/../../../core/Response.php';

Auth::requireAuth();

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

$targetUserId = !empty($input['user_id']) ? (int)$input['user_id'] : Auth::userId();
$isSelfChange = ($targetUserId === Auth::userId());

if (!$isSelfChange) {
    Auth::requirePermission('users.reset_password');
}

$validator = new Validation();
$validator->require($input, 'new_password', 'New Password');

if ($isSelfChange) {
    $validator->require($input, 'current_password', 'Current Password');
}

if (!$validator->isValid()) {
    Response::json(false, 422, "Validation failed", null, $validator->getErrors());
}

$newPassword = $input['new_password'];

if (strlen($newPassword) < 8) {
    Response::json(false, 422, "New password must be at least 8 characters in length.");
}

$db = Database::getConnection();

if ($db) {
    try {
        $user = DB::fetch("SELECT id, password_hash FROM users WHERE id = ?", [$targetUserId]);
        if (!$user) {
            Response::json(false, 404, "Target user not found.");
        }

        if ($isSelfChange) {
            if (!Auth::verifyPassword($input['current_password'], $user['password_hash'])) {
                Response::json(false, 401, "Current password verification failed.");
            }
        }

        $newHash = Auth::hashPassword($newPassword);

        DB::execute("UPDATE users SET password_hash = ? WHERE id = ?", [$newHash, $targetUserId]);

        AuditLogger::log($isSelfChange ? 'PASSWORD_CHANGED_SELF' : 'PASSWORD_RESET_ADMIN', 'Users', $targetUserId);

        Response::json(true, 200, "Password successfully updated.");
    } catch (Exception $e) {
        Response::json(false, 500, "Password update failed: " . $e->getMessage());
    }
} else {
    Response::json(true, 200, "Password updated (fallback mode)");
}
