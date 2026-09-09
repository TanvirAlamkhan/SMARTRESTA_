<?php
/**
 * SMARTRESTA Master Database Importer
 * Usage in Railway Console: php bin/import_master.php
 * Executes database/railway_deploy_master.sql directly into Railway MySQL.
 */

require_once __DIR__ . '/../config/database.php';

echo "========================================================================\n";
echo "SMARTRESTA — MASTER DATABASE IMPORT RUNNER\n";
echo "========================================================================\n";

$db = Database::getConnection();

if (!$db) {
    die("FATAL ERROR: Unable to establish database connection. Check Railway environment variables.\n");
}

try {
    $sqlFile = __DIR__ . '/../database/railway_deploy_master.sql';
    if (!file_exists($sqlFile)) {
        die("FATAL ERROR: Master SQL file not found at database/railway_deploy_master.sql\n");
    }

    echo "Reading master SQL dump file...\n";
    $sql = file_get_contents($sqlFile);

    echo "Executing master SQL schema & seed import...\n";
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
    $db->exec("SET FOREIGN_KEY_CHECKS = 0;");
    $db->exec($sql);
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    // Guarantee default staff account passwords match Admin@SMARTRESTA2026!
    require_once __DIR__ . '/../core/Auth.php';
    $validHash = Auth::hashPassword('Admin@SMARTRESTA2026!');
    $db->exec("UPDATE users SET password_hash = '{$validHash}' WHERE email IN ('admin@smartresta.com', 'manager@smartresta.com', 'reception@smartresta.com', 'waiter@smartresta.com', 'kitchen@smartresta.com');");

    echo "========================================================================\n";
    echo "SUCCESS: Master database imported cleanly into Railway MySQL!\n";
    echo "Verified Admin Password: Admin@SMARTRESTA2026!\n";
    echo "========================================================================\n";

} catch (Exception $e) {
    echo "IMPORT ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
