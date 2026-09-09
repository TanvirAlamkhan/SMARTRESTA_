<?php
/**
 * SMARTRESTA Production Authentication & RBAC Engine
 * Handles user authentication, Bcrypt password hashing, account status checks,
 * session security, and granular DB-backed permission enforcement.
 */

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Response.php';
require_once __DIR__ . '/AuditLogger.php';
require_once __DIR__ . '/../config/permissions.php';

class Auth {
    public static function initSession() {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_httponly', 1);
            ini_set('session.use_only_cookies', 1);
            ini_set('session.cookie_samesite', 'Lax');

            $isSecure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
            if ($isSecure || (getenv('SESSION_SECURE') === 'true')) {
                ini_set('session.cookie_secure', 1);
            }

            session_start();
        }

        // Enforce Session Inactivity Timeout (2 Hours = 7200 seconds)
        if (!empty($_SESSION['authenticated'])) {
            $maxInactivity = 7200;
            if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $maxInactivity)) {
                self::logout();
                return;
            }
            $_SESSION['last_activity'] = time();
        }
    }

    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    public static function login($email, $password) {
        self::initSession();

        try {
            $user = DB::fetch("
                SELECT u.id, u.name, u.email, u.password_hash, u.role, u.status, u.role_id,
                       r.name as role_name
                FROM users u
                LEFT JOIN roles r ON u.role_id = r.id
                WHERE u.email = ? AND u.deleted_at IS NULL
            ", [trim($email)]);
        } catch (Exception $e) {
            error_log("Auth Login DB Exception: " . $e->getMessage());
            return false;
        }

        if (!$user) {
            return false;
        }

        // Enforce Account Status Check (Only ACTIVE users allowed)
        if (strtoupper($user['status']) !== 'ACTIVE') {
            AuditLogger::log('LOGIN_DENIED_INACTIVE', 'Auth', $user['id'], null, ['status' => $user['status']], $user['id']);
            return false;
        }

        // Verify Bcrypt Password
        if (!self::verifyPassword($password, $user['password_hash'])) {
            AuditLogger::log('LOGIN_FAILED_BAD_PASSWORD', 'Auth', $user['id'], null, null, $user['id']);
            return false;
        }

        // Regenerate Session ID to prevent session fixation
        session_regenerate_id(true);

        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role_name'] ?: $user['role'];
        $_SESSION['role_id'] = (int)$user['role_id'];
        $_SESSION['authenticated'] = true;
        $_SESSION['login_time'] = time();

        // Load permissions into session
        self::loadPermissions((int)$user['role_id'], $user['role_name'] ?: $user['role']);

        // Update Last Login Timestamp
        DB::execute("UPDATE users SET last_login = NOW() WHERE id = ?", [$user['id']]);

        // Record Audit Event
        AuditLogger::log('LOGIN_SUCCESS', 'Auth', $user['id'], null, ['ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'], $user['id']);

        return $user;
    }

    public static function loadPermissions($roleId, $roleName) {
        $permissions = [];
        try {
            $rows = DB::fetchAll("
                SELECT p.name 
                FROM permissions p
                JOIN role_permissions rp ON p.id = rp.permission_id
                WHERE rp.role_id = ?
            ", [$roleId]);

            $permissions = array_column($rows, 'name');
        } catch (Exception $e) {
            // Fallback to default catalog mapping if DB mappings empty
            $permissions = ROLE_PERMISSIONS_DEFAULT[$roleName] ?? [];
        }

        $_SESSION['permissions'] = $permissions;
    }

    public static function check() {
        self::initSession();
        return !empty($_SESSION['authenticated']) && !empty($_SESSION['user_id']);
    }

    public static function user() {
        self::initSession();
        if (!self::check()) return null;

        return [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'],
            'email' => $_SESSION['user_email'],
            'role' => $_SESSION['user_role']
        ];
    }

    public static function userId() {
        self::initSession();
        return $_SESSION['user_id'] ?? null;
    }

    public static function role() {
        self::initSession();
        return $_SESSION['user_role'] ?? null;
    }

    public static function hasPermission($permissionName) {
        self::initSession();
        if (!self::check()) return false;

        // Admin has full system access
        if ($_SESSION['user_role'] === 'admin') return true;

        $userPermissions = $_SESSION['permissions'] ?? [];
        return in_array($permissionName, $userPermissions, true);
    }

    public static function requireAuth() {
        self::initSession();
        if (!self::check()) {
            if (self::isAjax()) {
                Response::json(false, 401, "Authentication required. Please log in.");
            } else {
                header("Location: landing.php");
                exit;
            }
        }
    }

    public static function requirePermission($permissionName) {
        self::requireAuth();
        if (!self::hasPermission($permissionName)) {
            AuditLogger::log('AUTHORIZATION_DENIED', 'Auth', null, null, ['required_permission' => $permissionName]);
            if (self::isAjax()) {
                Response::json(false, 403, "Forbidden: You do not have permission [{$permissionName}] to perform this action.");
            } else {
                http_response_code(403);
                echo "<h1 style='font-family:sans-serif; text-align:center; margin-top:100px; color:#EF4444;'>403 Forbidden: Insufficient Permissions</h1>";
                exit;
            }
        }
    }

    public static function logout() {
        self::initSession();
        if (isset($_SESSION['user_id'])) {
            AuditLogger::log('LOGOUT', 'Auth', $_SESSION['user_id']);
        }
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }

    private static function isAjax() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}
