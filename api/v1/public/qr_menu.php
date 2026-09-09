<?php
/**
 * SMARTRESTA Public API Endpoint: QR Menu Access
 * GET /api/v1/public/qr_menu.php?token=TOKEN
 */

require_once __DIR__ . '/../../../core/Response.php';
require_once __DIR__ . '/../../../core/CRMEngine.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $token = $_GET['token'] ?? '';
    if (empty($token)) {
        Response::json(false, 400, "QR token is required.");
    }
    $menuData = CRMEngine::getPublicMenuForQR($token);
    Response::json(true, 200, "Public QR menu retrieved.", $menuData);
} catch (Exception $e) {
    $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
    Response::json(false, $code, $e->getMessage());
}
