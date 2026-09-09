<?php
/**
 * SMARTRESTA API Endpoint: Table QR Token Management
 * GET/POST /api/v1/crm/qr.php
 */

require_once __DIR__ . '/../../../core/Response.php';
require_once __DIR__ . '/../../../core/CRMEngine.php';

header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        $branchId = (int)($_GET['branch_id'] ?? 1);
        $tables = CRMEngine::getQRTables($branchId);
        Response::json(true, 200, "QR tables retrieved.", ['tables' => $tables]);
    } elseif ($method === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $action = $input['action'] ?? 'generate';

        $branchId = (int)($input['branch_id'] ?? 1);
        $tableId = (int)($input['table_id'] ?? 0);

        if ($action === 'generate') {
            if (!$tableId) Response::json(false, 400, "Table ID is required.");
            $tokenRow = CRMEngine::getOrCreateQRToken($branchId, $tableId);
            Response::json(true, 200, "QR token generated.", $tokenRow);
        } elseif ($action === 'regenerate') {
            if (!$tableId) Response::json(false, 400, "Table ID is required.");
            $tokenRow = CRMEngine::regenerateQRToken($branchId, $tableId, 1);
            Response::json(true, 200, "QR token regenerated.", $tokenRow);
        } elseif ($action === 'revoke') {
            $tokenId = (int)($input['token_id'] ?? 0);
            if (!$tokenId) Response::json(false, 400, "Token ID is required.");
            CRMEngine::revokeQRToken($tokenId, 1);
            Response::json(true, 200, "QR token revoked.");
        } else {
            Response::json(false, 400, "Invalid action.");
        }
    } else {
        Response::json(false, 405, "Method Not Allowed");
    }
} catch (Exception $e) {
    $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
    Response::json(false, $code, $e->getMessage());
}
