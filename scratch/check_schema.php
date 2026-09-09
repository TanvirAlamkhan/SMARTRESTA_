<?php
require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/../config/database.php';

$db = Database::getConnection();
$tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

echo "DATABASE TABLES:\n" . implode("\n", $tables) . "\n\n";

foreach ($tables as $t) {
    $cols = $db->query("SHOW COLUMNS FROM $t")->fetchAll(PDO::FETCH_COLUMN);
    echo "TABLE [$t]: " . implode(', ', $cols) . "\n";
}
