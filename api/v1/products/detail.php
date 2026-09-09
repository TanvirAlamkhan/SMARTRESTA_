<?php
/**
 * SMARTRESTA Product Detail & Modification API Endpoint
 * GET /api/v1/products/detail.php?id=12 (Get Product with Variants & Modifiers)
 * POST /api/v1/products/detail.php (Update Product)
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/MenuEngine.php';
require_once __DIR__ . '/../../../core/Response.php';

Auth::requireAuth();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    Auth::requirePermission('products.view');
    $productId = !empty($_GET['id']) ? (int)$_GET['id'] : 0;

    if ($productId <= 0) {
        Response::json(false, 422, "Valid product ID is required.");
    }

    try {
        $product = MenuEngine::getProductDetail($productId);
        if (!$product) {
            Response::json(false, 404, "Product not found.");
        }
        Response::json(true, 200, "Product details retrieved successfully", $product);
    } catch (Exception $e) {
        Response::json(false, 500, "Failed to load product details: " . $e->getMessage());
    }

} elseif ($method === 'POST' || $method === 'PUT') {
    Auth::requirePermission('products.update');
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

    $productId = !empty($input['id']) ? (int)$input['id'] : 0;
    if ($productId <= 0) {
        Response::json(false, 422, "Valid product ID is required.");
    }

    try {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            UPDATE products 
            SET name = :name,
                category_id = :category_id,
                price = :price,
                sku = :sku,
                description = :description,
                default_station_id = :station_id
            WHERE id = :id AND deleted_at IS NULL
        ");
        $stmt->execute([
            'id' => $productId,
            'name' => trim($input['name']),
            'category_id' => (int)$input['category_id'],
            'price' => (float)$input['price'],
            'sku' => !empty($input['sku']) ? trim($input['sku']) : null,
            'description' => !empty($input['description']) ? trim($input['description']) : null,
            'station_id' => !empty($input['default_station_id']) ? (int)$input['default_station_id'] : 1
        ]);

        AuditLogger::log(Auth::user()['id'] ?? 1, 'PRODUCT_UPDATED', 'product', $productId, null, $input);
        Response::json(true, 200, "Product updated successfully");
    } catch (Exception $e) {
        Response::json(false, 500, "Failed to update product: " . $e->getMessage());
    }

} else {
    Response::json(false, 405, "Method Not Allowed");
}
