<?php
/**
 * SMARTRESTA Core Production Order Engine
 * Complete Order Lifecycle, Item & Modifier Snapshots, Calculations, State Machine & Audit History
 */

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/AuditLogger.php';
require_once __DIR__ . '/MenuEngine.php';

class OrderEngine {

    public static function generateOrderNumber(int $branchId = 1): string {
        $prefix = sprintf('ORD-B%d-%s-', $branchId, date('Ymd'));
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) FROM orders WHERE order_number LIKE :prefix");
        $stmt->execute(['prefix' => $prefix . '%']);
        $count = (int)$stmt->fetchColumn() + 1;
        return $prefix . sprintf('%04d', $count);
    }

    public static function createDraftOrder(array $data, int $userId = 1): array {
        $db = Database::getConnection();
        $branchId = !empty($data['branch_id']) ? (int)$data['branch_id'] : 1;
        $orderType = !empty($data['order_type']) ? strtoupper(trim($data['order_type'])) : 'DINE_IN';
        $session_id = !empty($data['dining_session_id']) ? (int)$data['dining_session_id'] : null;
        $table_id = !empty($data['table_id']) ? (int)$data['table_id'] : null;
        $customer_id = !empty($data['customer_id']) ? (int)$data['customer_id'] : null;
        $notes = !empty($data['notes']) ? trim($data['notes']) : null;

        // DINE_IN validation
        if ($orderType === 'DINE_IN' && $table_id) {
            $stmtSession = $db->prepare("
                SELECT ds.id 
                FROM dining_sessions ds 
                WHERE ds.table_id = :tid AND ds.status = 'OPEN' 
                ORDER BY ds.id DESC LIMIT 1
            ");
            $stmtSession->execute(['tid' => $table_id]);
            $foundSession = $stmtSession->fetchColumn();
            if ($foundSession) {
                $session_id = (int)$foundSession;
            }
        }

        $db->beginTransaction();
        try {
            $orderNumber = self::generateOrderNumber($branchId);

            $stmt = $db->prepare("
                INSERT INTO orders (
                    order_number, branch_id, dining_session_id, table_id, customer_id,
                    taken_by_user_id, order_type, subtotal, discount, tax, service_charge,
                    delivery_charge, total, payment_status, order_status, notes
                ) VALUES (
                    :order_number, :branch_id, :dining_session_id, :table_id, :customer_id,
                    :taken_by_user_id, :order_type, 0.00, 0.00, 0.00, 0.00,
                    0.00, 0.00, 'UNPAID', 'DRAFT', :notes
                )
            ");
            $stmt->execute([
                'order_number' => $orderNumber,
                'branch_id' => $branchId,
                'dining_session_id' => $session_id,
                'table_id' => $table_id,
                'customer_id' => $customer_id,
                'taken_by_user_id' => $userId,
                'order_type' => $orderType,
                'notes' => $notes
            ]);
            $orderId = (int)$db->lastInsertId();

            // Status history
            $stmtH = $db->prepare("
                INSERT INTO order_status_history (order_id, previous_status, new_status, changed_by_user_id, notes)
                VALUES (:order_id, NULL, 'DRAFT', :user_id, 'Draft order initialized')
            ");
            $stmtH->execute(['order_id' => $orderId, 'user_id' => $userId]);

            AuditLogger::log($userId, 'ORDER_CREATED', 'order', $orderId, null, [
                'order_number' => $orderNumber, 'order_type' => $orderType
            ]);

            $db->commit();
            return [
                'order_id' => $orderId,
                'order_number' => $orderNumber,
                'dining_session_id' => $session_id,
                'table_id' => $table_id,
                'status' => 'DRAFT'
            ];

        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function addItemToOrder(
        int $orderId,
        int $productId,
        ?int $variantId = null,
        array $selectedModifierIds = [],
        int $quantity = 1,
        ?string $notes = null,
        int $userId = 1
    ): array {
        $db = Database::getConnection();
        if ($quantity <= 0) {
            throw new Exception("Item quantity must be greater than zero.");
        }

        // Verify Order
        $stmtOrd = $db->prepare("SELECT id, order_status FROM orders WHERE id = :id");
        $stmtOrd->execute(['id' => $orderId]);
        $order = $stmtOrd->fetch(PDO::FETCH_ASSOC);
        if (!$order) {
            throw new Exception("Order #{$orderId} not found.");
        }
        if (in_array($order['order_status'], ['COMPLETED', 'CANCELLED', 'REFUNDED'], true)) {
            throw new Exception("Cannot add items to an order with status {$order['order_status']}.");
        }

        // Server-Side Deterministic Pricing Calculation via MenuEngine
        $priceCalc = MenuEngine::calculateEffectivePrice($productId, $variantId, $selectedModifierIds);

        // Fetch product station routing metadata & SKU
        $stmtProd = $db->prepare("SELECT default_station_id, sku FROM products WHERE id = :id");
        $stmtProd->execute(['id' => $productId]);
        $prodMeta = $stmtProd->fetch(PDO::FETCH_ASSOC);
        $stationId = (int)($prodMeta['default_station_id'] ?? 1);
        $sku = $prodMeta['sku'] ?? null;

        $unitPrice = $priceCalc['effective_variant_price'];
        $modifierTotal = $priceCalc['modifier_total'];
        $lineUnitPrice = $priceCalc['final_unit_price'];
        $lineSubtotal = $lineUnitPrice * $quantity;

        $db->beginTransaction();
        try {
            // Insert into order_items with snapshots
            $stmtItem = $db->prepare("
                INSERT INTO order_items (
                    order_id, product_id, variant_id, counter_id, item_name, variant_name,
                    sku, quantity, unit_price, modifier_total, subtotal, status, notes
                ) VALUES (
                    :order_id, :product_id, :variant_id, :counter_id, :item_name, :variant_name,
                    :sku, :quantity, :unit_price, :modifier_total, :subtotal, 'PENDING', :notes
                )
            ");
            $stmtItem->execute([
                'order_id' => $orderId,
                'product_id' => $productId,
                'variant_id' => $variantId,
                'counter_id' => $stationId,
                'item_name' => $priceCalc['product_name'],
                'variant_name' => $priceCalc['variant_name'],
                'sku' => $sku,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'modifier_total' => $modifierTotal,
                'subtotal' => $lineSubtotal,
                'notes' => $notes ? trim($notes) : null
            ]);
            $orderItemId = (int)$db->lastInsertId();

            // Insert modifier snapshots
            if (!empty($priceCalc['modifiers'])) {
                $stmtModSnap = $db->prepare("
                    INSERT INTO order_item_modifiers (order_item_id, modifier_id, modifier_name, unit_price, quantity, subtotal)
                    VALUES (:order_item_id, :modifier_id, :modifier_name, :unit_price, :quantity, :subtotal)
                ");
                foreach ($priceCalc['modifiers'] as $mod) {
                    $modSubtotal = $mod['price'] * $quantity;
                    $stmtModSnap->execute([
                        'order_item_id' => $orderItemId,
                        'modifier_id' => $mod['id'],
                        'modifier_name' => $mod['name'],
                        'unit_price' => $mod['price'],
                        'quantity' => $quantity,
                        'subtotal' => $modSubtotal
                    ]);
                }
            }

            self::recalculateOrderTotals($orderId);

            AuditLogger::log($userId, 'ORDER_ITEM_ADDED', 'order_item', $orderItemId, null, [
                'order_id' => $orderId, 'item' => $priceCalc['product_name'], 'quantity' => $quantity, 'subtotal' => $lineSubtotal
            ]);

            $db->commit();
            return ['order_item_id' => $orderItemId, 'order_id' => $orderId, 'line_total' => $lineSubtotal];

        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function updateItemQuantity(int $orderId, int $orderItemId, int $quantity, int $userId = 1): bool {
        $db = Database::getConnection();
        if ($quantity <= 0) {
            return self::removeItemFromOrder($orderId, $orderItemId, $userId);
        }

        $stmtItem = $db->prepare("
            SELECT unit_price, modifier_total FROM order_items 
            WHERE id = :id AND order_id = :order_id
        ");
        $stmtItem->execute(['id' => $orderItemId, 'order_id' => $orderId]);
        $item = $stmtItem->fetch(PDO::FETCH_ASSOC);
        if (!$item) {
            throw new Exception("Order item not found.");
        }

        $unitPrice = (float)$item['unit_price'];
        $modTotal = (float)$item['modifier_total'];
        $newSubtotal = ($unitPrice + $modTotal) * $quantity;

        $db->beginTransaction();
        try {
            $stmtUpd = $db->prepare("
                UPDATE order_items 
                SET quantity = :qty, subtotal = :subtotal 
                WHERE id = :id AND order_id = :order_id
            ");
            $stmtUpd->execute(['qty' => $quantity, 'subtotal' => $newSubtotal, 'id' => $orderItemId, 'order_id' => $orderId]);

            // Update modifier snapshot quantities
            $stmtModUpd = $db->prepare("
                UPDATE order_item_modifiers 
                SET quantity = :qty, subtotal = (unit_price * :qty) 
                WHERE order_item_id = :item_id
            ");
            $stmtModUpd->execute(['qty' => $quantity, 'item_id' => $orderItemId]);

            self::recalculateOrderTotals($orderId);

            AuditLogger::log($userId, 'ORDER_ITEM_UPDATED', 'order_item', $orderItemId, null, ['quantity' => $quantity]);
            $db->commit();
            return true;

        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function removeItemFromOrder(int $orderId, int $orderItemId, int $userId = 1): bool {
        $db = Database::getConnection();
        $db->beginTransaction();
        try {
            $stmtDelMod = $db->prepare("DELETE FROM order_item_modifiers WHERE order_item_id = :item_id");
            $stmtDelMod->execute(['item_id' => $orderItemId]);

            $stmtDel = $db->prepare("DELETE FROM order_items WHERE id = :id AND order_id = :order_id");
            $stmtDel->execute(['id' => $orderItemId, 'order_id' => $orderId]);

            self::recalculateOrderTotals($orderId);

            AuditLogger::log($userId, 'ORDER_ITEM_REMOVED', 'order_item', $orderItemId, null, ['order_id' => $orderId]);
            $db->commit();
            return true;

        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function recalculateOrderTotals(int $orderId): array {
        $db = Database::getConnection();
        $stmtSum = $db->prepare("SELECT SUM(subtotal) FROM order_items WHERE order_id = :id AND status != 'CANCELLED'");
        $stmtSum->execute(['id' => $orderId]);
        $subtotal = (float)($stmtSum->fetchColumn() ?: 0.00);

        $stmtOrd = $db->prepare("SELECT discount, service_charge, delivery_charge FROM orders WHERE id = :id");
        $stmtOrd->execute(['id' => $orderId]);
        $ord = $stmtOrd->fetch(PDO::FETCH_ASSOC);

        $discount = (float)($ord['discount'] ?? 0.00);
        $serviceCharge = (float)($ord['service_charge'] ?? 0.00);
        $deliveryCharge = (float)($ord['delivery_charge'] ?? 0.00);

        $taxableAmount = max(0, $subtotal - $discount);
        $tax = round($taxableAmount * 0.05, 2); // 5% VAT
        $total = round(($taxableAmount + $tax + $serviceCharge + $deliveryCharge), 2);

        $stmtUpd = $db->prepare("
            UPDATE orders 
            SET subtotal = :subtotal, tax = :tax, total = :total 
            WHERE id = :id
        ");
        $stmtUpd->execute([
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
            'id' => $orderId
        ]);

        return ['subtotal' => $subtotal, 'tax' => $tax, 'total' => $total];
    }

    public static function submitOrder(int $orderId, int $userId = 1): array {
        $db = Database::getConnection();
        $stmtOrd = $db->prepare("SELECT * FROM orders WHERE id = :id");
        $stmtOrd->execute(['id' => $orderId]);
        $order = $stmtOrd->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            throw new Exception("Order #{$orderId} not found.");
        }
        if (in_array($order['order_status'], ['COMPLETED', 'CANCELLED', 'REFUNDED'], true)) {
            throw new Exception("Order is already {$order['order_status']}.");
        }

        // Verify line items exist
        $stmtCount = $db->prepare("SELECT COUNT(*) FROM order_items WHERE order_id = :id AND status != 'CANCELLED'");
        $stmtCount->execute(['id' => $orderId]);
        if ((int)$stmtCount->fetchColumn() === 0) {
            throw new Exception("Cannot submit empty order with 0 items.");
        }

        $db->beginTransaction();
        try {
            // Update items to ROUTED
            $stmtItemSt = $db->prepare("UPDATE order_items SET status = 'ROUTED' WHERE order_id = :id AND status = 'PENDING'");
            $stmtItemSt->execute(['id' => $orderId]);

            $previousStatus = $order['order_status'];
            $newStatus = 'SUBMITTED';

            $stmtUpd = $db->prepare("UPDATE orders SET order_status = :status WHERE id = :id");
            $stmtUpd->execute(['status' => $newStatus, 'id' => $orderId]);

            // Insert status history
            $stmtH = $db->prepare("
                INSERT INTO order_status_history (order_id, previous_status, new_status, changed_by_user_id, notes)
                VALUES (:order_id, :prev, :new, :user_id, 'Order submitted for kitchen & counter routing')
            ");
            $stmtH->execute(['order_id' => $orderId, 'prev' => $previousStatus, 'new' => $newStatus, 'user_id' => $userId]);

            AuditLogger::log($userId, 'ORDER_SUBMITTED', 'order', $orderId, null, [
                'order_number' => $order['order_number'], 'total' => $order['total']
            ]);

            $db->commit();
            return ['order_id' => $orderId, 'order_number' => $order['order_number'], 'status' => $newStatus];

        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function changeOrderStatus(int $orderId, string $newStatus, int $userId = 1, ?string $notes = null): bool {
        $db = Database::getConnection();
        $allowedStatuses = ['DRAFT', 'SUBMITTED', 'WAITING_PAYMENT', 'CONFIRMED', 'ROUTED', 'PREPARING', 'READY', 'SERVED', 'COMPLETED', 'CANCELLED', 'REFUNDED'];
        $newStatus = strtoupper(trim($newStatus));
        if (!in_array($newStatus, $allowedStatuses, true)) {
            throw new Exception("Invalid order status '{$newStatus}' specified.");
        }

        $stmtOrd = $db->prepare("SELECT id, order_status FROM orders WHERE id = :id");
        $stmtOrd->execute(['id' => $orderId]);
        $order = $stmtOrd->fetch(PDO::FETCH_ASSOC);
        if (!$order) {
            throw new Exception("Order not found.");
        }

        $prevStatus = $order['order_status'];
        if ($prevStatus === $newStatus) return true;

        $db->beginTransaction();
        try {
            $completedAt = ($newStatus === 'COMPLETED') ? date('Y-m-d H:i:s') : null;
            $cancelledAt = ($newStatus === 'CANCELLED') ? date('Y-m-d H:i:s') : null;

            $sql = "UPDATE orders SET order_status = :new_status";
            $params = ['new_status' => $newStatus, 'id' => $orderId];
            if ($completedAt) { $sql .= ", completed_at = :cat"; $params['cat'] = $completedAt; }
            if ($cancelledAt) { $sql .= ", cancelled_at = :can"; $params['can'] = $cancelledAt; }
            $sql .= " WHERE id = :id";

            $stmtUpd = $db->prepare($sql);
            $stmtUpd->execute($params);

            // History record
            $stmtH = $db->prepare("
                INSERT INTO order_status_history (order_id, previous_status, new_status, changed_by_user_id, notes)
                VALUES (:order_id, :prev, :new, :user_id, :notes)
            ");
            $stmtH->execute([
                'order_id' => $orderId,
                'prev' => $prevStatus,
                'new' => $newStatus,
                'user_id' => $userId,
                'notes' => $notes ? trim($notes) : "Status advanced to {$newStatus}"
            ]);

            AuditLogger::log($userId, 'ORDER_STATUS_CHANGED', 'order', $orderId, null, [
                'previous' => $prevStatus, 'new' => $newStatus
            ]);

            $db->commit();
            return true;

        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function cancelOrder(int $orderId, string $reason, int $userId = 1): bool {
        if (empty(trim($reason))) {
            throw new Exception("Cancellation reason is required.");
        }
        return self::changeOrderStatus($orderId, 'CANCELLED', $userId, "Cancelled: " . trim($reason));
    }

    public static function getOrderDetails(int $orderId): ?array {
        $db = Database::getConnection();
        $stmtOrd = $db->prepare("
            SELECT o.*, b.name AS branch_name, rt.table_number, f.name AS floor_name,
                   u1.name AS taken_by_name, u2.name AS served_by_name, c.name AS customer_name
            FROM orders o
            JOIN branches b ON o.branch_id = b.id
            LEFT JOIN restaurant_tables rt ON o.table_id = rt.id
            LEFT JOIN floors f ON rt.floor_id = f.id
            LEFT JOIN users u1 ON o.taken_by_user_id = u1.id
            LEFT JOIN users u2 ON o.served_by_user_id = u2.id
            LEFT JOIN customers c ON o.customer_id = c.id
            WHERE o.id = :id
        ");
        $stmtOrd->execute(['id' => $orderId]);
        $order = $stmtOrd->fetch(PDO::FETCH_ASSOC);
        if (!$order) return null;

        // Order Items
        $stmtItems = $db->prepare("
            SELECT oi.*, s.name AS station_name
            FROM order_items oi
            LEFT JOIN stations s ON oi.counter_id = s.id
            WHERE oi.order_id = :id
            ORDER BY oi.id ASC
        ");
        $stmtItems->execute(['id' => $orderId]);
        $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

        // Modifiers for items
        foreach ($items as &$item) {
            $stmtMods = $db->prepare("SELECT * FROM order_item_modifiers WHERE order_item_id = :item_id");
            $stmtMods->execute(['item_id' => $item['id']]);
            $item['modifiers'] = $stmtMods->fetchAll(PDO::FETCH_ASSOC);
        }
        $order['items'] = $items;

        // Status history
        $stmtH = $db->prepare("
            SELECT osh.*, u.name AS changed_by_name
            FROM order_status_history osh
            LEFT JOIN users u ON osh.changed_by_user_id = u.id
            WHERE osh.order_id = :id
            ORDER BY osh.id ASC
        ");
        $stmtH->execute(['id' => $orderId]);
        $order['status_history'] = $stmtH->fetchAll(PDO::FETCH_ASSOC);

        return $order;
    }

    public static function getOrders(array $filters = []): array {
        $db = Database::getConnection();
        $sql = "
            SELECT o.id, o.order_number, o.branch_id, o.dining_session_id, o.table_id, o.order_type,
                   o.subtotal, o.tax, o.total, o.payment_status, o.order_status, o.created_at,
                   rt.table_number, u.name AS waiter_name,
                   (SELECT COUNT(*) FROM order_items oi WHERE oi.order_id = o.id) AS item_count
            FROM orders o
            LEFT JOIN restaurant_tables rt ON o.table_id = rt.id
            LEFT JOIN users u ON o.taken_by_user_id = u.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filters['branch_id'])) {
            $sql .= " AND o.branch_id = :branch_id";
            $params['branch_id'] = (int)$filters['branch_id'];
        }
        if (!empty($filters['order_status'])) {
            $sql .= " AND o.order_status = :status";
            $params['status'] = strtoupper(trim($filters['order_status']));
        }
        if (!empty($filters['dining_session_id'])) {
            $sql .= " AND o.dining_session_id = :session_id";
            $params['session_id'] = (int)$filters['dining_session_id'];
        }
        if (!empty($filters['table_id'])) {
            $sql .= " AND o.table_id = :table_id";
            $params['table_id'] = (int)$filters['table_id'];
        }
        if (!empty($filters['search'])) {
            $sql .= " AND (o.order_number LIKE :search OR rt.table_number LIKE :search OR u.name LIKE :search)";
            $params['search'] = '%' . trim($filters['search']) . '%';
        }

        $sql .= " ORDER BY o.id DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
