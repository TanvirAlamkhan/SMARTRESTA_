<?php
/**
 * SMARTRESTA Global Helper Functions - Response Helper
 */

require_once __DIR__ . '/../core/Response.php';

if (!function_exists('sendJsonResponse')) {
    function sendJsonResponse($success, $statusCode, $message, $data = null, $errors = null) {
        Response::json($success, $statusCode, $message, $data, $errors);
    }
}
