<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/Auth.php';

$user = Auth::login('admin@smartresta.com', 'Admin@SMARTRESTA2026!');
if ($user) {
    echo "LOGIN SUCCESSFUL!\n";
    echo "User ID: " . $user['id'] . "\n";
    echo "Name: " . $user['name'] . "\n";
    echo "Email: " . $user['email'] . "\n";
    echo "Role: " . ($user['role_name'] ?: $user['role']) . "\n";
} else {
    echo "LOGIN FAILED!\n";
}
