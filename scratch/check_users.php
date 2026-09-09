<?php
require_once __DIR__ . '/../api/config/database.php';
$db = Database::getConnection();
$stmt = $db->query("SELECT id, email, name, role, status FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($users, JSON_PRETTY_PRINT);
