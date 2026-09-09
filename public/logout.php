<?php
/**
 * Direct GET/POST Logout Endpoint
 * Clears PHP session and redirects to public/login.php
 */
require_once __DIR__ . '/../core/Auth.php';

Auth::logout();
header('Location: login.php');
exit;
