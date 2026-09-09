<?php
/**
 * SMARTRESTA Role & Permission Matrix API Endpoint
 * GET /api/v1/roles/index.php
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../config/permissions.php';
require_once __DIR__ . '/../../../core/Response.php';

Auth::requirePermission('roles.view');

$db = Database::getConnection();

if ($db) {
    try {
        $roles = DB::fetchAll("SELECT id, name, description FROM roles");
        $matrix = [];

        foreach ($roles as $r) {
            $perms = DB::fetchAll("
                SELECT p.name, p.module, p.description 
                FROM permissions p
                JOIN role_permissions rp ON p.id = rp.permission_id
                WHERE rp.role_id = ?
            ", [$r['id']]);

            $matrix[] = [
                'role_id' => $r['id'],
                'role_name' => $r['name'],
                'description' => $r['description'],
                'permissions' => array_column($perms, 'name')
            ];
        }

        Response::json(true, 200, "Role permission matrix retrieved", [
            'catalog' => PERMISSIONS_CATALOG,
            'matrix' => $matrix
        ]);
    } catch (Exception $e) {
        Response::json(false, 500, "Database error: " . $e->getMessage());
    }
} else {
    // Dynamic Fallback Matrix
    $matrix = [
        ['role_name' => 'admin', 'permissions' => array_keys(PERMISSIONS_CATALOG)],
        ['role_name' => 'manager', 'permissions' => ROLE_PERMISSIONS_DEFAULT['manager']],
        ['role_name' => 'reception', 'permissions' => ROLE_PERMISSIONS_DEFAULT['reception']],
        ['role_name' => 'waiter', 'permissions' => ROLE_PERMISSIONS_DEFAULT['waiter']],
        ['role_name' => 'kitchen', 'permissions' => ROLE_PERMISSIONS_DEFAULT['kitchen']]
    ];
    Response::json(true, 200, "Role permission matrix retrieved (fallback mode)", [
        'catalog' => PERMISSIONS_CATALOG,
        'matrix' => $matrix
    ]);
}
