<?php
require_once __DIR__ . '/../config/database.php';
$stmt = Database::getConnection()->query('SELECT * FROM payment_methods');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
