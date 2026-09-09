<?php
/**
 * SMARTRESTA Production Database Backup & Export Utility
 * Generates timestamped database SQL dumps for disaster recovery.
 */

require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/Logger.php';

echo "========================================================\n";
echo "SMARTRESTA PRODUCTION DATABASE BACKUP UTILITY\n";
echo "========================================================\n\n";

try {
    $db = Database::getConnection();
    if (!$db) {
        throw new Exception("Unable to connect to database for backup.");
    }

    $storageDir = dirname(__DIR__) . '/storage/backups';
    if (!is_dir($storageDir)) {
        @mkdir($storageDir, 0755, true);
    }

    $timestamp = date('Ymd_His');
    $backupFile = "{$storageDir}/smartresta_backup_{$timestamp}.sql";

    echo "Starting backup export to: {$backupFile}\n";

    // Fetch all tables
    $stmt = $db->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $sqlDump = "-- SMARTRESTA Production Database Backup\n";
    $sqlDump .= "-- Export Date: " . date('Y-m-d H:i:s') . "\n\n";
    $sqlDump .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

    foreach ($tables as $table) {
        // Table structure
        $stmtCreate = $db->query("SHOW CREATE TABLE `{$table}`");
        $createRow = $stmtCreate->fetch(PDO::FETCH_NUM);
        $sqlDump .= "-- Table structure for `{$table}`\n";
        $sqlDump .= "DROP TABLE IF EXISTS `{$table}`;\n";
        $sqlDump .= $createRow[1] . ";\n\n";

        // Table data
        $stmtRows = $db->query("SELECT * FROM `{$table}`");
        $rows = $stmtRows->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($rows)) {
            $sqlDump .= "-- Dumping data for `{$table}`\n";
            foreach ($rows as $row) {
                $cols = array_keys($row);
                $escapedCols = array_map(function($c) { return "`{$c}`"; }, $cols);

                $escapedValues = array_map(function($v) use ($db) {
                    if ($v === null) return 'NULL';
                    return $db->quote($v);
                }, array_values($row));

                $sqlDump .= "INSERT INTO `{$table}` (" . implode(', ', $escapedCols) . ") VALUES (" . implode(', ', $escapedValues) . ");\n";
            }
            $sqlDump .= "\n";
        }
    }

    $sqlDump .= "SET FOREIGN_KEY_CHECKS = 1;\n";

    $bytesWritten = file_put_contents($backupFile, $sqlDump, LOCK_EX);

    if ($bytesWritten === false || $bytesWritten === 0) {
        throw new Exception("Failed to write backup dump to file.");
    }

    $fileSizeKb = round($bytesWritten / 1024, 2);
    echo "✓ Backup completed successfully! Backup Size: {$fileSizeKb} KB\n";
    echo "File location: {$backupFile}\n";

    Logger::info("Database backup created successfully", ['file' => basename($backupFile), 'size_kb' => $fileSizeKb]);

    exit(0);

} catch (Exception $e) {
    echo "❌ BACKUP FAILED: " . $e->getMessage() . "\n";
    Logger::error("Database backup failed: " . $e->getMessage());
    exit(1);
}
