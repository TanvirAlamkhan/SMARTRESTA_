<?php
/**
 * SMARTRESTA Product Availability Toggle API Endpoint
 * POST /api/v1/products/toggle_availability.php
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/MenuEngine.php';
require_once __DIR__ . '/../../../core/Response.php';

Auth::requireAuth();
Auth::requirePermission('products.manage_availability');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::json(false, 405, "Method Not Allowed");
}

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

$productId = !empty($input['product_id']) ? (int)$input['product_id'] : 0;
$isAvailable = isset($input['is_available']) ? (bool)$input['is_available'] : true;

if ($productId <= 0) {
    Response::json(false, 422, "Valid product ID is required.");
}

try {
    $userId = Auth::user()['id'] ?? 1;
    MenuEngine::toggleAvailability($productId, $isAvailable, $userId);
    $statusText = $isAvailable ? "AVAILABLE" : "UNAVAILABLE";
    Response::json(true, 200, "Product availability updated to {$statusText}");
} catch (Exception $e) {
    Response::json(false, 500, "Failed to toggle availability: " . $e->getMessage());
}
