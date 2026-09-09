<?php
/**
 * SMARTRESTA Automated CLI Database Migration Runner
 * Usage: php bin/migrate.php
 * Executes migrations 001 through 024 in order idempotently.
 */

require_once __DIR__ . '/../api/config/database.php';

echo "========================================================================\n";
echo "SMARTRESTA — AUTOMATED DATABASE MIGRATION RUNNER\n";
echo "========================================================================\n";

$db = Database::getConnection();

if (!$db) {
    die("FATAL ERROR: Unable to establish database connection. Check environment variables.\n");
}

try {
    // 1. Ensure schema_migrations Tracking Table Exists
    $db->exec("
        CREATE TABLE IF NOT EXISTS `schema_migrations` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `migration` VARCHAR(255) NOT NULL UNIQUE,
            `executed_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    // Get List of Already Executed Migrations
    $stmt = $db->query("SELECT `migration` FROM `schema_migrations`");
    $executed = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // 2. Discover Migration Files in Order
    $migrationsDir = __DIR__ . '/../database/migrations';
    $files = glob($migrationsDir . '/*.sql');
    sort($files);

    if (empty($files)) {
        die("No migration files found in database/migrations/\n");
    }

    $appliedCount = 0;

    foreach ($files as $file) {
        $filename = basename($file);
        
        if (in_array($filename, $executed)) {
            echo "[SKIPPED] $filename (Already Applied)\n";
            continue;
        }

        echo "[RUNNING] $filename ... ";
        $sql = file_get_contents($file);

        if (empty(trim($sql))) {
            echo "EMPTY (Skipping)\n";
            continue;
        }

        // Clean DELIMITER lines for PDO compatibility
        $sqlCleaned = preg_replace('/DELIMITER\s+\/\//i', '', $sql);
        $sqlCleaned = preg_replace('/DELIMITER\s+;/i', '', $sqlCleaned);
        $sqlCleaned = str_replace('//', ';', $sqlCleaned);

        // Enable multi query execution mode
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
        $db->exec($sqlCleaned);
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        // Record Migration as Executed
        $ins = $db->prepare("INSERT INTO `schema_migrations` (`migration`) VALUES (?)");
        $ins->execute([$filename]);

        echo "PASSED!\n";
        $appliedCount++;
    }

    echo "========================================================================\n";
    echo "MIGRATION COMPLETE! $appliedCount new migrations applied successfully.\n";
    echo "========================================================================\n";

} catch (Exception $e) {
    echo "MIGRATION FAILED: " . $e->getMessage() . "\n";
    exit(1);
}
