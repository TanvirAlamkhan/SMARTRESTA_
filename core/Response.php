<?php
/**
 * SMARTRESTA Core Standardized Response Helper
 * Enforces production security HTTP headers, correlation IDs, and sanitizes error outputs.
 */

require_once __DIR__ . '/Logger.php';

class Response {
    public static function json($success, $statusCode, $message, $data = null, $errors = null) {
        while (ob_get_level() > 0) {
            @ob_end_clean();
        }
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('X-Request-ID: ' . Logger::getRequestId());

        $debugMode = defined('APP_DEBUG') ? (bool)APP_DEBUG : false;

        // In production mode (APP_DEBUG = false), mask internal server error messages containing SQL or path details
        if (!$success && $statusCode >= 500 && !$debugMode) {
            Logger::error("500 Server Error: {$message}", ['data' => $data, 'errors' => $errors]);
            $message = "A server error occurred. Please try again later.";
            $errors = null;
        }

        $response = [
            'success' => (bool)$success,
            'statusCode' => (int)$statusCode,
            'message' => (string)$message,
            'data' => $data,
            'request_id' => Logger::getRequestId(),
            'timestamp' => date('c')
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
}
