<?php
/**
 * SMARTRESTA Core Portal Router & Access Control Engine
 * Centralizes role-to-portal mapping, server-side route guards,
 * 403 Forbidden error handling, and portal authorization checks.
 */

require_once __DIR__ . '/Auth.php';
require_once __DIR__ . '/Response.php';

class Router {

    /**
     * Map of normalized role key to portal path
     */
    private static $portalMap = [
        'admin'     => 'admin/index.php',
        'manager'   => 'manager/index.php',
        'reception' => 'reception/index.php',
        'waiter'    => 'waiter/index.php',
        'kitchen'   => 'kitchen/index.php',
    ];

    /**
     * Normalize role name into standard role key
     */
    public static function normalizeRole($roleName) {
        $roleKey = strtolower(trim((string)$roleName));
        if (strpos($roleKey, 'admin') !== false) return 'admin';
        if (strpos($roleKey, 'manager') !== false || strpos($roleKey, 'supervisor') !== false) return 'manager';
        if (strpos($roleKey, 'reception') !== false || strpos($roleKey, 'cashier') !== false) return 'reception';
        if (strpos($roleKey, 'waiter') !== false || strpos($roleKey, 'server') !== false) return 'waiter';
        if (strpos($roleKey, 'kitchen') !== false || strpos($roleKey, 'chef') !== false || strpos($roleKey, 'cook') !== false) return 'kitchen';
        return 'waiter';
    }

    /**
     * Get the relative portal URL path for a given role
     */
    public static function getPortalPath($roleName, $prefix = '') {
        $roleKey = self::normalizeRole($roleName);
        $path = self::$portalMap[$roleKey] ?? 'waiter/index.php';
        return $prefix . $path;
    }

    /**
     * Redirect currently authenticated user to their assigned role's portal
     */
    public static function redirectToPortal($prefix = '') {
        Auth::requireAuth();
        $role = Auth::role();
        $portalPath = self::getPortalPath($role, $prefix);
        header("Location: " . $portalPath);
        exit;
    }

    /**
     * Server-side route guard: Authorize current user for specific portal.
     * If user is not authenticated -> redirect to login.
     * If user is authenticated but role is unauthorized -> render HTTP 403 Forbidden page.
     */
    public static function authorizePortal($requiredPortal) {
        Auth::requireAuth();

        $userRole = Auth::role();
        $userRoleKey = self::normalizeRole($userRole);
        $requiredKey = self::normalizeRole($requiredPortal);

        // Define portal access policies:
        // Admin has access to all portals for system management.
        // Other roles are restricted to their assigned portal or operational permissions.
        $allowed = false;
        if ($userRoleKey === 'admin') {
            $allowed = true;
        } elseif ($userRoleKey === $requiredKey) {
            $allowed = true;
        } elseif ($userRoleKey === 'manager' && in_array($requiredKey, ['manager', 'reception', 'waiter', 'kitchen'], true)) {
            $allowed = true;
        }

        if (!$allowed) {
            AuditLogger::log('UNAUTHORIZED_PORTAL_ACCESS', 'Router', Auth::userId(), null, [
                'user_role' => $userRole,
                'requested_portal' => $requiredPortal
            ]);
            self::forbidden("Access Denied: Your role [{$userRole}] is not authorized to access the [{$requiredPortal}] portal.", $userRole);
        }

        return true;
    }

    /**
     * Server-side route guard for specific list of allowed roles
     */
    public static function authorizeRole($allowedRoles = []) {
        Auth::requireAuth();

        $userRole = Auth::role();
        $userRoleKey = self::normalizeRole($userRole);

        if (is_string($allowedRoles)) {
            $allowedRoles = [$allowedRoles];
        }

        $normalizedAllowed = array_map([__CLASS__, 'normalizeRole'], $allowedRoles);

        if (!in_array($userRoleKey, $normalizedAllowed, true) && $userRoleKey !== 'admin') {
            AuditLogger::log('UNAUTHORIZED_ROLE_ACCESS', 'Router', Auth::userId(), null, [
                'user_role' => $userRole,
                'allowed_roles' => $allowedRoles
            ]);
            self::forbidden("Access Denied: Your role [{$userRole}] does not have access to this resource.", $userRole);
        }

        return true;
    }

    /**
     * Render standardized HTTP 403 Forbidden page (or JSON response if API)
     */
    public static function forbidden($message = "403 Forbidden: Access Denied", $userRole = null) {
        http_response_code(403);
        $currentUser = Auth::user();
        $role = $userRole ?: ($currentUser['role'] ?? 'Guest');
        $userPortal = self::getPortalPath($role, '../');

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
            Response::json(false, 403, $message);
            exit;
        }

        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>403 Forbidden — SMARTRESTA</title>
            <style>
                body {
                    font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
                    background-color: #0F172A;
                    color: #F8FAFC;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    min-height: 100vh;
                    margin: 0;
                    padding: 20px;
                }
                .error-card {
                    background: #1E293B;
                    border: 1px solid #334155;
                    border-radius: 12px;
                    padding: 40px;
                    max-width: 480px;
                    width: 100%;
                    text-align: center;
                    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
                }
                .error-badge {
                    font-size: 3rem;
                    margin-bottom: 12px;
                }
                h1 {
                    font-size: 1.5rem;
                    color: #EF4444;
                    margin-bottom: 12px;
                }
                p {
                    color: #94A3B8;
                    font-size: 0.95rem;
                    line-height: 1.5;
                    margin-bottom: 24px;
                }
                .btn {
                    display: inline-block;
                    background-color: #3B82F6;
                    color: #FFFFFF;
                    padding: 12px 24px;
                    border-radius: 8px;
                    text-decoration: none;
                    font-weight: 600;
                    transition: background 0.2s;
                }
                .btn:hover {
                    background-color: #2563EB;
                }
            </style>
        </head>
        <body>
            <div class="error-card">
                <div class="error-badge">🔒</div>
                <h1>403 — Access Forbidden</h1>
                <p><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
                <a href="<?= htmlspecialchars($userPortal, ENT_QUOTES, 'UTF-8') ?>" class="btn">Return to My Portal</a>
            </div>
        </body>
        </html>
        <?php
        exit;
    }

    /**
     * Render standardized 404 Not Found error
     */
    public static function notFound($message = "404 Not Found") {
        http_response_code(404);
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
            Response::json(false, 404, $message);
            exit;
        }
        echo "<h1 style='font-family:sans-serif; text-align:center; margin-top:100px; color:#64748B;'>404 — Page Not Found</h1>";
        exit;
    }
}
