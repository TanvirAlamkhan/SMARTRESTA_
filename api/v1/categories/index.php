<?php
/**
 * SMARTRESTA Product Category API Endpoint
 * GET  /api/v1/categories/index.php (List Categories)
 * POST /api/v1/categories/index.php (Create Category)
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/MenuEngine.php';
require_once __DIR__ . '/../../../core/Response.php';

Auth::requireAuth();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    Auth::requirePermissionOrEmpty('categories.view');
    $menuId = !empty($_GET['menu_id']) ? (int)$_GET['menu_id'] : null;

    try {
        $categories = MenuEngine::getCategories($menuId);
        Response::json(true, 200, "Categories retrieved successfully", $categories);
    } catch (Exception $e) {
        Response::json(false, 500, "Failed to load categories: " . $e->getMessage());
    }

} elseif ($method === 'POST') {
    Auth::requirePermission('categories.manage');
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

    $name = trim($input['name'] ?? '');
    $menuId = !empty($input['menu_id']) ? (int)$input['menu_id'] : null;
    $description = !empty($input['description']) ? trim($input['description']) : null;

    if (empty($name)) {
        Response::json(false, 422, "Category name is required.");
    }

    try {
        $userId = Auth::user()['id'] ?? 1;
        $result = MenuEngine::createCategory($name, $menuId, $description, $userId);
        Response::json(true, 201, "Category '{$name}' created successfully", $result);
    } catch (Exception $e) {
        Response::json(false, 500, "Failed to create category: " . $e->getMessage());
    }

} else {
    Response::json(false, 405, "Method Not Allowed");
}
