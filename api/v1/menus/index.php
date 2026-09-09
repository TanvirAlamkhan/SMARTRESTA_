<?php
/**
 * SMARTRESTA Menu Management API Endpoint
 * GET  /api/v1/menus/index.php (List Menus)
 * POST /api/v1/menus/index.php (Create Menu)
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/MenuEngine.php';
require_once __DIR__ . '/../../../core/Response.php';

Auth::requireAuth();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    Auth::requirePermission('menus.view');
    $branchId = !empty($_GET['branch_id']) ? (int)$_GET['branch_id'] : 1;

    try {
        $menus = MenuEngine::getMenus($branchId);
        Response::json(true, 200, "Menus retrieved successfully", $menus);
    } catch (Exception $e) {
        Response::json(false, 500, "Failed to load menus: " . $e->getMessage());
    }

} elseif ($method === 'POST') {
    Auth::requirePermission('menus.manage');
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

    $name = trim($input['name'] ?? '');
    $branchId = !empty($input['branch_id']) ? (int)$input['branch_id'] : 1;
    $description = !empty($input['description']) ? trim($input['description']) : null;

    if (empty($name)) {
        Response::json(false, 422, "Menu name is required.");
    }

    try {
        $userId = Auth::user()['id'] ?? 1;
        $result = MenuEngine::createMenu($branchId, $name, $description, $userId);
        Response::json(true, 201, "Menu '{$name}' created successfully", $result);
    } catch (Exception $e) {
        Response::json(false, 500, "Failed to create menu: " . $e->getMessage());
    }

} else {
    Response::json(false, 405, "Method Not Allowed");
}
