<?php
/**
 * SMARTRESTA API Endpoint: Ingredients Management
 * GET  /api/v1/ingredients/index.php?branch_id={bid}&category_id={cid}&search={q}
 * POST /api/v1/ingredients/index.php
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/InventoryEngine.php';
require_once __DIR__ . '/../../../core/Response.php';

Auth::requireAuth();

$method = $_SERVER['REQUEST_METHOD'];
$branchId = $_SESSION['branch_id'] ?? 1;
$userId = $_SESSION['user_id'] ?? 1;

if ($method === 'GET') {
    try {
        Auth::requirePermission('inventory.view');
        $bId = isset($_GET['branch_id']) ? (int)$_GET['branch_id'] : $branchId;
        $catId = isset($_GET['category_id']) ? (int)$_GET['category_id'] : null;
        $search = trim($_GET['search'] ?? '');

        $ingredients = InventoryEngine::getIngredients($bId, $catId, $search);
        $categories = InventoryEngine::getCategories();
        $locations = InventoryEngine::getLocations($bId);

        Response::json(true, 200, "Ingredients retrieved successfully", [
            'ingredients' => $ingredients,
            'categories' => $categories,
            'locations' => $locations
        ]);
    } catch (Exception $e) {
        $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
        Response::json(false, $code, "Failed to load ingredients: " . $e->getMessage());
    }
} else if ($method === 'POST') {
    try {
        Auth::requirePermission('ingredients.manage');
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['name'])) {
            Response::json(false, 400, "Ingredient name is required.");
            exit;
        }

        $data['branch_id'] = $data['branch_id'] ?? $branchId;
        $result = InventoryEngine::saveIngredient($data, $userId);
        Response::json(true, 200, "Ingredient saved successfully", $result);

    } catch (Exception $e) {
        $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 400;
        Response::json(false, $code, "Failed to save ingredient: " . $e->getMessage());
    }
} else {
    Response::json(false, 405, "Method not allowed");
}
