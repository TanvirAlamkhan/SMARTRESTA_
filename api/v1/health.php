<?php
/**
 * SMARTRESTA Production Health Probe API Endpoint
 * Supports Liveness (/health.php?type=liveness) and Readiness (/health.php?type=readiness)
 * Compatible with Railway Health Check Probes
 */

require_once __DIR__ . '/../../core/Response.php';
require_once __DIR__ . '/../../core/Database.php';

header('Content-Type: application/json; charset=utf-8');

$type = strtolower($_GET['type'] ?? 'liveness');

if ($type === 'liveness') {
    // Lightweight Liveness Check (Application process running)
    Response::json(true, 200, "SMARTRESTA Application is Live.", [
        'status' => 'UP',
        'service' => 'SMARTRESTA POS',
        'environment' => getenv('APP_ENV') ?: 'production',
        'timestamp' => date('c')
    ]);
} elseif ($type === 'readiness') {
    // Database Readiness Check
    try {
        $db = Database::getConnection();
        if (!$db) {
            Response::json(false, 503, "Database connection unavailable.", ['status' => 'UNAVAILABLE']);
        }

        // Test lightweight query
        $stmt = $db->query("SELECT 1");
        $check = $stmt->fetchColumn();

        if ($check == 1) {
            Response::json(true, 200, "SMARTRESTA Application and Database are Ready.", [
                'status' => 'UP',
                'database' => 'CONNECTED',
                'timestamp' => date('c')
            ]);
        } else {
            Response::json(false, 503, "Database readiness query failed.", ['status' => 'UNHEALTHY']);
        }

    } catch (Exception $e) {
        Logger::error("Readiness probe database failure: " . $e->getMessage());
        Response::json(false, 503, "Service Unavailable. Database connection failed.", ['status' => 'UNAVAILABLE']);
    }
} else {
    Response::json(false, 400, "Invalid health probe type. Use 'liveness' or 'readiness'.");
}
