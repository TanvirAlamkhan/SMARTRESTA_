<?php
/**
 * SMARTRESTA Database Connection Handler
 * Proxies to root config/database.php with class_exists safety wrapper.
 */

if (!class_exists('Database')) {
    require_once __DIR__ . '/../../config/database.php';
}

