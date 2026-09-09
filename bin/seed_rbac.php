<?php
/**
 * SMARTRESTA RBAC & Role Permissions Seeder
 * Populates `permissions`, `roles`, and `role_permissions` tables to ensure
 * all staff roles (Admin, Manager, Reception, Waiter, Kitchen) have full database-backed access.
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/permissions.php';

echo "========================================================================\n";
echo "SMARTRESTA — RBAC ROLE PERMISSIONS SEEDER\n";
echo "========================================================================\n";

$db = Database::getConnection();

if (!$db) {
    die("FATAL ERROR: Could not connect to database.\n");
}

try {
    // 1. Seed Permissions Catalog
    echo "1. Seeding permissions catalog (" . count(PERMISSIONS_CATALOG) . " permissions)...\n";
    $stmtPerm = $db->prepare("INSERT INTO permissions (name, module, description) VALUES (:name, :module, :desc) ON DUPLICATE KEY UPDATE module = VALUES(module), description = VALUES(description)");
    foreach (PERMISSIONS_CATALOG as $permName => $permDesc) {
        $module = explode('.', $permName)[0] ?? 'general';
        $stmtPerm->execute(['name' => $permName, 'module' => $module, 'desc' => $permDesc]);
    }

    // 2. Fetch all permission IDs mapped by name
    $permMap = [];
    $rows = $db->query("SELECT id, name FROM permissions")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $r) {
        $permMap[$r['name']] = (int)$r['id'];
    }

    // 3. Ensure Roles exist in `roles` table
    $rolesToEnsure = [
        1 => ['name' => 'System Administrator', 'description' => 'Full System Admin'],
        2 => ['name' => 'Manager', 'description' => 'Restaurant Manager'],
        3 => ['name' => 'Receptionist / Cashier', 'description' => 'Front Desk & Billing Cashier'],
        4 => ['name' => 'Waiter', 'description' => 'Floor Waiter & Order Entry'],
        5 => ['name' => 'Kitchen Staff / Chef', 'description' => 'Kitchen Display System & Station Operator']
    ];

    echo "2. Seeding roles...\n";
    $stmtRole = $db->prepare("INSERT INTO roles (id, name, description) VALUES (:id, :name, :desc) ON DUPLICATE KEY UPDATE name = VALUES(name), description = VALUES(description)");
    foreach ($rolesToEnsure as $id => $roleData) {
        $stmtRole->execute(['id' => $id, 'name' => $roleData['name'], 'desc' => $roleData['description']]);
    }

    // 4. Populate role_permissions mapping for each role
    echo "3. Seeding role_permissions mappings...\n";
    $stmtMap = $db->prepare("INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES (:rid, :pid)");

    $roleKeyMapping = [
        1 => 'admin',
        2 => 'manager',
        3 => 'reception',
        4 => 'waiter',
        5 => 'kitchen'
    ];

    foreach ($roleKeyMapping as $roleId => $key) {
        $allowedPerms = ROLE_PERMISSIONS_DEFAULT[$key] ?? [];
        $insertedCount = 0;
        foreach ($allowedPerms as $permName) {
            if (isset($permMap[$permName])) {
                $stmtMap->execute(['rid' => $roleId, 'pid' => $permMap[$permName]]);
                $insertedCount++;
            }
        }
        echo "   -> Mapped {$insertedCount} permissions for role '{$key}' (Role ID: {$roleId})\n";
    }

    echo "========================================================================\n";
    echo "SUCCESS: All RBAC permissions and role mappings successfully seeded!\n";
    echo "========================================================================\n";

} catch (Exception $e) {
    echo "SEEDING ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
