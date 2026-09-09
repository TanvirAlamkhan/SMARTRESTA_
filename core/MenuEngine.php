<?php
/**
 * SMARTRESTA - Core Menu & Product Management Engine
 * Production Domain Engine for Menus, Categories, Products, Variants, Modifiers & Pricing Calculation
 */

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/AuditLogger.php';

class MenuEngine {
    
    // =========================================================================
    // 1. MENUS MANAGEMENT
    // =========================================================================

    public static function getMenus(int $branchId = 1): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT m.*, b.name AS branch_name
            FROM menus m
            JOIN branches b ON m.branch_id = b.id
            WHERE m.branch_id = :branch_id AND m.deleted_at IS NULL
            ORDER BY m.display_order ASC, m.id ASC
        ");
        $stmt->execute(['branch_id' => $branchId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function createMenu(int $branchId, string $name, ?string $description = null, int $userId = 1): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            INSERT INTO menus (branch_id, name, description, status, display_order)
            VALUES (:branch_id, :name, :description, 'ACTIVE', 0)
        ");
        $stmt->execute([
            'branch_id' => $branchId,
            'name' => trim($name),
            'description' => $description ? trim($description) : null
        ]);
        $menuId = (int)$db->lastInsertId();

        AuditLogger::log($userId, 'MENU_CREATED', 'menu', $menuId, null, ['name' => $name, 'branch_id' => $branchId]);
        return ['id' => $menuId, 'name' => $name];
    }

    // =========================================================================
    // 2. CATEGORIES MANAGEMENT
    // =========================================================================

    public static function getCategories(?int $menuId = null): array {
        $db = Database::getConnection();
        $sql = "
            SELECT c.*, m.name AS menu_name,
                   (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id AND p.deleted_at IS NULL) AS product_count
            FROM categories c
            LEFT JOIN menus m ON c.menu_id = m.id
            WHERE c.deleted_at IS NULL
        ";
        $params = [];
        if ($menuId) {
            $sql .= " AND c.menu_id = :menu_id";
            $params['menu_id'] = $menuId;
        }
        $sql .= " ORDER BY c.sort_order ASC, c.id ASC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function createCategory(string $name, ?int $menuId = null, ?string $description = null, int $userId = 1): array {
        $db = Database::getConnection();
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
        
        // Ensure slug uniqueness
        $stmtCheck = $db->prepare("SELECT id FROM categories WHERE slug = :slug AND deleted_at IS NULL");
        $stmtCheck->execute(['slug' => $slug]);
        if ($stmtCheck->fetch()) {
            $slug .= '-' . time();
        }

        $stmt = $db->prepare("
            INSERT INTO categories (menu_id, name, slug, description, sort_order, status)
            VALUES (:menu_id, :name, :slug, :description, 0, 'ACTIVE')
        ");
        $stmt->execute([
            'menu_id' => $menuId,
            'name' => trim($name),
            'slug' => $slug,
            'description' => $description ? trim($description) : null
        ]);
        $catId = (int)$db->lastInsertId();

        AuditLogger::log($userId, 'CATEGORY_CREATED', 'category', $catId, null, ['name' => $name]);
        return ['id' => $catId, 'name' => $name, 'slug' => $slug];
    }

    // =========================================================================
    // 3. PRODUCTS MANAGEMENT & CATALOG
    // =========================================================================

    public static function getProducts(array $filters = []): array {
        $db = Database::getConnection();
        $sql = "
            SELECT p.*, c.name AS category_name, s.name AS station_name, s.badge_code AS station_badge
            FROM products p
            JOIN categories c ON p.category_id = c.id
            LEFT JOIN stations s ON p.default_station_id = s.id
            WHERE p.deleted_at IS NULL
        ";
        $params = [];

        if (!empty($filters['category_id'])) {
            $sql .= " AND p.category_id = :category_id";
            $params['category_id'] = (int)$filters['category_id'];
        }
        if (!empty($filters['status'])) {
            $sql .= " AND p.status = :status";
            $params['status'] = $filters['status'];
        }
        if (isset($filters['is_available'])) {
            $sql .= " AND p.is_available = :is_available";
            $params['is_available'] = (int)$filters['is_available'];
        }
        if (!empty($filters['search'])) {
            $sql .= " AND (p.name LIKE :search OR p.sku LIKE :search OR p.description LIKE :search)";
            $params['search'] = '%' . trim($filters['search']) . '%';
        }

        $sql .= " ORDER BY p.display_order ASC, p.id DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Attach variant count & modifier count
        foreach ($products as &$prod) {
            $stmtVar = $db->prepare("SELECT COUNT(*) FROM product_variants WHERE product_id = :pid");
            $stmtVar->execute(['pid' => $prod['id']]);
            $prod['variant_count'] = (int)$stmtVar->fetchColumn();

            $stmtMod = $db->prepare("SELECT COUNT(*) FROM product_modifiers WHERE product_id = :pid");
            $stmtMod->execute(['pid' => $prod['id']]);
            $prod['modifier_count'] = (int)$stmtMod->fetchColumn();
        }

        return $products;
    }

    public static function getProductDetail(int $productId): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT p.*, c.name AS category_name, s.name AS station_name
            FROM products p
            JOIN categories c ON p.category_id = c.id
            LEFT JOIN stations s ON p.default_station_id = s.id
            WHERE p.id = :id AND p.deleted_at IS NULL
        ");
        $stmt->execute(['id' => $productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$product) return null;

        // Fetch variants
        $stmtVar = $db->prepare("SELECT * FROM product_variants WHERE product_id = :pid ORDER BY display_order ASC, id ASC");
        $stmtVar->execute(['pid' => $productId]);
        $product['variants'] = $stmtVar->fetchAll(PDO::FETCH_ASSOC);

        // Fetch attached modifiers
        $stmtMod = $db->prepare("
            SELECT m.*, pm.product_id
            FROM modifiers m
            JOIN product_modifiers pm ON m.id = pm.modifier_id
            WHERE pm.product_id = :pid AND m.status = 'ACTIVE'
            ORDER BY m.display_order ASC, m.id ASC
        ");
        $stmtMod->execute(['pid' => $productId]);
        $product['modifiers'] = $stmtMod->fetchAll(PDO::FETCH_ASSOC);

        return $product;
    }

    public static function createProduct(array $data, int $userId = 1): array {
        $db = Database::getConnection();

        // Validation
        $name = trim($data['name'] ?? '');
        $categoryId = (int)($data['category_id'] ?? 0);
        $stationId = (int)($data['default_station_id'] ?? 1);
        $price = (float)($data['price'] ?? 0.00);
        $sku = !empty($data['sku']) ? trim($data['sku']) : null;
        $description = !empty($data['description']) ? trim($data['description']) : null;
        $shortDescription = !empty($data['short_description']) ? trim($data['short_description']) : null;
        $costPrice = (float)($data['cost_price'] ?? 0.00);

        $imageUrl = !empty($data['image_url']) ? trim($data['image_url']) : null;

        if (empty($name)) {
            throw new Exception("Product name is required.");
        }
        if ($categoryId <= 0) {
            throw new Exception("Valid category must be selected.");
        }
        if ($price < 0) {
            throw new Exception("Product price cannot be negative.");
        }

        // SKU uniqueness
        if ($sku) {
            $stmtCheck = $db->prepare("SELECT id FROM products WHERE sku = :sku AND deleted_at IS NULL");
            $stmtCheck->execute(['sku' => $sku]);
            if ($stmtCheck->fetch()) {
                throw new Exception("Product SKU '{$sku}' already exists in catalog.");
            }
        }

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));

        $db->beginTransaction();
        try {
            $stmt = $db->prepare("
                INSERT INTO products (category_id, default_station_id, name, slug, sku, short_description, description, price, cost_price, image_url, status, is_available, display_order)
                VALUES (:category_id, :default_station_id, :name, :slug, :sku, :short_description, :description, :price, :cost_price, :image_url, 'ACTIVE', 1, 0)
            ");
            $stmt->execute([
                'category_id' => $categoryId,
                'default_station_id' => $stationId,
                'name' => $name,
                'slug' => $slug,
                'sku' => $sku,
                'short_description' => $shortDescription,
                'description' => $description,
                'price' => $price,
                'cost_price' => $costPrice,
                'image_url' => $imageUrl
            ]);
            $productId = (int)$db->lastInsertId();

            AuditLogger::log($userId, 'PRODUCT_CREATED', 'product', $productId, null, [
                'name' => $name, 'price' => $price, 'sku' => $sku, 'image_url' => $imageUrl
            ]);

            $db->commit();
            return ['id' => $productId, 'name' => $name, 'price' => $price, 'image_url' => $imageUrl];

        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function updateProductImage(int $productId, string $imageUrl, int $userId = 1): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE products SET image_url = :image_url WHERE id = :id AND deleted_at IS NULL");
        $stmt->execute(['image_url' => trim($imageUrl), 'id' => $productId]);
        AuditLogger::log($userId, 'PRODUCT_IMAGE_UPDATED', 'product', $productId, null, ['image_url' => $imageUrl]);
        return true;
    }

    public static function toggleAvailability(int $productId, bool $isAvailable, int $userId = 1): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            UPDATE products 
            SET is_available = :is_available 
            WHERE id = :id AND deleted_at IS NULL
        ");
        $val = $isAvailable ? 1 : 0;
        $stmt->execute(['is_available' => $val, 'id' => $productId]);

        AuditLogger::log($userId, 'PRODUCT_AVAILABILITY_CHANGED', 'product', $productId, null, ['is_available' => $val]);
        return true;
    }

    // =========================================================================
    // 4. VARIANTS & MODIFIERS
    // =========================================================================

    public static function saveVariant(int $productId, string $variantName, float $price, ?string $sku = null, int $userId = 1): array {
        $db = Database::getConnection();
        if (empty(trim($variantName))) {
            throw new Exception("Variant name is required.");
        }
        if ($price < 0) {
            throw new Exception("Variant price cannot be negative.");
        }

        $stmt = $db->prepare("
            INSERT INTO product_variants (product_id, variant_name, sku, price, is_default, status)
            VALUES (:product_id, :variant_name, :sku, :price, 0, 'ACTIVE')
        ");
        $stmt->execute([
            'product_id' => $productId,
            'variant_name' => trim($variantName),
            'sku' => $sku ? trim($sku) : null,
            'price' => $price
        ]);
        $varId = (int)$db->lastInsertId();

        AuditLogger::log($userId, 'VARIANT_CREATED', 'product_variant', $varId, null, [
            'product_id' => $productId, 'variant_name' => $variantName, 'price' => $price
        ]);
        return ['id' => $varId, 'variant_name' => $variantName, 'price' => $price];
    }

    public static function getModifiers(): array {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM modifiers WHERE status = 'ACTIVE' ORDER BY display_order ASC, id ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function createModifier(string $name, float $price, ?string $description = null, int $userId = 1): array {
        $db = Database::getConnection();
        if (empty(trim($name))) {
            throw new Exception("Modifier name is required.");
        }
        if ($price < 0) {
            throw new Exception("Modifier price cannot be negative.");
        }

        $stmt = $db->prepare("
            INSERT INTO modifiers (name, description, price, status)
            VALUES (:name, :description, :price, 'ACTIVE')
        ");
        $stmt->execute([
            'name' => trim($name),
            'description' => $description ? trim($description) : null,
            'price' => $price
        ]);
        $modId = (int)$db->lastInsertId();

        AuditLogger::log($userId, 'MODIFIER_CREATED', 'modifier', $modId, null, ['name' => $name, 'price' => $price]);
        return ['id' => $modId, 'name' => $name, 'price' => $price];
    }

    public static function attachModifierToProduct(int $productId, int $modifierId, int $userId = 1): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            INSERT IGNORE INTO product_modifiers (product_id, modifier_id)
            VALUES (:product_id, :modifier_id)
        ");
        $stmt->execute(['product_id' => $productId, 'modifier_id' => $modifierId]);

        AuditLogger::log($userId, 'PRODUCT_MODIFIER_ATTACHED', 'product_modifier', $productId, null, [
            'product_id' => $productId, 'modifier_id' => $modifierId
        ]);
        return true;
    }

    public static function detachModifierFromProduct(int $productId, int $modifierId, int $userId = 1): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            DELETE FROM product_modifiers 
            WHERE product_id = :product_id AND modifier_id = :modifier_id
        ");
        $stmt->execute(['product_id' => $productId, 'modifier_id' => $modifierId]);

        AuditLogger::log($userId, 'PRODUCT_MODIFIER_DETACHED', 'product_modifier', $productId, null, [
            'product_id' => $productId, 'modifier_id' => $modifierId
        ]);
        return true;
    }

    // =========================================================================
    // 5. DETERMINISTIC PRICING ENGINE (Prompt 06 Integration Ready)
    // =========================================================================

    public static function calculateEffectivePrice(int $productId, ?int $variantId = null, array $selectedModifierIds = []): array {
        $db = Database::getConnection();

        // 1. Base Product
        $stmtP = $db->prepare("SELECT id, name, price, is_available, status FROM products WHERE id = :id AND deleted_at IS NULL");
        $stmtP->execute(['id' => $productId]);
        $product = $stmtP->fetch(PDO::FETCH_ASSOC);

        if (!$product || $product['status'] !== 'ACTIVE' || !$product['is_available']) {
            throw new Exception("Product is currently unavailable for ordering.");
        }

        $basePrice = (float)$product['price'];
        $unitPrice = $basePrice;
        $variantName = null;

        // 2. Variant Price Override
        if ($variantId) {
            $stmtV = $db->prepare("SELECT variant_name, price, price_adjustment FROM product_variants WHERE id = :vid AND product_id = :pid");
            $stmtV->execute(['vid' => $variantId, 'pid' => $productId]);
            $variant = $stmtV->fetch(PDO::FETCH_ASSOC);
            if ($variant) {
                $variantName = $variant['variant_name'];
                if ($variant['price'] !== null) {
                    $unitPrice = (float)$variant['price'];
                } else {
                    $unitPrice += (float)$variant['price_adjustment'];
                }
            }
        }

        // 3. Modifier Additional Charges
        $modifierTotal = 0.00;
        $modifierDetails = [];
        if (!empty($selectedModifierIds)) {
            $inClause = implode(',', array_map('intval', $selectedModifierIds));
            $stmtM = $db->prepare("
                SELECT m.id, m.name, m.price 
                FROM modifiers m
                JOIN product_modifiers pm ON m.id = pm.modifier_id
                WHERE pm.product_id = :pid AND m.id IN ({$inClause}) AND m.status = 'ACTIVE'
            ");
            $stmtM->execute(['pid' => $productId]);
            $modifiers = $stmtM->fetchAll(PDO::FETCH_ASSOC);

            foreach ($modifiers as $mod) {
                $modPrice = (float)$mod['price'];
                $modifierTotal += $modPrice;
                $modifierDetails[] = [
                    'id' => $mod['id'],
                    'name' => $mod['name'],
                    'price' => $modPrice
                ];
            }
        }

        $finalUnitPrice = $unitPrice + $modifierTotal;

        return [
            'product_id' => $productId,
            'product_name' => $product['name'],
            'variant_id' => $variantId,
            'variant_name' => $variantName,
            'base_price' => $basePrice,
            'effective_variant_price' => $unitPrice,
            'modifier_total' => $modifierTotal,
            'final_unit_price' => $finalUnitPrice,
            'modifiers' => $modifierDetails
        ];
    }
}
