<?php
/**
 * SMARTRESTA Product Variants API Endpoint
 * GET  /api/v1/variants/index.php?product_id=12
 * POST /api/v1/variants/index.php
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/MenuEngine.php';
require_once __DIR__ . '/../../../core/Response.php';

Auth::requireAuth();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    Auth::requirePermission('products.view');
    $productId = !empty($_GET['product_id']) ? (int)$_GET['product_id'] : 0;

    if ($productId <= 0) {
        Response::json(false, 422, "Valid product ID is required.");
    }

    try {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM product_variants WHERE product_id = :pid ORDER BY display_order ASC, id ASC");
        $stmt->execute(['pid' => $productId]);
        $variants = $stmt->fetchAll(PDO::FETCH_ASSOC);
        Response::json(true, 200, "Variants retrieved successfully", $variants);
    } catch (Exception $e) {
        Response::json(false, 500, "Failed to load variants: " . $e->getMessage());
    }

} elseif ($method === 'POST') {
    Auth::requirePermission('variants.manage');
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

    $productId = !empty($input['product_id']) ? (int)$input['product_id'] : 0;
    $variantName = trim($input['variant_name'] ?? '');
    $price = (float)($input['price'] ?? 0.00);
    $sku = !empty($input['sku']) ? trim($input['sku']) : null;

    if ($productId <= 0 || empty($variantName)) {
        Response::json(false, 422, "Product ID and variant name are required.");
    }

    try {
        $userId = Auth::user()['id'] ?? 1;
        $result = MenuEngine::saveVariant($productId, $variantName, $price, $sku, $userId);
        Response::json(true, 201, "Variant '{$variantName}' created successfully", $result);
    } catch (Exception $e) {
        Response::json(false, 500, "Failed to save variant: " . $e->getMessage());
    }

} else {
    Response::json(false, 405, "Method Not Allowed");
}
