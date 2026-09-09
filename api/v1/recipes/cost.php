<?php
/**
 * SMARTRESTA API Endpoint: Recipe Costing & Margin Analysis
 * GET /api/v1/recipes/cost.php?product_id={pid}&variant_id={vid}
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/InventoryEngine.php';
require_once __DIR__ . '/../../../core/Response.php';

Auth::requireAuth();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        if (!Auth::hasPermission('recipes.cost.view') && !Auth::hasPermission('inventory.view')) {
            Response::json(false, 403, "Permission denied: recipes.cost.view");
            exit;
        }

        $productId = isset($_GET['product_id']) ? (int)$_GET['product_id'] : null;
        $variantId = isset($_GET['variant_id']) ? (int)$_GET['variant_id'] : null;

        $costing = InventoryEngine::calculateRecipeCost($productId, $variantId);
        Response::json(true, 200, "Recipe cost analysis calculated successfully", $costing);

    } catch (Exception $e) {
        $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
        Response::json(false, $code, "Failed to calculate recipe cost: " . $e->getMessage());
    }
} else {
    Response::json(false, 405, "Method not allowed");
}
