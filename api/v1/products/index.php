<?php
/**
 * SMARTRESTA Products API Endpoint
 * GET  /api/v1/products/index.php (Search, filter, list products)
 * POST /api/v1/products/index.php (Create product)
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/MenuEngine.php';
require_once __DIR__ . '/../../../core/Response.php';

Auth::requireAuth();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    Auth::requirePermission('products.view');

    $filters = [];
    if (!empty($_GET['category_id'])) $filters['category_id'] = (int)$_GET['category_id'];
    if (!empty($_GET['status'])) $filters['status'] = strtoupper(trim($_GET['status']));
    if (isset($_GET['is_available'])) $filters['is_available'] = (int)$_GET['is_available'];
    if (!empty($_GET['search'])) $filters['search'] = trim($_GET['search']);

    try {
        $products = MenuEngine::getProducts($filters);
        Response::json(true, 200, "Products retrieved successfully", $products);
    } catch (Exception $e) {
        Response::json(false, 500, "Failed to load products: " . $e->getMessage());
    }

} elseif ($method === 'POST') {
    Auth::requirePermission('products.create');
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

    try {
        $userId = Auth::user()['id'] ?? 1;
        $result = MenuEngine::createProduct($input, $userId);
        Response::json(true, 201, "Product successfully created", $result);
    } catch (Exception $e) {
        Response::json(false, 422, "Failed to create product: " . $e->getMessage());
    }

} else {
    Response::json(false, 405, "Method Not Allowed");
}
