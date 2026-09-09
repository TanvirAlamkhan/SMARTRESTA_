<?php
/**
 * SMARTRESTA Core Inventory, Purchasing, Recipe Costing & Stock Management Engine
 * Prompt 11: Inventory, Purchasing, Recipe Costing & Stock Management Engine
 */

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/AuditLogger.php';

class InventoryEngine {

    // =========================================================================
    // 1. INVENTORY STOCK & INGREDIENT SERVICE
    // =========================================================================

    public static function getIngredients(int $branchId, ?int $categoryId = null, string $search = ''): array {
        $db = Database::getConnection();
        $params = [':bid' => $branchId];

        $sql = "
            SELECT 
                i.*, 
                c.name AS category_name,
                s.name AS supplier_name,
                COALESCE(SUM(st.quantity_on_hand), i.current_stock, 0) AS total_on_hand,
                (COALESCE(SUM(st.quantity_on_hand), i.current_stock, 0) * COALESCE(i.average_cost, i.cost_per_unit, 0)) AS stock_value
            FROM ingredients i
            LEFT JOIN ingredient_categories c ON i.category_id = c.id
            LEFT JOIN suppliers s ON i.preferred_supplier_id = s.id
            LEFT JOIN inventory_stock st ON i.id = st.ingredient_id
            WHERE (i.branch_id = :bid OR i.branch_id IS NULL OR i.branch_id = 1)
        ";

        if ($categoryId) {
            $sql .= " AND i.category_id = :cid";
            $params[':cid'] = $categoryId;
        }

        if (!empty($search)) {
            $sql .= " AND (i.name LIKE :q OR i.sku LIKE :q OR i.ingredient_code LIKE :q)";
            $params[':q'] = "%{$search}%";
        }

        $sql .= " GROUP BY i.id ORDER BY i.name ASC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function saveIngredient(array $data, int $userId): array {
        $db = Database::getConnection();
        $id = (int)($data['id'] ?? 0);
        $branchId = (int)($data['branch_id'] ?? 1);
        $name = trim($data['name'] ?? '');
        $sku = trim($data['sku'] ?? ('ING-' . strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $name), 0, 4)) . '-' . rand(100, 999)));
        $baseUnit = trim($data['base_unit'] ?? 'kg');
        $purchaseUnit = trim($data['purchase_unit'] ?? $baseUnit);
        $conversionFactor = (float)($data['conversion_factor'] ?? 1.0);
        $costPerUnit = (float)($data['cost_per_unit'] ?? ($data['average_cost'] ?? 0.0));
        $minStock = (float)($data['min_stock'] ?? ($data['reorder_level'] ?? 10.0));
        $maxStock = (float)($data['max_stock'] ?? 100.0);
        $categoryId = !empty($data['category_id']) ? (int)$data['category_id'] : null;
        $supplierId = !empty($data['preferred_supplier_id']) ? (int)$data['preferred_supplier_id'] : null;

        if (empty($name)) {
            throw new Exception("Ingredient name is required.");
        }

        if ($id > 0) {
            $stmt = $db->prepare("
                UPDATE ingredients 
                SET name = :name, sku = :sku, category_id = :cid, unit = :unit, base_unit = :bunit, purchase_unit = :punit, 
                    conversion_factor = :cfactor, cost_per_unit = :cost1, average_cost = :cost2, min_stock = :minst, 
                    reorder_level = :reorder, max_stock = :maxst, preferred_supplier_id = :sid, updated_at = NOW()
                WHERE id = :id
            ");
            $stmt->execute([
                ':name' => $name, ':sku' => $sku, ':cid' => $categoryId, ':unit' => $baseUnit, ':bunit' => $baseUnit, ':punit' => $purchaseUnit,
                ':cfactor' => $conversionFactor, ':cost1' => $costPerUnit, ':cost2' => $costPerUnit, ':minst' => $minStock,
                ':reorder' => $minStock, ':maxst' => $maxStock, ':sid' => $supplierId, ':id' => $id
            ]);

            AuditLogger::log('INGREDIENT_UPDATED', 'ingredients', $id, null, "Ingredient '{$name}' updated.", $userId);
            return ['success' => true, 'id' => $id, 'name' => $name];
        } else {
            $stmt = $db->prepare("
                INSERT INTO ingredients 
                (branch_id, name, sku, ingredient_code, category_id, unit, base_unit, purchase_unit, conversion_factor, current_stock, min_stock, reorder_level, max_stock, cost_per_unit, average_cost, preferred_supplier_id, status, created_at)
                VALUES (:bid, :name, :sku, :code, :cid, :unit, :bunit, :punit, :cfactor, 0.00, :minst, :reorder, :maxst, :cost1, :cost2, :sid, 'ACTIVE', NOW())
            ");
            $stmt->execute([
                ':bid' => $branchId, ':name' => $name, ':sku' => $sku, ':code' => $sku, ':cid' => $categoryId, ':unit' => $baseUnit, ':bunit' => $baseUnit,
                ':punit' => $purchaseUnit, ':cfactor' => $conversionFactor, ':minst' => $minStock, ':reorder' => $minStock,
                ':maxst' => $maxStock, ':cost1' => $costPerUnit, ':cost2' => $costPerUnit, ':sid' => $supplierId
            ]);
            $newId = (int)$db->lastInsertId();

            // Initialize Main Store stock entry
            $db->prepare("
                INSERT INTO inventory_stock (ingredient_id, location_id, quantity_on_hand, average_cost)
                VALUES (:ing_id, 1, 0.00, :cost)
                ON DUPLICATE KEY UPDATE average_cost = VALUES(average_cost)
            ")->execute([':ing_id' => $newId, ':cost' => $costPerUnit]);

            AuditLogger::log('INGREDIENT_CREATED', 'ingredients', $newId, null, "Ingredient '{$name}' created.", $userId);
            return ['success' => true, 'id' => $newId, 'name' => $name];
        }
    }

    public static function getStockBalances(int $branchId, ?int $locationId = null): array {
        $db = Database::getConnection();
        $params = [':bid' => $branchId];

        $sql = "
            SELECT 
                i.id AS ingredient_id,
                i.name AS ingredient_name,
                i.sku,
                i.base_unit,
                i.min_stock AS reorder_level,
                i.max_stock,
                COALESCE(i.average_cost, i.cost_per_unit, 0) AS average_cost,
                l.id AS location_id,
                l.name AS location_name,
                COALESCE(st.quantity_on_hand, i.current_stock, 0) AS quantity_on_hand,
                COALESCE(st.reserved_quantity, 0) AS reserved_quantity,
                (COALESCE(st.quantity_on_hand, i.current_stock, 0) * COALESCE(i.average_cost, i.cost_per_unit, 0)) AS stock_value,
                CASE 
                    WHEN COALESCE(st.quantity_on_hand, i.current_stock, 0) <= 0 THEN 'OUT_OF_STOCK'
                    WHEN COALESCE(st.quantity_on_hand, i.current_stock, 0) <= i.min_stock THEN 'LOW_STOCK'
                    ELSE 'IN_STOCK'
                END AS stock_status
            FROM ingredients i
            LEFT JOIN inventory_stock st ON i.id = st.ingredient_id
            LEFT JOIN inventory_locations l ON st.location_id = l.id
            WHERE (i.branch_id = :bid OR i.branch_id IS NULL OR i.branch_id = 1)
        ";

        if ($locationId) {
            $sql .= " AND (st.location_id = :lid OR st.location_id IS NULL)";
            $params[':lid'] = $locationId;
        }

        $sql .= " ORDER BY i.name ASC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Perform stock adjustment IN/OUT with transaction logging
     */
    public static function adjustStock(int $ingredientId, int $locationId, float $quantity, string $direction, string $reason, int $userId, string $notes = ''): array {
        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            if ($quantity <= 0) {
                throw new Exception("Adjustment quantity must be greater than zero.");
            }

            // Lock ingredient and stock row
            $ingStmt = $db->prepare("SELECT * FROM ingredients WHERE id = :id FOR UPDATE");
            $ingStmt->execute([':id' => $ingredientId]);
            $ing = $ingStmt->fetch();

            if (!$ing) {
                throw new Exception("Ingredient #{$ingredientId} not found.");
            }

            $stStmt = $db->prepare("SELECT * FROM inventory_stock WHERE ingredient_id = :iid AND location_id = :lid FOR UPDATE");
            $stStmt->execute([':iid' => $ingredientId, ':lid' => $locationId]);
            $st = $stStmt->fetch();

            $balanceBefore = (float)($st['quantity_on_hand'] ?? $ing['current_stock'] ?? 0.0);
            $unitCost = (float)($ing['average_cost'] ?? $ing['cost_per_unit'] ?? 0.0);

            $changeQty = ($direction === 'IN') ? $quantity : -$quantity;
            $balanceAfter = $balanceBefore + $changeQty;

            if ($balanceAfter < 0) {
                throw new Exception("Insufficient stock balance! Available: {$balanceBefore} {$ing['base_unit']}, Attempted Deduction: {$quantity}.");
            }

            // Update inventory_stock
            $upSt = $db->prepare("
                INSERT INTO inventory_stock (ingredient_id, location_id, quantity_on_hand, average_cost)
                VALUES (:iid, :lid, :qty, :cost)
                ON DUPLICATE KEY UPDATE quantity_on_hand = VALUES(quantity_on_hand), average_cost = VALUES(average_cost)
            ");
            $upSt->execute([':iid' => $ingredientId, ':lid' => $locationId, ':qty' => $balanceAfter, ':cost' => $unitCost]);

            // Sync total stock on ingredients table
            $db->prepare("UPDATE ingredients SET current_stock = (SELECT COALESCE(SUM(quantity_on_hand), 0) FROM inventory_stock WHERE ingredient_id = :iid1) WHERE id = :iid2")
               ->execute([':iid1' => $ingredientId, ':iid2' => $ingredientId]);

            // Insert transaction ledger record
            $type = ($direction === 'IN') ? 'ADJUSTMENT_IN' : 'ADJUSTMENT_OUT';
            $totalCost = round($quantity * $unitCost, 2);

            $insTx = $db->prepare("
                INSERT INTO inventory_transactions 
                (branch_id, ingredient_id, location_id, type, quantity, unit_cost, total_cost, balance_before, balance_after, reference_type, created_by, notes, created_at)
                VALUES (:bid, :iid, :lid, :type, :qty, :cost, :tcost, :bbef, :baft, 'manual_adjustment', :uid, :notes, NOW())
            ");
            $insTx->execute([
                ':bid' => $ing['branch_id'] ?? 1, ':iid' => $ingredientId, ':lid' => $locationId, ':type' => $type,
                ':qty' => $changeQty, ':cost' => $unitCost, ':tcost' => $totalCost, ':bbef' => $balanceBefore,
                ':baft' => $balanceAfter, ':uid' => $userId, ':notes' => "[Reason: {$reason}] " . $notes
            ]);

            AuditLogger::log('STOCK_ADJUSTED', 'inventory_stock', $ingredientId, null, "Stock adjusted ({$direction} {$quantity} {$ing['base_unit']}). New balance: {$balanceAfter}", $userId);

            $db->commit();
            return [
                'success' => true,
                'ingredient_id' => $ingredientId,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'direction' => $direction
            ];

        } catch (Exception $e) {
            if ($db && $db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Record stock wastage / spoilage
     */
    public static function recordWastage($ingredientId, int $locationId = 0, float $quantity = 0.0, string $reason = '', int $userId = 0, string $notes = ''): array {
        if (is_array($ingredientId)) {
            $data = $ingredientId;
            $userId = (int)$locationId; // 2nd param passed is userId when 1st param is data array
            $ingredientId = (int)($data['ingredient_id'] ?? 0);
            $locationId = (int)($data['location_id'] ?? 0);
            $quantity = (float)($data['quantity'] ?? 0);
            $reason = (string)($data['reason'] ?? '');
            $notes = (string)($data['notes'] ?? '');
        }

        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            if ($quantity <= 0) {
                throw new Exception("Wastage quantity must be greater than zero.");
            }

            $ingStmt = $db->prepare("SELECT * FROM ingredients WHERE id = :id FOR UPDATE");
            $ingStmt->execute([':id' => $ingredientId]);
            $ing = $ingStmt->fetch();

            if (!$ing) {
                throw new Exception("Ingredient #{$ingredientId} not found.");
            }

            $stStmt = $db->prepare("SELECT * FROM inventory_stock WHERE ingredient_id = :iid AND location_id = :lid FOR UPDATE");
            $stStmt->execute([':iid' => $ingredientId, ':lid' => $locationId]);
            $st = $stStmt->fetch();

            $balanceBefore = (float)($st['quantity_on_hand'] ?? $ing['current_stock'] ?? 0.0);
            $unitCost = (float)($ing['average_cost'] ?? $ing['cost_per_unit'] ?? 0.0);
            $balanceAfter = $balanceBefore - $quantity;

            if ($balanceAfter < 0) {
                throw new Exception("Insufficient stock for wastage recording. Current balance: {$balanceBefore} {$ing['base_unit']}.");
            }

            $estimatedCost = round($quantity * $unitCost, 2);

            $allowedReasons = ['EXPIRED', 'DAMAGED', 'SPOILED', 'BURNED', 'PREPARATION_LOSS', 'OTHER'];
            $cleanReason = strtoupper(trim($reason));
            if (!in_array($cleanReason, $allowedReasons)) {
                if (strpos($cleanReason, 'SPOIL') !== false) {
                    $cleanReason = 'SPOILED';
                } elseif (strpos($cleanReason, 'EXPIR') !== false) {
                    $cleanReason = 'EXPIRED';
                } elseif (strpos($cleanReason, 'DAMAG') !== false) {
                    $cleanReason = 'DAMAGED';
                } elseif (strpos($cleanReason, 'BURN') !== false) {
                    $cleanReason = 'BURNED';
                } elseif (strpos($cleanReason, 'PREP') !== false || strpos($cleanReason, 'LOSS') !== false) {
                    $cleanReason = 'PREPARATION_LOSS';
                } else {
                    $notes = trim("[Reason: {$reason}] " . $notes);
                    $cleanReason = 'OTHER';
                }
            }

            // Insert wastage record
            $insWaste = $db->prepare("
                INSERT INTO wastage_records (branch_id, ingredient_id, location_id, quantity, unit, reason, estimated_cost, recorded_by, notes, created_at)
                VALUES (:bid, :iid, :lid, :qty, :unit, :reason, :ecost, :uid, :notes, NOW())
            ");
            $insWaste->execute([
                ':bid' => $ing['branch_id'] ?? 1, ':iid' => $ingredientId, ':lid' => $locationId,
                ':qty' => $quantity, ':unit' => $ing['base_unit'], ':reason' => $cleanReason,
                ':ecost' => $estimatedCost, ':uid' => $userId, ':notes' => $notes
            ]);
            $wastageId = (int)$db->lastInsertId();

            // Update inventory_stock
            $db->prepare("UPDATE inventory_stock SET quantity_on_hand = :qty WHERE ingredient_id = :iid AND location_id = :lid")
               ->execute([':qty' => $balanceAfter, ':iid' => $ingredientId, ':lid' => $locationId]);

            $db->prepare("UPDATE ingredients SET current_stock = (SELECT COALESCE(SUM(quantity_on_hand), 0) FROM inventory_stock WHERE ingredient_id = :iid1) WHERE id = :iid2")
               ->execute([':iid1' => $ingredientId, ':iid2' => $ingredientId]);

            // Insert transaction ledger record
            $db->prepare("
                INSERT INTO inventory_transactions 
                (branch_id, ingredient_id, location_id, type, quantity, unit_cost, total_cost, balance_before, balance_after, reference_type, reference_id, created_by, notes, created_at)
                VALUES (:bid, :iid, :lid, 'WASTAGE', :qty, :cost, :tcost, :bbef, :baft, 'wastage_records', :ref_id, :uid, :notes, NOW())
            ")->execute([
                ':bid' => $ing['branch_id'] ?? 1, ':iid' => $ingredientId, ':lid' => $locationId,
                ':qty' => -$quantity, ':cost' => $unitCost, ':tcost' => $estimatedCost,
                ':bbef' => $balanceBefore, ':baft' => $balanceAfter, ':ref_id' => $wastageId,
                ':uid' => $userId, ':notes' => "[Wastage Reason: {$reason}] " . $notes
            ]);

            AuditLogger::log('WASTAGE_RECORDED', 'wastage_records', $wastageId, null, "Wastage recorded: {$quantity} {$ing['base_unit']} of {$ing['name']} (Reason: {$reason})", $userId);

            $db->commit();
            return ['success' => true, 'wastage_id' => $wastageId, 'estimated_cost' => $estimatedCost, 'balance_after' => $balanceAfter];

        } catch (Exception $e) {
            if ($db && $db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Transfer stock between locations
     */
    /**
     * Transfer stock between locations (Supports single item or array of items)
     */
    public static function transferStock(int $sourceLocId, int $destLocId, $ingredientIdOrItems, $quantityOrUserId = 1, $userIdOrNotes = 1, string $notes = ''): array {
        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            if ($sourceLocId === $destLocId) {
                throw new Exception("Source and destination locations must be different.");
            }

            $items = [];
            if (is_array($ingredientIdOrItems)) {
                $items = $ingredientIdOrItems;
                $userId = (int)$quantityOrUserId;
                $notes = (string)$userIdOrNotes;
            } else {
                $items = [
                    [
                        'ingredient_id' => (int)$ingredientIdOrItems,
                        'quantity' => (float)$quantityOrUserId
                    ]
                ];
                $userId = (int)$userIdOrNotes;
            }

            if (empty($items)) {
                throw new Exception("At least one transfer line item is required.");
            }

            $tNum = 'TRF-' . date('Ymd') . '-' . sprintf('%04d', rand(1000, 9999));
            $insTrf = $db->prepare("
                INSERT INTO stock_transfers (branch_id, transfer_number, source_location_id, destination_location_id, status, created_by, notes, created_at)
                VALUES (1, :tnum, :slid, :dlid, 'COMPLETED', :uid, :notes, NOW())
            ");
            $insTrf->execute([
                ':tnum' => $tNum, ':slid' => $sourceLocId,
                ':dlid' => $destLocId, ':uid' => $userId, ':notes' => $notes
            ]);
            $transferId = (int)$db->lastInsertId();

            foreach ($items as $item) {
                $ingredientId = (int)($item['ingredient_id'] ?? 0);
                $quantity = (float)($item['quantity'] ?? 0.0);

                if (!$ingredientId || $quantity <= 0) continue;

                $ingStmt = $db->prepare("SELECT * FROM ingredients WHERE id = :id FOR UPDATE");
                $ingStmt->execute([':id' => $ingredientId]);
                $ing = $ingStmt->fetch();

                if (!$ing) {
                    throw new Exception("Ingredient #{$ingredientId} not found.");
                }

                // Lock source stock
                $srcStmt = $db->prepare("SELECT * FROM inventory_stock WHERE ingredient_id = :iid AND location_id = :lid FOR UPDATE");
                $srcStmt->execute([':iid' => $ingredientId, ':lid' => $sourceLocId]);
                $srcSt = $srcStmt->fetch();

                $srcBalBefore = (float)($srcSt['quantity_on_hand'] ?? 0.0);
                if ($srcBalBefore < $quantity) {
                    throw new Exception("Insufficient stock in source location for ingredient '{$ing['name']}'. Available: {$srcBalBefore} {$ing['base_unit']}.");
                }

                $srcBalAfter = $srcBalBefore - $quantity;
                $unitCost = (float)($ing['average_cost'] ?? $ing['cost_per_unit'] ?? 0.0);
                $totalCost = round($quantity * $unitCost, 2);

                // Deduct from source
                $db->prepare("UPDATE inventory_stock SET quantity_on_hand = :qty WHERE ingredient_id = :iid AND location_id = :lid")
                   ->execute([':qty' => $srcBalAfter, ':iid' => $ingredientId, ':lid' => $sourceLocId]);

                // Add to destination
                $dstStmt = $db->prepare("SELECT * FROM inventory_stock WHERE ingredient_id = :iid AND location_id = :lid FOR UPDATE");
                $dstStmt->execute([':iid' => $ingredientId, ':lid' => $destLocId]);
                $dstSt = $dstStmt->fetch();

                $dstBalBefore = (float)($dstSt['quantity_on_hand'] ?? 0.0);
                $dstBalAfter = $dstBalBefore + $quantity;

                $db->prepare("
                    INSERT INTO inventory_stock (ingredient_id, location_id, quantity_on_hand, average_cost)
                    VALUES (:iid, :lid, :qty, :cost)
                    ON DUPLICATE KEY UPDATE quantity_on_hand = VALUES(quantity_on_hand), average_cost = VALUES(average_cost)
                ")->execute([':iid' => $ingredientId, ':lid' => $destLocId, ':qty' => $dstBalAfter, ':cost' => $unitCost]);

                $db->prepare("INSERT INTO stock_transfer_items (stock_transfer_id, ingredient_id, quantity, unit) VALUES (:tid, :iid, :qty, :unit)")
                   ->execute([':tid' => $transferId, ':iid' => $ingredientId, ':qty' => $quantity, ':unit' => $ing['base_unit']]);

                // Insert transaction ledger entries (TRANSFER_OUT & TRANSFER_IN)
                $db->prepare("
                    INSERT INTO inventory_transactions (branch_id, ingredient_id, location_id, type, quantity, unit_cost, total_cost, balance_before, balance_after, reference_type, reference_id, created_by, notes, created_at)
                    VALUES (:bid, :iid, :slid, 'TRANSFER_OUT', :qty, :cost, :tcost, :bbef, :baft, 'stock_transfers', :ref_id, :uid, :notes, NOW())
                ")->execute([
                    ':bid' => $ing['branch_id'] ?? 1, ':iid' => $ingredientId, ':slid' => $sourceLocId,
                    ':qty' => -$quantity, ':cost' => $unitCost, ':tcost' => $totalCost,
                    ':bbef' => $srcBalBefore, ':baft' => $srcBalAfter, ':ref_id' => $transferId,
                    ':uid' => $userId, ':notes' => "Transferred to Location #{$destLocId} ({$tNum})"
                ]);

                $db->prepare("
                    INSERT INTO inventory_transactions (branch_id, ingredient_id, location_id, type, quantity, unit_cost, total_cost, balance_before, balance_after, reference_type, reference_id, created_by, notes, created_at)
                    VALUES (:bid, :iid, :dlid, 'TRANSFER_IN', :qty, :cost, :tcost, :bbef, :baft, 'stock_transfers', :ref_id, :uid, :notes, NOW())
                ")->execute([
                    ':bid' => $ing['branch_id'] ?? 1, ':iid' => $ingredientId, ':dlid' => $destLocId,
                    ':qty' => $quantity, ':cost' => $unitCost, ':tcost' => $totalCost,
                    ':bbef' => $dstBalBefore, ':baft' => $dstBalAfter, ':ref_id' => $transferId,
                    ':uid' => $userId, ':notes' => "Transferred from Location #{$sourceLocId} ({$tNum})"
                ]);
            }

            AuditLogger::log('STOCK_TRANSFERRED', 'stock_transfers', $transferId, null, "Stock transfer {$tNum} completed", $userId);

            $db->commit();
            return ['success' => true, 'transfer_id' => $transferId, 'transfer_number' => $tNum];

        } catch (Exception $e) {
            if ($db && $db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }
    }

    public static function getTransactionLedger(int $branchId = 1, ?int $ingredientId = null, int $limit = 100): array {
        $db = Database::getConnection();
        $params = [':bid' => $branchId];

        $sql = "
            SELECT 
                tx.*,
                i.name AS ingredient_name,
                i.base_unit,
                l.name AS location_name,
                u.name AS user_name
            FROM inventory_transactions tx
            JOIN ingredients i ON tx.ingredient_id = i.id
            LEFT JOIN inventory_locations l ON tx.location_id = l.id
            LEFT JOIN users u ON tx.created_by = u.id
            WHERE (tx.branch_id = :bid OR tx.branch_id IS NULL OR tx.branch_id = 1)
        ";

        if ($ingredientId) {
            $sql .= " AND tx.ingredient_id = :iid";
            $params[':iid'] = $ingredientId;
        }

        $sql .= " ORDER BY tx.created_at DESC, tx.id DESC LIMIT " . (int)$limit;

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function getTransactionsLedger(?int $ingredientId = null, ?int $locationId = null, string $type = '', int $limit = 100): array {
        return self::getTransactionLedger(1, $ingredientId, $limit);
    }

    // =========================================================================
    // 2. RECIPE BOM & COSTING SERVICE
    // =========================================================================

    public static function getRecipe(int $productId, ?int $variantId = null): array {
        $db = Database::getConnection();

        $recStmt = $db->prepare("
            SELECT r.*, p.name AS product_name, v.variant_name
            FROM recipes r
            JOIN products p ON r.product_id = p.id
            LEFT JOIN product_variants v ON r.variant_id = v.id
            WHERE r.product_id = :pid AND (r.variant_id = :vid1 OR (:vid2 IS NULL AND r.variant_id IS NULL))
            ORDER BY r.is_active DESC, r.id DESC LIMIT 1
        ");
        $recStmt->execute([':pid' => $productId, ':vid1' => $variantId, ':vid2' => $variantId]);
        $recipe = $recStmt->fetch();

        if (!$recipe) {
            return ['recipe' => null, 'items' => [], 'calculated_cost' => 0.00];
        }

        $itemsStmt = $db->prepare("
            SELECT ri.*, i.name AS ingredient_name, i.base_unit, COALESCE(i.average_cost, i.cost_per_unit, 0) AS current_ingredient_cost
            FROM recipe_items ri
            JOIN ingredients i ON ri.ingredient_id = i.id
            WHERE ri.recipe_id = :rid
        ");
        $itemsStmt->execute([':rid' => $recipe['id']]);
        $items = $itemsStmt->fetchAll();

        // Calculate live costing
        $calculatedCost = 0.00;
        foreach ($items as &$it) {
            $wastageMult = 1.0 + ((float)$it['wastage_percentage'] / 100.0);
            $effectiveQty = (float)$it['quantity'] * $wastageMult;
            $unitCost = (float)$it['current_ingredient_cost'];
            $it['line_cost'] = round($effectiveQty * $unitCost, 2);
            $calculatedCost += $it['line_cost'];
        }

        $calculatedCost = round($calculatedCost, 2);

        return [
            'recipe' => $recipe,
            'items' => $items,
            'calculated_cost' => $calculatedCost
        ];
    }

    public static function calculateRecipeCost(int $productId, ?int $variantId = null): array {
        $db = Database::getConnection();
        $recipeData = self::getRecipe($productId, $variantId);
        $recipeCost = (float)($recipeData['calculated_cost'] ?? 0.00);

        $prodPrice = 0.00;
        if ($variantId) {
            $vStmt = $db->prepare("SELECT price FROM product_variants WHERE id = :vid");
            $vStmt->execute([':vid' => $variantId]);
            $prodPrice = (float)$vStmt->fetchColumn();
        }
        if (!$prodPrice) {
            $pStmt = $db->prepare("SELECT price FROM products WHERE id = :pid");
            $pStmt->execute([':pid' => $productId]);
            $prodPrice = (float)$pStmt->fetchColumn();
        }

        $grossProfit = max(0.00, round($prodPrice - $recipeCost, 2));
        $marginPercent = ($prodPrice > 0) ? round(($grossProfit / $prodPrice) * 100.0, 2) : 0.0;

        return [
            'product_id' => $productId,
            'variant_id' => $variantId,
            'recipe_cost' => $recipeCost,
            'selling_price' => $prodPrice,
            'gross_profit' => $grossProfit,
            'profit_margin_percent' => $marginPercent,
            'items' => $recipeData['items'] ?? []
        ];
    }

    public static function getRecipesList(): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT r.*, p.name AS product_name, p.price AS product_price, v.variant_name, v.price AS variant_price,
                   (SELECT COUNT(*) FROM recipe_items WHERE recipe_id = r.id) AS ingredient_count
            FROM recipes r
            JOIN products p ON r.product_id = p.id
            LEFT JOIN product_variants v ON r.variant_id = v.id
            WHERE r.is_active = 1
            ORDER BY p.name ASC
        ");
        $stmt->execute();
        $recipes = $stmt->fetchAll();

        foreach ($recipes as &$r) {
            $r['costing'] = self::calculateRecipeCost((int)$r['product_id'], !empty($r['variant_id']) ? (int)$r['variant_id'] : null);
        }

        return $recipes;
    }

    public static function saveRecipe(array $data, int $userId): array {
        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            $productId = (int)($data['product_id'] ?? 0);
            $variantId = !empty($data['variant_id']) ? (int)$data['variant_id'] : null;
            $yieldQty = (float)($data['yield_quantity'] ?? 1.0);
            $yieldUnit = trim($data['yield_unit'] ?? 'portion');
            $items = $data['items'] ?? [];

            if (!$productId) {
                throw new Exception("product_id is required for recipe BOM.");
            }

            if (empty($items)) {
                throw new Exception("Recipe must contain at least one ingredient line item.");
            }

            // Deactivate previous active recipes for this product/variant
            $db->prepare("UPDATE recipes SET is_active = 0 WHERE product_id = :pid AND (variant_id = :vid1 OR (:vid2 IS NULL AND variant_id IS NULL))")
               ->execute([':pid' => $productId, ':vid1' => $variantId, ':vid2' => $variantId]);

            $firstIngId = (int)($items[0]['ingredient_id'] ?? 1);
            $firstQty = (float)($items[0]['quantity'] ?? 1.0);
            // Create new recipe
            $insRec = $db->prepare("
                INSERT INTO recipes (branch_id, product_id, variant_id, ingredient_id, quantity_required, name, yield_quantity, yield_unit, total_cost, is_active, created_by, created_at)
                VALUES (1, :pid, :vid, :ingid, :qreq, :name, :yqty, :yunit, 0.00, 1, :uid, NOW())
            ");
            $insRec->execute([
                ':pid' => $productId, ':vid' => $variantId, ':ingid' => $firstIngId, ':qreq' => $firstQty,
                ':name' => "Recipe for Product #{$productId}",
                ':yqty' => $yieldQty, ':yunit' => $yieldUnit, ':uid' => $userId
            ]);
            $recipeId = (int)$db->lastInsertId();

            $totalRecipeCost = 0.00;
            foreach ($items as $it) {
                $ingId = (int)($it['ingredient_id'] ?? 0);
                $qty = (float)($it['quantity'] ?? 0.0);
                $unit = trim($it['unit'] ?? 'g');
                $wastage = (float)($it['wastage_percentage'] ?? 0.0);

                if (!$ingId || $qty <= 0) continue;

                // Get ingredient current cost price
                $costStmt = $db->prepare("SELECT COALESCE(average_cost, cost_per_unit, 0) FROM ingredients WHERE id = :iid");
                $costStmt->execute([':iid' => $ingId]);
                $unitCost = (float)$costStmt->fetchColumn();

                $effectiveQty = $qty * (1.0 + ($wastage / 100.0));
                $lineCost = round($effectiveQty * $unitCost, 2);
                $totalRecipeCost += $lineCost;

                $insItem = $db->prepare("
                    INSERT INTO recipe_items (recipe_id, ingredient_id, quantity, unit, wastage_percentage, unit_cost, line_cost)
                    VALUES (:rid, :iid, :qty, :unit, :waste, :ucost, :lcost)
                ");
                $insItem->execute([
                    ':rid' => $recipeId, ':iid' => $ingId, ':qty' => $qty, ':unit' => $unit,
                    ':waste' => $wastage, ':ucost' => $unitCost, ':lcost' => $lineCost
                ]);
            }

            $totalRecipeCost = round($totalRecipeCost, 2);

            // Update recipe total cost
            $db->prepare("UPDATE recipes SET total_cost = :cost WHERE id = :rid")
               ->execute([':cost' => $totalRecipeCost, ':rid' => $recipeId]);

            AuditLogger::log('RECIPE_SAVED', 'recipes', $recipeId, null, "Recipe BOM saved for Product #{$productId} (Cost: ৳{$totalRecipeCost})", $userId);

            $db->commit();
            return [
                'success' => true,
                'recipe_id' => $recipeId,
                'product_id' => $productId,
                'total_cost' => $totalRecipeCost
            ];

        } catch (Exception $e) {
            if ($db && $db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }
    }

    // =========================================================================
    // 3. SUPPLIER & PURCHASE ORDER SERVICE
    // =========================================================================

    public static function getSuppliers(int $branchId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM suppliers WHERE (branch_id = :bid OR branch_id = 1) AND status = 'ACTIVE' ORDER BY name ASC");
        $stmt->execute([':bid' => $branchId]);
        return $stmt->fetchAll();
    }

    public static function saveSupplier(array $data, int $userId): array {
        $db = Database::getConnection();
        $id = (int)($data['id'] ?? 0);
        $branchId = (int)($data['branch_id'] ?? 1);
        $name = trim($data['name'] ?? '');
        $code = trim($data['supplier_code'] ?? ('SUP-' . strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $name), 0, 4)) . '-' . rand(100, 999)));
        $contact = trim($data['contact_person'] ?? '');
        $email = trim($data['email'] ?? '');
        $phone = trim($data['phone'] ?? '');
        $address = trim($data['address'] ?? '');

        if (empty($name)) {
            throw new Exception("Supplier name is required.");
        }

        if ($id > 0) {
            $stmt = $db->prepare("
                UPDATE suppliers SET name = :name, contact_person = :contact, email = :email, phone = :phone, address = :addr WHERE id = :id
            ");
            $stmt->execute([':name' => $name, ':contact' => $contact, ':email' => $email, ':phone' => $phone, ':addr' => $address, ':id' => $id]);
            return ['success' => true, 'id' => $id, 'name' => $name];
        } else {
            $stmt = $db->prepare("
                INSERT INTO suppliers (branch_id, supplier_code, name, contact_person, email, phone, address, status, created_at)
                VALUES (:bid, :code, :name, :contact, :email, :phone, :addr, 'ACTIVE', NOW())
            ");
            $stmt->execute([':bid' => $branchId, ':code' => $code, ':name' => $name, ':contact' => $contact, ':email' => $email, ':phone' => $phone, ':addr' => $address]);
            $newId = (int)$db->lastInsertId();
            return ['success' => true, 'id' => $newId, 'name' => $name];
        }
    }

    public static function getPurchaseOrder(int $id): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT po.*, s.name AS supplier_name, u.name AS creator_name
            FROM purchase_orders po
            JOIN suppliers s ON po.supplier_id = s.id
            LEFT JOIN users u ON po.created_by = u.id
            WHERE po.id = :id
        ");
        $stmt->execute([':id' => $id]);
        $po = $stmt->fetch();
        if (!$po) return null;

        $itemStmt = $db->prepare("
            SELECT poi.*, i.name AS ingredient_name, i.base_unit
            FROM purchase_order_items poi
            JOIN ingredients i ON poi.ingredient_id = i.id
            WHERE poi.purchase_order_id = :poid
        ");
        $itemStmt->execute([':poid' => $id]);
        $po['items'] = $itemStmt->fetchAll();
        return $po;
    }

    public static function getPurchaseOrders(int $branchId, string $statusFilter = ''): array {
        $db = Database::getConnection();
        $params = [':bid' => $branchId];

        $sql = "
            SELECT po.*, s.name AS supplier_name, u.name AS creator_name
            FROM purchase_orders po
            JOIN suppliers s ON po.supplier_id = s.id
            LEFT JOIN users u ON po.created_by = u.id
            WHERE (po.branch_id = :bid OR po.branch_id = 1)
        ";

        if (!empty($statusFilter)) {
            $sql .= " AND po.status = :st";
            $params[':st'] = $statusFilter;
        }

        $sql .= " ORDER BY po.id DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $pos = $stmt->fetchAll();

        foreach ($pos as &$po) {
            $itemStmt = $db->prepare("
                SELECT poi.*, i.name AS ingredient_name, i.base_unit
                FROM purchase_order_items poi
                JOIN ingredients i ON poi.ingredient_id = i.id
                WHERE poi.purchase_order_id = :poid
            ");
            $itemStmt->execute([':poid' => $po['id']]);
            $po['items'] = $itemStmt->fetchAll();
        }

        return $pos;
    }

    public static function createPurchaseOrder(array $data, int $userId): array {
        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            $branchId = (int)($data['branch_id'] ?? 1);
            $supplierId = (int)($data['supplier_id'] ?? 0);
            $notes = trim($data['notes'] ?? '');
            $items = $data['items'] ?? [];

            if (!$supplierId) {
                throw new Exception("Supplier ID is required for purchase order.");
            }

            if (empty($items)) {
                throw new Exception("Purchase order must contain at least one line item.");
            }

            $poNum = $data['po_number'] ?? null;
            if (empty($poNum)) {
                $count = 0;
                do {
                    $poNum = 'PO-' . date('Ymd') . '-' . sprintf('%04d', rand(1000, 9999)) . ($count > 0 ? "-{$count}" : '');
                    $checkStmt = $db->prepare("SELECT id FROM purchase_orders WHERE po_number = :ponum");
                    $checkStmt->execute([':ponum' => $poNum]);
                    $exists = $checkStmt->fetchColumn();
                    $count++;
                } while ($exists);
            }
            $subtotal = 0.00;

            $insPO = $db->prepare("
                INSERT INTO purchase_orders (branch_id, po_number, supplier_id, status, subtotal, grand_total, created_by, notes, created_at)
                VALUES (:bid, :ponum, :sid, 'DRAFT', 0.00, 0.00, :uid, :notes, NOW())
            ");
            $insPO->execute([':bid' => $branchId, ':ponum' => $poNum, ':sid' => $supplierId, ':uid' => $userId, ':notes' => $notes]);
            $poId = (int)$db->lastInsertId();

            foreach ($items as $it) {
                $ingId = (int)($it['ingredient_id'] ?? 0);
                $qty = (float)($it['ordered_quantity'] ?? ($it['quantity'] ?? 0.0));
                $price = (float)($it['unit_price'] ?? 0.0);
                $unit = trim($it['unit'] ?? 'kg');

                if (!$ingId || $qty <= 0) continue;

                $lineTotal = round($qty * $price, 2);
                $subtotal += $lineTotal;

                $insItem = $db->prepare("
                    INSERT INTO purchase_order_items (purchase_order_id, ingredient_id, ordered_quantity, unit, unit_price, line_total)
                    VALUES (:poid, :iid, :oqty, :unit, :price, :ltotal)
                ");
                $insItem->execute([':poid' => $poId, ':iid' => $ingId, ':oqty' => $qty, ':unit' => $unit, ':price' => $price, ':ltotal' => $lineTotal]);
            }

            $grandTotal = round($subtotal, 2);
            $db->prepare("UPDATE purchase_orders SET subtotal = :tot1, grand_total = :tot2 WHERE id = :poid")->execute([':tot1' => $grandTotal, ':tot2' => $grandTotal, ':poid' => $poId]);

            AuditLogger::log('PURCHASE_ORDER_CREATED', 'purchase_orders', $poId, null, "PO #{$poNum} created (Grand Total: ৳{$grandTotal})", $userId);

            $db->commit();
            return ['success' => true, 'po_id' => $poId, 'po_number' => $poNum, 'grand_total' => $grandTotal];

        } catch (Exception $e) {
            if ($db && $db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }
    }

    public static function approvePurchaseOrder(int $poId, int $userId): array {
        return self::updatePOStatus($poId, 'APPROVED', $userId);
    }

    public static function updatePOStatus(int $poId, string $newStatus, int $userId): array {
        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            $stmt = $db->prepare("SELECT * FROM purchase_orders WHERE id = :id FOR UPDATE");
            $stmt->execute([':id' => $poId]);
            $po = $stmt->fetch();

            if (!$po) {
                throw new Exception("Purchase Order #{$poId} not found.");
            }

            $upStmt = $db->prepare("UPDATE purchase_orders SET status = :st WHERE id = :id");
            $upStmt->execute([':st' => $newStatus, ':id' => $poId]);

            AuditLogger::log('PURCHASE_ORDER_STATUS', 'purchase_orders', $poId, null, "PO #{$po['po_number']} status updated to {$newStatus}", $userId);

            $db->commit();
            return ['success' => true, 'po_id' => $poId, 'status' => $newStatus];

        } catch (Exception $e) {
            if ($db && $db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Process Goods Receiving for PO, update stock balances & average costs
     */
    public static function receiveGoods(int $poId, int $locationId, array $receivedItems, int $userId, string $notes = ''): array {
        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            $poStmt = $db->prepare("SELECT * FROM purchase_orders WHERE id = :id FOR UPDATE");
            $poStmt->execute([':id' => $poId]);
            $po = $poStmt->fetch();

            if (!$po) {
                throw new Exception("Purchase Order #{$poId} not found.");
            }

            if (in_array($po['status'], ['RECEIVED', 'CLOSED', 'CANCELLED'])) {
                throw new Exception("Purchase Order #{$po['po_number']} is in status '{$po['status']}' and cannot accept receiving.");
            }

            $rcpNum = 'GRN-' . date('Ymd') . '-' . sprintf('%04d', rand(1000, 9999));
            $insReceipt = $db->prepare("
                INSERT INTO goods_receipts (purchase_order_id, receipt_number, supplier_id, received_by, notes, received_at)
                VALUES (:poid, :rcpnum, :sid, :uid, :notes, NOW())
            ");
            $insReceipt->execute([':poid' => $poId, ':rcpnum' => $rcpNum, ':sid' => $po['supplier_id'], ':uid' => $userId, ':notes' => $notes]);
            $receiptId = (int)$db->lastInsertId();

            $allReceived = true;

            foreach ($receivedItems as $item) {
                $ingId = (int)($item['ingredient_id'] ?? 0);
                $recvQty = (float)($item['received_quantity'] ?? 0.0);

                if (!$ingId || $recvQty <= 0) continue;

                // Lock PO Item row
                $poiStmt = $db->prepare("SELECT * FROM purchase_order_items WHERE purchase_order_id = :poid AND ingredient_id = :iid FOR UPDATE");
                $poiStmt->execute([':poid' => $poId, ':iid' => $ingId]);
                $poi = $poiStmt->fetch();

                if (!$poi) continue;

                $newReceivedQty = (float)$poi['received_quantity'] + $recvQty;
                $unitPrice = (float)$poi['unit_price'];

                if ($newReceivedQty < (float)$poi['ordered_quantity']) {
                    $allReceived = false;
                }

                // Update PO Item received quantity
                $db->prepare("UPDATE purchase_order_items SET received_quantity = :rqty WHERE id = :poiid")
                   ->execute([':rqty' => $newReceivedQty, ':poiid' => $poi['id']]);

                // Record Goods Receipt Item
                $lineCost = round($recvQty * $unitPrice, 2);
                $db->prepare("
                    INSERT INTO goods_receipt_items (goods_receipt_id, ingredient_id, location_id, received_quantity, unit_price, total_cost)
                    VALUES (:grId, :iid, :lid, :rqty, :uprice, :tcost)
                ")->execute([':grId' => $receiptId, ':iid' => $ingId, ':lid' => $locationId, ':rqty' => $recvQty, ':uprice' => $unitPrice, ':tcost' => $lineCost]);

                // Update Ingredient Weighted Average Cost & Stock
                $ingStmt = $db->prepare("SELECT * FROM ingredients WHERE id = :id FOR UPDATE");
                $ingStmt->execute([':id' => $ingId]);
                $ing = $ingStmt->fetch();

                $stStmt = $db->prepare("SELECT * FROM inventory_stock WHERE ingredient_id = :iid AND location_id = :lid FOR UPDATE");
                $stStmt->execute([':iid' => $ingId, ':lid' => $locationId]);
                $st = $stStmt->fetch();

                $oldQty = (float)($st['quantity_on_hand'] ?? $ing['current_stock'] ?? 0.0);
                $oldAvgCost = (float)($ing['average_cost'] ?? $ing['cost_per_unit'] ?? 0.0);
                $newQty = $oldQty + $recvQty;

                $newAvgCost = ($newQty > 0) ? round((($oldQty * $oldAvgCost) + ($recvQty * $unitPrice)) / $newQty, 2) : $unitPrice;

                // Update inventory_stock
                $db->prepare("
                    INSERT INTO inventory_stock (ingredient_id, location_id, quantity_on_hand, average_cost, last_purchase_cost)
                    VALUES (:iid, :lid, :qty, :avgc, :lcost)
                    ON DUPLICATE KEY UPDATE quantity_on_hand = VALUES(quantity_on_hand), average_cost = VALUES(average_cost), last_purchase_cost = VALUES(last_purchase_cost)
                ")->execute([':iid' => $ingId, ':lid' => $locationId, ':qty' => $newQty, ':avgc' => $newAvgCost, ':lcost' => $unitPrice]);

                // Update master ingredient
                $db->prepare("
                    UPDATE ingredients 
                    SET current_stock = (SELECT COALESCE(SUM(quantity_on_hand), 0) FROM inventory_stock WHERE ingredient_id = :iid1),
                        average_cost = :avgc1, cost_per_unit = :avgc2, last_purchase_cost = :lcost
                    WHERE id = :iid2
                ")->execute([':avgc1' => $newAvgCost, ':avgc2' => $newAvgCost, ':lcost' => $unitPrice, ':iid1' => $ingId, ':iid2' => $ingId]);

                // Insert ledger record
                $db->prepare("
                    INSERT INTO inventory_transactions 
                    (branch_id, ingredient_id, location_id, type, quantity, unit_cost, total_cost, balance_before, balance_after, reference_type, reference_id, created_by, notes, created_at)
                    VALUES (:bid, :iid, :lid, 'PURCHASE_RECEIPT', :qty, :cost, :tcost, :bbef, :baft, 'goods_receipts', :ref_id, :uid, :notes, NOW())
                ")->execute([
                    ':bid' => $po['branch_id'], ':iid' => $ingId, ':lid' => $locationId,
                    ':qty' => $recvQty, ':cost' => $unitPrice, ':tcost' => $lineCost,
                    ':bbef' => $oldQty, ':baft' => $newQty, ':ref_id' => $receiptId,
                    ':uid' => $userId, ':notes' => "Received GRN #{$rcpNum} for PO #{$po['po_number']}"
                ]);
            }

            $newPoStatus = $allReceived ? 'RECEIVED' : 'PARTIALLY_RECEIVED';
            $db->prepare("UPDATE purchase_orders SET status = :st WHERE id = :poid")->execute([':st' => $newPoStatus, ':poid' => $poId]);

            AuditLogger::log('GOODS_RECEIVED', 'goods_receipts', $receiptId, null, "Goods Receipt #{$rcpNum} processed for PO #{$po['po_number']}", $userId);

            $db->commit();
            return [
                'success' => true,
                'receipt_id' => $receiptId,
                'receipt_number' => $rcpNum,
                'po_status' => $newPoStatus
            ];

        } catch (Exception $e) {
            if ($db && $db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }
    }

    // =========================================================================
    // 4. AUTOMATED ORDER KDS PRODUCTION CONSUMPTION SERVICE
    // =========================================================================

    /**
     * Consume inventory stock for line items in an order ticket (Idempotent & Recipe-Based)
     */
    public static function consumeForTicket(int $ticketId, int $userId = 1): array {
        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            $tktStmt = $db->prepare("SELECT * FROM order_tickets WHERE id = :tid FOR UPDATE");
            $tktStmt->execute([':tid' => $ticketId]);
            $ticket = $tktStmt->fetch();

            if (!$ticket) {
                throw new Exception("Ticket #{$ticketId} not found.");
            }

            // Get ticket items
            $itemStmt = $db->prepare("
                SELECT oti.*, oi.product_id, oi.variant_id, oi.order_id 
                FROM order_ticket_items oti
                JOIN order_items oi ON oti.order_item_id = oi.id
                WHERE oti.order_ticket_id = :tid AND oti.item_status != 'CANCELLED'
            ");
            $itemStmt->execute([':tid' => $ticketId]);
            $ticketItems = $itemStmt->fetchAll();

            $consumedCount = 0;

            foreach ($ticketItems as $tItem) {
                $orderItemId = (int)$tItem['order_item_id'];

                // 1. Idempotency Check: Check if this order item was already consumed for this ticket
                $checkStmt = $db->prepare("SELECT id FROM order_item_consumptions WHERE order_item_id = :oiid AND order_ticket_id = :tid AND status = 'CONSUMED'");
                $checkStmt->execute([':oiid' => $orderItemId, ':tid' => $ticketId]);
                if ($checkStmt->fetchColumn()) {
                    continue; // Already consumed safely
                }

                // 2. Fetch active recipe BOM for product/variant
                $recipeData = self::getRecipe((int)$tItem['product_id'], !empty($tItem['variant_id']) ? (int)$tItem['variant_id'] : null);
                $recipe = $recipeData['recipe'];
                $recipeItems = $recipeData['items'] ?? [];

                if (!$recipe || empty($recipeItems)) {
                    // No recipe configured for product - record consumption marker without deducting stock
                    $db->prepare("INSERT INTO order_item_consumptions (order_item_id, order_ticket_id, recipe_id, status, created_at) VALUES (:oiid, :tid, NULL, 'CONSUMED', NOW())")
                       ->execute([':oiid' => $orderItemId, ':tid' => $ticketId]);
                    continue;
                }

                // 3. Deduct stock for each recipe ingredient
                // Location: Station 2 (Bar) for Bar items, Location 2 (Kitchen Store) or Location 1 (Main Store) for others
                $targetLocationId = ($ticket['station_id'] == 2) ? 2 : 1;

                foreach ($recipeItems as $rItem) {
                    $ingId = (int)$rItem['ingredient_id'];
                    $wasteMult = 1.0 + ((float)$rItem['wastage_percentage'] / 100.0);
                    $deductQty = round((float)$rItem['quantity'] * $wasteMult * (int)$tItem['quantity'], 4);

                    if ($deductQty <= 0) continue;

                    // Lock ingredient & stock row
                    $ingStmt = $db->prepare("SELECT * FROM ingredients WHERE id = :id FOR UPDATE");
                    $ingStmt->execute([':id' => $ingId]);
                    $ing = $ingStmt->fetch();

                    if (!$ing) continue;

                    $stStmt = $db->prepare("SELECT * FROM inventory_stock WHERE ingredient_id = :iid AND location_id = :lid FOR UPDATE");
                    $stStmt->execute([':iid' => $ingId, ':lid' => $targetLocationId]);
                    $st = $stStmt->fetch();

                    $balBefore = (float)($st['quantity_on_hand'] ?? $ing['current_stock'] ?? 0.0);
                    $balAfter = $balBefore - $deductQty;
                    $unitCost = (float)($ing['average_cost'] ?? $ing['cost_per_unit'] ?? 0.0);
                    $totalCost = round($deductQty * $unitCost, 2);

                    // Update stock quantity
                    $db->prepare("
                        INSERT INTO inventory_stock (ingredient_id, location_id, quantity_on_hand, average_cost)
                        VALUES (:iid, :lid, :qty, :cost)
                        ON DUPLICATE KEY UPDATE quantity_on_hand = VALUES(quantity_on_hand)
                    ")->execute([':iid' => $ingId, ':lid' => $targetLocationId, ':qty' => $balAfter, ':cost' => $unitCost]);

                    $db->prepare("UPDATE ingredients SET current_stock = (SELECT COALESCE(SUM(quantity_on_hand), 0) FROM inventory_stock WHERE ingredient_id = :iid1) WHERE id = :iid2")
                       ->execute([':iid1' => $ingId, ':iid2' => $ingId]);

                    // Insert transaction ledger record
                    $db->prepare("
                        INSERT INTO inventory_transactions 
                        (branch_id, ingredient_id, location_id, type, quantity, unit_cost, total_cost, balance_before, balance_after, reference_type, reference_id, created_by, notes, created_at)
                        VALUES (:bid, :iid, :lid, 'ORDER_CONSUMPTION', :qty, :cost, :tcost, :bbef, :baft, 'order_items', :ref_id, :uid, :notes, NOW())
                    ")->execute([
                        ':bid' => $ing['branch_id'] ?? 1, ':iid' => $ingId, ':lid' => $targetLocationId,
                        ':qty' => -$deductQty, ':cost' => $unitCost, ':tcost' => $totalCost,
                        ':bbef' => $balBefore, ':baft' => $balAfter, ':ref_id' => $orderItemId,
                        ':uid' => $userId, ':notes' => "Recipe consumption for Ticket #{$ticket['ticket_number']} ({$tItem['quantity']}x {$tItem['product_name']})"
                    ]);
                }

                // Insert idempotency tracking record
                $db->prepare("INSERT INTO order_item_consumptions (order_item_id, order_ticket_id, recipe_id, status, created_at) VALUES (:oiid, :tid, :rid, 'CONSUMED', NOW())")
                   ->execute([':oiid' => $orderItemId, ':tid' => $ticketId, ':rid' => $recipe['id']]);

                $consumedCount++;
            }

            $db->commit();
            return ['success' => true, 'ticket_id' => $ticketId, 'consumed_items_count' => $consumedCount];

        } catch (Exception $e) {
            if ($db && $db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Reverse inventory consumption when an order is refunded/cancelled
     */
    public static function reverseOrderConsumption(int $orderId, int $userId = 1, string $reason = 'Order Refund'): array {
        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            $cStmt = $db->prepare("
                SELECT c.*, oi.product_id, oi.quantity AS item_qty
                FROM order_item_consumptions c
                JOIN order_items oi ON c.order_item_id = oi.id
                WHERE oi.order_id = :oid AND c.status = 'CONSUMED'
                FOR UPDATE
            ");
            $cStmt->execute([':oid' => $orderId]);
            $consumptions = $cStmt->fetchAll();

            $reversedCount = 0;

            foreach ($consumptions as $c) {
                if (empty($c['recipe_id'])) {
                    $db->prepare("UPDATE order_item_consumptions SET status = 'REVERSED' WHERE id = :cid")->execute([':cid' => $c['id']]);
                    continue;
                }

                $itemsStmt = $db->prepare("SELECT * FROM recipe_items WHERE recipe_id = :rid");
                $itemsStmt->execute([':rid' => $c['recipe_id']]);
                $recipeItems = $itemsStmt->fetchAll();

                foreach ($recipeItems as $rItem) {
                    $ingId = (int)$rItem['ingredient_id'];
                    $wasteMult = 1.0 + ((float)$rItem['wastage_percentage'] / 100.0);
                    $restoreQty = round((float)$rItem['quantity'] * $wasteMult * (int)$c['item_qty'], 4);

                    if ($restoreQty <= 0) continue;

                    $ingStmt = $db->prepare("SELECT * FROM ingredients WHERE id = :id FOR UPDATE");
                    $ingStmt->execute([':id' => $ingId]);
                    $ing = $ingStmt->fetch();

                    if (!$ing) continue;

                    $stStmt = $db->prepare("SELECT * FROM inventory_stock WHERE ingredient_id = :iid AND location_id = 1 FOR UPDATE");
                    $stStmt->execute([':iid' => $ingId]);
                    $st = $stStmt->fetch();

                    $balBefore = (float)($st['quantity_on_hand'] ?? $ing['current_stock'] ?? 0.0);
                    $balAfter = $balBefore + $restoreQty;
                    $unitCost = (float)($ing['average_cost'] ?? $ing['cost_per_unit'] ?? 0.0);

                    $db->prepare("UPDATE inventory_stock SET quantity_on_hand = :qty WHERE ingredient_id = :iid AND location_id = 1")
                       ->execute([':qty' => $balAfter, ':iid' => $ingId]);

                    $db->prepare("UPDATE ingredients SET current_stock = (SELECT COALESCE(SUM(quantity_on_hand), 0) FROM inventory_stock WHERE ingredient_id = :iid1) WHERE id = :iid2")
                       ->execute([':iid1' => $ingId, ':iid2' => $ingId]);

                    $db->prepare("
                        INSERT INTO inventory_transactions 
                        (branch_id, ingredient_id, location_id, type, quantity, unit_cost, total_cost, balance_before, balance_after, reference_type, reference_id, created_by, notes, created_at)
                        VALUES (:bid, :iid, 1, 'REVERSAL', :qty, :cost, :tcost, :bbef, :baft, 'orders', :ref_id, :uid, :notes, NOW())
                    ")->execute([
                        ':bid' => $ing['branch_id'] ?? 1, ':iid' => $ingId,
                        ':qty' => $restoreQty, ':cost' => $unitCost, ':tcost' => round($restoreQty * $unitCost, 2),
                        ':bbef' => $balBefore, ':baft' => $balAfter, ':ref_id' => $orderId,
                        ':uid' => $userId, ':notes' => "Reversal for Order #{$orderId} ({$reason})"
                    ]);
                }

                $db->prepare("UPDATE order_item_consumptions SET status = 'REVERSED' WHERE id = :cid")->execute([':cid' => $c['id']]);
                $reversedCount++;
            }

            AuditLogger::log('INVENTORY_REVERSED', 'orders', $orderId, null, "Inventory consumption reversed for Order #{$orderId} (Reason: {$reason})", $userId);

            $db->commit();
            return ['success' => true, 'order_id' => $orderId, 'reversed_count' => $reversedCount];

        } catch (Exception $e) {
            if ($db && $db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }
    }
}
