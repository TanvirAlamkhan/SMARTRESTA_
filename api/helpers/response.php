<?php
/**
 * SMARTRESTA Standardized JSON Response Helper
 */

function sendJsonResponse($success, $statusCode, $message, $data = null) {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    header('X-Requested-With: XMLHttpRequest');

    echo json_encode([
        'success' => $success,
        'statusCode' => $statusCode,
        'message' => $message,
        'data' => $data,
        'timestamp' => date('c')
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}
