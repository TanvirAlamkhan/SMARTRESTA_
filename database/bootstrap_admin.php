<?php
/**
 * SMARTRESTA Safe Admin User Initialization Bootstrap Script
 * Creates or resets the initial System Administrator account with Bcrypt password hashing.
 * 
 * Usage: Execute via CLI or web browser during setup.
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/Auth.php';

$db = Database::getConnection();

if (!$db) {
    die("Database connection failed. Please verify .env or MySQL configuration.\n");
}

try {
    // 1. Ensure Admin Role Exists
    $roleRow = DB::fetch("SELECT id FROM roles WHERE name = 'admin'");
    if (!$roleRow) {
        $roleId = DB::insert("INSERT INTO roles (name, description) VALUES ('admin', 'System Administrator')");
    } else {
        $roleId = $roleRow['id'];
    }

    // 2. Default Admin Account Parameters
    $adminEmail = 'admin@smartresta.com';
    $adminPassword = 'Admin@SMARTRESTA2026!'; // Change on first login
    $passwordHash = Auth::hashPassword($adminPassword);

    $existingAdmin = DB::fetch("SELECT id FROM users WHERE email = ?", [$adminEmail]);

    if ($existingAdmin) {
        DB::execute("
            UPDATE users 
            SET password_hash = ?, role_id = ?, role = 'admin', status = 'ACTIVE', deleted_at = NULL 
            WHERE id = ?
        ", [$passwordHash, $roleId, $existingAdmin['id']]);
        echo "Successfully updated System Admin account ({$adminEmail})\n";
    } else {
        DB::insert("
            INSERT INTO users (role_id, name, email, phone, password_hash, role, status)
            VALUES (?, 'System Administrator', ?, '01700000000', ?, 'admin', 'ACTIVE')
        ", [$roleId, $adminEmail, $passwordHash]);
        echo "Successfully created System Admin account ({$adminEmail})\n";
    }

    // 3. Populate Default Permissions to Role
    $permissions = DB::fetchAll("SELECT id FROM permissions");
    foreach ($permissions as $perm) {
        DB::execute("
            INSERT IGNORE INTO role_permissions (role_id, permission_id) 
            VALUES (?, ?)
        ", [$roleId, $perm['id']]);
    }

    echo "Default permissions successfully mapped to Admin role.\n";
    echo "Default Password: {$adminPassword}\n";
} catch (Exception $e) {
    die("Bootstrap Admin Error: " . $e->getMessage() . "\n");
}
