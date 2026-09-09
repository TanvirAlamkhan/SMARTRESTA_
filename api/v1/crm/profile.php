<?php
/**
 * SMARTRESTA API Endpoint: Customer Profile & Merge
 * GET/POST /api/v1/crm/profile.php
 */

require_once __DIR__ . '/../../../core/Response.php';
require_once __DIR__ . '/../../../core/CRMEngine.php';

header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) {
            Response::json(false, 400, "Customer ID is required.");
        }
        $profile = CRMEngine::getCustomerProfile($id);
        Response::json(true, 200, "Customer profile retrieved successfully.", $profile);
    } elseif ($method === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $action = $input['action'] ?? '';

        if ($action === 'merge') {
            $sourceId = (int)($input['source_customer_id'] ?? 0);
            $targetId = (int)($input['target_customer_id'] ?? 0);
            if (!$sourceId || !$targetId) {
                Response::json(false, 400, "Both source and target customer IDs are required.");
            }
            CRMEngine::mergeCustomers($sourceId, $targetId, 1);
            Response::json(true, 200, "Customer records successfully merged.");
        } else {
            Response::json(false, 400, "Invalid action specified.");
        }
    } else {
        Response::json(false, 405, "Method Not Allowed");
    }
} catch (Exception $e) {
    $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
    Response::json(false, $code, $e->getMessage());
}
