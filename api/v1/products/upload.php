<?php
/**
 * SMARTRESTA API Endpoint: Upload / Set Product Image
 * POST /api/v1/products/upload.php
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Response.php';
require_once __DIR__ . '/../../../core/MenuEngine.php';

$productId = $_POST['product_id'] ?? ($_GET['product_id'] ?? null);
$imageUrl = null;

// Case 1: Direct File Upload
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['image']['tmp_name'];
    $fileName = $_FILES['image']['name'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    if (!in_array($fileExtension, $allowedExtensions)) {
        Response::json(false, 400, "Invalid file format. Allowed formats: " . implode(', ', $allowedExtensions));
    }

    $uploadDir = __DIR__ . '/../../../assets/images/products/';
    if (!file_exists($uploadDir)) {
        @mkdir($uploadDir, 0755, true);
    }

    $newFileName = 'prod_' . uniqid() . '_' . time() . '.' . $fileExtension;
    $destPath = $uploadDir . $newFileName;

    if (move_uploaded_file($fileTmpPath, $destPath)) {
        $imageUrl = 'assets/images/products/' . $newFileName;
    } else {
        Response::json(false, 500, "Failed to save uploaded image file.");
    }
} 
// Case 2: JSON or POST image_url string input
else {
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    if (!empty($input['image_url'])) {
        $imageUrl = trim($input['image_url']);
    }
    if (empty($productId) && !empty($input['product_id'])) {
        $productId = $input['product_id'];
    }
}

if (!$imageUrl) {
    Response::json(false, 400, "No image file uploaded or image_url provided.");
}

// Update DB if product_id is passed
if (!empty($productId)) {
    try {
        $menuEngine = new MenuEngine();
        $updated = $menuEngine->updateProductImage($productId, $imageUrl);
        Response::json(true, 200, "Product image updated successfully", [
            'product_id' => $productId,
            'image_url' => $imageUrl,
            'db_updated' => $updated
        ]);
    } catch (Exception $e) {
        Response::json(false, 500, "Error updating database: " . $e->getMessage());
    }
} else {
    Response::json(true, 200, "Image uploaded successfully", [
        'image_url' => $imageUrl
    ]);
}
