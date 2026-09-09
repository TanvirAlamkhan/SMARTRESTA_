<?php
/**
 * SMARTRESTA Auth API Endpoint: Get Current User Identity & Permissions
 * GET /api/v1/auth/me.php
 */

require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/Response.php';

Auth::requireAuth();

$user = Auth::user();
$permissions = $_SESSION['permissions'] ?? [];

Response::json(true, 200, "Authenticated user identity retrieved", [
    'user' => $user,
    'permissions' => $permissions
]);
