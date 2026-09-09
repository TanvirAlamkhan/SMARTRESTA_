<?php
/**
 * SMARTRESTA API Endpoint: Recipe BOM Management
 * GET  /api/v1/recipes/index.php?product_id={pid}&variant_id={vid}
 * POST /api/v1/recipes/index.php
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/InventoryEngine.php';
require_once __DIR__ . '/../../../core/Response.php';

Auth::requireAuth();

$method = $_SERVER['REQUEST_METHOD'];
$userId = $_SESSION['user_id'] ?? 1;

if ($method === 'GET') {
    try {
        Auth::requirePermission('inventory.view');
        $productId = isset($_GET['product_id']) ? (int)$_GET['product_id'] : null;
        $variantId = isset($_GET['variant_id']) ? (int)$_GET['variant_id'] : null;

        if ($productId || $variantId) {
            $recipe = InventoryEngine::getRecipe($productId, $variantId);
            $costing = InventoryEngine::calculateRecipeCost($productId, $variantId);
            Response::json(true, 200, "Recipe retrieved successfully", [
                'recipe' => $recipe,
                'costing' => $costing
            ]);
        } else {
            $recipes = InventoryEngine::getRecipesList();
            Response::json(true, 200, "Recipes list retrieved successfully", ['recipes' => $recipes]);
        }
    } catch (Exception $e) {
        $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
        Response::json(false, $code, "Failed to load recipe: " . $e->getMessage());
    }
} else if ($method === 'POST') {
    try {
        Auth::requirePermission('recipes.manage');
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['product_id']) && empty($data['variant_id'])) {
            Response::json(false, 400, "product_id or variant_id is required.");
            exit;
        }

        $id = InventoryEngine::saveRecipe($data, $userId);
        Response::json(true, 200, "Recipe BOM saved successfully", ['recipe_id' => $id]);

    } catch (Exception $e) {
        $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 400;
        Response::json(false, $code, "Failed to save recipe BOM: " . $e->getMessage());
    }
} else {
    Response::json(false, 405, "Method not allowed");
}
