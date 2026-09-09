<?php
/**
 * SMARTRESTA Auth API Endpoint: Logout
 * POST /api/v1/auth/logout.php
 */

require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/Response.php';

Auth::logout();
Response::json(true, 200, "Successfully logged out");
