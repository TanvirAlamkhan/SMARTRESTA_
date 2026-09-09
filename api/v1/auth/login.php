<?php
/**
 * SMARTRESTA Auth API Endpoint: User Login
 * POST /api/v1/auth/login.php
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/CSRF.php';
require_once __DIR__ . '/../../../core/Validation.php';
require_once __DIR__ . '/../../../core/RateLimiter.php';
require_once __DIR__ . '/../../../core/Response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::json(false, 405, "Method Not Allowed");
}

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

$validator = new Validation();
$validator->require($input, 'email', 'Email / Identifier')
          ->require($input, 'password', 'Password');

if (!$validator->isValid()) {
    Response::json(false, 422, "Validation failed", null, $validator->getErrors());
}

$email = trim($input['email']);
$password = $input['password'];

// Rate Limiting Check (Max 5 attempts / 15 mins)
if (!RateLimiter::check($email)) {
    Response::json(false, 429, "Too many failed login attempts. Please try again in 15 minutes.");
}

// Check Database Connectivity
try {
    $db = Database::getConnection();
    if (!$db) {
        Response::json(false, 500, "Database connection unavailable. Please start MySQL service in XAMPP or check database settings.");
    }
} catch (Exception $e) {
    Response::json(false, 500, "Database Connection Error: " . $e->getMessage());
}

// Authenticate via Auth Engine
try {
    $user = Auth::login($email, $password);
} catch (Exception $e) {
    Response::json(false, 500, $e->getMessage());
}

if (!$user) {
    RateLimiter::recordFailedAttempt($email);
    Response::json(false, 401, "Invalid login credentials.");
}

// Clear Rate Limiter on Success
RateLimiter::clear($email);

require_once __DIR__ . '/../../../core/Router.php';

$redirectTarget = '../' . Router::getPortalPath($user['role_name'] ?: $user['role']);

Response::json(true, 200, "Authentication successful", [
    'user' => [
        'id' => $user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'role' => $user['role_name'] ?: $user['role']
    ],
    'redirect' => $redirectTarget
]);
