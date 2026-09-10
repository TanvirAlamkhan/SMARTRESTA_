<?php
echo "Testing localhost...\n";
try {
    $pdo1 = new PDO("mysql:host=localhost;port=3306;charset=utf8mb4", "root", "");
    echo "SUCCESS: Connected via localhost\n";
} catch (Exception $e) {
    echo "FAILED localhost: " . $e->getMessage() . "\n";
}

echo "Testing 127.0.0.1...\n";
try {
    $pdo2 = new PDO("mysql:host=127.0.0.1;port=3306;charset=utf8mb4", "root", "");
    echo "SUCCESS: Connected via 127.0.0.1\n";
} catch (Exception $e) {
    echo "FAILED 127.0.0.1: " . $e->getMessage() . "\n";
}
