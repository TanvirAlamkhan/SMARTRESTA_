<?php
/**
 * SMARTRESTA Core Order Routing Engine & Ticket Dispatch Service
 * Prompt 07: Smart Order Routing Engine, Multi-Station Dispatch & Item-Level Routing
 */

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/AuditLogger.php';

class RoutingEngine {

    /**
     * Resolve target operational station for an order line item
     * Precedence:
     * 1. Variant station_id override (if variant has assigned station)
     * 2. Product default_station_id (products.default_station_id)
     * 3. Branch Default Kitchen Station (fallback)
     */
    public static function resolveItemStation(int $productId, ?int $variantId = null, int $branchId = 1): int {
        $db = Database::getConnection();

        // 1. Check Variant Override
        if ($variantId) {
            $stmt = $db->prepare("SELECT station_id FROM product_variants WHERE id = :vid AND station_id IS NOT NULL");
            $stmt->execute([':vid' => $variantId]);
            $variantStation = $stmt->fetchColumn();
            if ($variantStation) {
                return (int)$variantStation;
            }
        }

        // 2. Check Product Default Station
        $stmt = $db->prepare("SELECT default_station_id FROM products WHERE id = :pid AND deleted_at IS NULL");
        $stmt->execute([':pid' => $productId]);
        $productStation = $stmt->fetchColumn();
        if ($productStation) {
            return (int)$productStation;
        }

        // 3. Fallback to Branch Default Kitchen Station
        $stmt = $db->prepare("SELECT id FROM stations WHERE branch_id = :bid AND status = 'ACTIVE' ORDER BY id ASC LIMIT 1");
        $stmt->execute([':bid' => $branchId]);
        $defaultStation = $stmt->fetchColumn();

        if ($defaultStation) {
            return (int)$defaultStation;
        }

        throw new Exception("No active station available for branch ID {$branchId}");
    }

    /**
     * Route an order into station-specific routes and operational tickets
     * Transactional and Idempotent.
     */
    public static function routeOrder(int $orderId, int $userId = 1, string $mode = 'AUTOMATIC'): array {
        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            // 1. Fetch Order Details
            $stmt = $db->prepare("
                SELECT id, branch_id, order_number, order_status, priority 
                FROM orders 
                WHERE id = :id 
                FOR UPDATE
            ");
            $stmt->execute([':id' => $orderId]);
            $order = $stmt->fetch();

            if (!$order) {
                throw new Exception("Order #{$orderId} not found");
            }

            // Valid states for routing: SUBMITTED, CONFIRMED, ROUTED, PARTIALLY_ROUTED
            if (in_array($order['order_status'], ['DRAFT', 'COMPLETED', 'CANCELLED', 'REFUNDED'])) {
                throw new Exception("Order #{$order['order_number']} is in status {$order['order_status']} and cannot be routed.");
            }

            // 2. Fetch Unrouted Line Items
            $stmt = $db->prepare("
                SELECT oi.*, p.name AS product_name, p.default_station_id AS product_station_id
                FROM order_items oi
                JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = :oid AND (oi.routing_status = 'UNROUTED' OR oi.station_id IS NULL)
                FOR UPDATE
            ");
            $stmt->execute([':oid' => $orderId]);
            $unroutedItems = $stmt->fetchAll();

            if (empty($unroutedItems)) {
                // Already fully routed - return existing routes (Idempotent response)
                $db->rollBack();
                return self::getOrderRouteSummary($orderId);
            }

            // 3. Resolve Target Station for Each Item and Group
            $stationGroups = [];
            foreach ($unroutedItems as $item) {
                $targetStationId = self::resolveItemStation(
                    (int)$item['product_id'], 
                    !empty($item['variant_id']) ? (int)$item['variant_id'] : null, 
                    (int)$order['branch_id']
                );

                // Verify station exists and is active
                $stStmt = $db->prepare("SELECT badge_code, status FROM stations WHERE id = :sid AND branch_id = :bid");
                $stStmt->execute([':sid' => $targetStationId, ':bid' => $order['branch_id']]);
                $stationInfo = $stStmt->fetch();

                if (!$stationInfo || $stationInfo['status'] !== 'ACTIVE') {
                    throw new Exception("Target station ID {$targetStationId} is inactive or invalid for branch.");
                }

                $stationGroups[$targetStationId][] = [
                    'item' => $item,
                    'badge_code' => $stationInfo['badge_code']
                ];
            }

            $createdTickets = [];

            // 4. Create Order Routes and Station Tickets for each Station Group
            foreach ($stationGroups as $stationId => $groupItems) {
                $badgeCode = $groupItems[0]['badge_code'];

                // Insert or Get Order Route
                $routeStmt = $db->prepare("
                    INSERT INTO order_routes (order_id, station_id, routing_mode, route_status, routed_by, routed_at)
                    VALUES (:oid, :sid, :mode, 'SENT', :uid, NOW())
                    ON DUPLICATE KEY UPDATE route_status = 'SENT', routed_at = NOW()
                ");
                $routeStmt->execute([
                    ':oid' => $orderId,
                    ':sid' => $stationId,
                    ':mode' => $mode,
                    ':uid' => $userId
                ]);

                $stmtRouteId = $db->prepare("SELECT id FROM order_routes WHERE order_id = :oid AND station_id = :sid");
                $stmtRouteId->execute([':oid' => $orderId, ':sid' => $stationId]);
                $orderRouteId = (int)$stmtRouteId->fetchColumn();

                // Generate Unique Station Ticket Number
                $ticketNum = 'TKT-' . strtoupper($badgeCode) . '-' . date('Ymd') . '-' . sprintf('%04d', rand(1000, 9999));

                $ticketStmt = $db->prepare("
                    INSERT INTO order_tickets (order_id, station_id, order_route_id, ticket_number, status, priority, created_by, created_at)
                    VALUES (:oid, :sid, :orid, :tnum, 'NEW', :priority, :uid, NOW())
                ");
                $ticketStmt->execute([
                    ':oid' => $orderId,
                    ':sid' => $stationId,
                    ':orid' => $orderRouteId,
                    ':tnum' => $ticketNum,
                    ':priority' => $order['priority'] ?? 'NORMAL',
                    ':uid' => $userId
                ]);
                $ticketId = (int)$db->lastInsertId();

                // Create Ticket Items and Update Order Item Routing Status
                foreach ($groupItems as $gi) {
                    $item = $gi['item'];

                    // Fetch Item Modifiers Snapshot
                    $modStmt = $db->prepare("SELECT modifier_name, unit_price FROM order_item_modifiers WHERE order_item_id = :oiid");
                    $modStmt->execute([':oiid' => $item['id']]);
                    $modifiers = $modStmt->fetchAll();
                    $modText = !empty($modifiers) ? implode(', ', array_map(fn($m) => $m['modifier_name'], $modifiers)) : null;

                    $tItemStmt = $db->prepare("
                        INSERT INTO order_ticket_items 
                        (order_ticket_id, order_item_id, product_name, variant_name, quantity, modifiers_snapshot, special_instructions, item_status)
                        VALUES (:tid, :oiid, :pname, :vname, :qty, :msnap, :notes, 'NEW')
                    ");
                    $tItemStmt->execute([
                        ':tid' => $ticketId,
                        ':oiid' => $item['id'],
                        ':pname' => $item['product_name'] ?? $item['item_name'],
                        ':vname' => $item['variant_name'] ?? null,
                        ':qty' => $item['quantity'],
                        ':msnap' => $modText,
                        ':notes' => $item['notes'] ?? null
                    ]);

                    // Update order item station and routing status
                    $upItemStmt = $db->prepare("
                        UPDATE order_items 
                        SET station_id = :sid, routing_status = 'ROUTED', routed_at = NOW() 
                        WHERE id = :oiid
                    ");
                    $upItemStmt->execute([':sid' => $stationId, ':oiid' => $item['id']]);
                }

                $createdTickets[] = [
                    'ticket_id' => $ticketId,
                    'ticket_number' => $ticketNum,
                    'station_id' => $stationId,
                    'item_count' => count($groupItems)
                ];
            }

            // 5. Update Aggregate Order Status
            $checkUnrouted = $db->prepare("SELECT COUNT(*) FROM order_items WHERE order_id = :oid AND routing_status = 'UNROUTED'");
            $checkUnrouted->execute([':oid' => $orderId]);
            $remainingUnrouted = (int)$checkUnrouted->fetchColumn();

            $newOrderStatus = ($remainingUnrouted === 0) ? 'ROUTED' : 'PARTIALLY_ROUTED';
            $upOrdStmt = $db->prepare("UPDATE orders SET order_status = :st WHERE id = :oid");
            $upOrdStmt->execute([':st' => $newOrderStatus, ':oid' => $orderId]);

            // 6. Audit Log
            AuditLogger::log(
                'ROUTE_CREATED',
                'orders',
                $orderId,
                null,
                "Order #{$order['order_number']} routed to " . count($stationGroups) . " station(s).",
                $userId
            );

            $db->commit();
            return self::getOrderRouteSummary($orderId);

        } catch (Exception $e) {
            if ($db && $db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Override station assignment for a specific order item
     */
    public static function rerouteItem(int $orderItemId, int $newStationId, int $userId, string $reason = ''): array {
        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            $stmt = $db->prepare("
                SELECT oi.*, o.branch_id, o.order_number, o.priority 
                FROM order_items oi 
                JOIN orders o ON oi.order_id = o.id 
                WHERE oi.id = :oiid 
                FOR UPDATE
            ");
            $stmt->execute([':oiid' => $orderItemId]);
            $item = $stmt->fetch();

            if (!$item) {
                throw new Exception("Order line item ID {$orderItemId} not found");
            }

            // Verify new station exists and is active
            $stStmt = $db->prepare("SELECT name, badge_code, status FROM stations WHERE id = :sid AND branch_id = :bid");
            $stStmt->execute([':sid' => $newStationId, ':bid' => $item['branch_id']]);
            $newStation = $stStmt->fetch();

            if (!$newStation || $newStation['status'] !== 'ACTIVE') {
                throw new Exception("Target station ID {$newStationId} is invalid or inactive.");
            }

            // Update order_items table
            $upStmt = $db->prepare("UPDATE order_items SET station_id = :sid, routing_status = 'ROUTED' WHERE id = :oiid");
            $upStmt->execute([':sid' => $newStationId, ':oiid' => $orderItemId]);

            // Create or update order_routes
            $routeStmt = $db->prepare("
                INSERT INTO order_routes (order_id, station_id, routing_mode, route_status, routed_by, routed_at, notes)
                VALUES (:oid, :sid, 'MANUAL', 'SENT', :uid, NOW(), :notes)
                ON DUPLICATE KEY UPDATE route_status = 'SENT', routed_at = NOW(), notes = VALUES(notes)
            ");
            $routeStmt->execute([
                ':oid' => $item['order_id'],
                ':sid' => $newStationId,
                ':uid' => $userId,
                ':notes' => "Rerouted item: " . ($reason ?: 'Manual override')
            ]);

            $db->commit();
            return self::getOrderRouteSummary((int)$item['order_id']);

        } catch (Exception $e) {
            if ($db && $db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Update ticket operational status (State Machine: NEW -> PREPARING -> READY -> SERVED)
     */
    public static function updateTicketStatus(int $ticketId, string $newStatus, int $userId): array {
        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            $stmt = $db->prepare("SELECT * FROM order_tickets WHERE id = :tid FOR UPDATE");
            $stmt->execute([':tid' => $ticketId]);
            $ticket = $stmt->fetch();

            if (!$ticket) {
                throw new Exception("Ticket #{$ticketId} not found");
            }

            $validStatuses = ['NEW', 'PREPARING', 'READY', 'SERVED', 'CANCELLED'];
            if (!in_array($newStatus, $validStatuses)) {
                throw new Exception("Invalid ticket status: {$newStatus}");
            }

            $timeField = '';
            if ($newStatus === 'PREPARING' && empty($ticket['started_at'])) {
                $timeField = ", started_at = NOW()";
            } else if ($newStatus === 'READY' && empty($ticket['ready_at'])) {
                $timeField = ", ready_at = NOW()";
            } else if ($newStatus === 'SERVED' && empty($ticket['completed_at'])) {
                $timeField = ", completed_at = NOW()";
            }

            $upTicket = $db->prepare("UPDATE order_tickets SET status = :st {$timeField} WHERE id = :tid");
            $upTicket->execute([':st' => $newStatus, ':tid' => $ticketId]);

            // Update item statuses in order_ticket_items
            $upItems = $db->prepare("UPDATE order_ticket_items SET item_status = :st WHERE order_ticket_id = :tid");
            $upItems->execute([':st' => $newStatus, ':tid' => $ticketId]);

            // Sync main order status if all tickets for this order are ready/served
            $allTktStmt = $db->prepare("SELECT status FROM order_tickets WHERE order_id = :oid");
            $allTktStmt->execute([':oid' => $ticket['order_id']]);
            $allStatuses = $allTktStmt->fetchAll(PDO::FETCH_COLUMN);

            if (!empty($allStatuses)) {
                if (array_reduce($allStatuses, fn($carry, $s) => $carry && in_array($s, ['READY', 'SERVED']), true)) {
                    $db->prepare("UPDATE orders SET order_status = 'READY' WHERE id = :oid AND order_status IN ('SUBMITTED', 'ROUTED', 'PREPARING')")
                       ->execute([':oid' => $ticket['order_id']]);
                } else if (in_array('PREPARING', $allStatuses)) {
                    $db->prepare("UPDATE orders SET order_status = 'PREPARING' WHERE id = :oid AND order_status IN ('SUBMITTED', 'ROUTED')")
                       ->execute([':oid' => $ticket['order_id']]);
                }
            }

            AuditLogger::log('TICKET_STATUS_UPDATED', 'order_tickets', $ticketId, null, "Ticket #{$ticket['ticket_number']} set to {$newStatus}", $userId);

            $db->commit();
            return ['success' => true, 'ticket_id' => $ticketId, 'status' => $newStatus];

        } catch (Exception $e) {
            if ($db && $db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Retrieve station operational tickets for active queue
     */
    public static function getStationQueue(int $branchId, ?int $stationId = null, string $statusFilter = ''): array {
        $db = Database::getConnection();
        $params = [':bid' => $branchId];

        $sql = "
            SELECT 
                t.*, 
                s.name AS station_name, 
                s.badge_code AS station_badge,
                o.order_number, 
                o.order_type, 
                rt.table_number, 
                u.name AS taken_by_name
            FROM order_tickets t
            JOIN stations s ON t.station_id = s.id
            JOIN orders o ON t.order_id = o.id
            LEFT JOIN restaurant_tables rt ON o.table_id = rt.id
            LEFT JOIN users u ON o.taken_by_user_id = u.id
            WHERE s.branch_id = :bid
        ";

        if ($stationId) {
            $sql .= " AND t.station_id = :sid";
            $params[':sid'] = $stationId;
        }

        if ($statusFilter) {
            $sql .= " AND t.status = :st";
            $params[':st'] = $statusFilter;
        } else {
            $sql .= " AND t.status != 'SERVED' AND t.status != 'CANCELLED'";
        }

        $sql .= " ORDER BY t.created_at ASC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $tickets = $stmt->fetchAll();

        foreach ($tickets as &$t) {
            $itemStmt = $db->prepare("SELECT * FROM order_ticket_items WHERE order_ticket_id = :tid");
            $itemStmt->execute([':tid' => $t['id']]);
            $t['items'] = $itemStmt->fetchAll();
        }

        return $tickets;
    }

    /**
     * Detailed station tickets and routing summary for an order
     */
    public static function getOrderRouteSummary(int $orderId): array {
        $db = Database::getConnection();

        $ordStmt = $db->prepare("
            SELECT o.*, rt.table_number, u.name AS waiter_name 
            FROM orders o
            LEFT JOIN restaurant_tables rt ON o.table_id = rt.id
            LEFT JOIN users u ON o.taken_by_user_id = u.id
            WHERE o.id = :oid
        ");
        $ordStmt->execute([':oid' => $orderId]);
        $order = $ordStmt->fetch();

        if (!$order) {
            return [];
        }

        // Fetch routes
        $routeStmt = $db->prepare("
            SELECT r.*, s.name AS station_name, s.badge_code, s.type AS station_type
            FROM order_routes r
            JOIN stations s ON r.station_id = s.id
            WHERE r.order_id = :oid
        ");
        $routeStmt->execute([':oid' => $orderId]);
        $routes = $routeStmt->fetchAll();

        // Fetch tickets
        $ticketStmt = $db->prepare("
            SELECT t.*, s.name AS station_name, s.badge_code 
            FROM order_tickets t
            JOIN stations s ON t.station_id = s.id
            WHERE t.order_id = :oid
        ");
        $ticketStmt->execute([':oid' => $orderId]);
        $tickets = $ticketStmt->fetchAll();

        foreach ($tickets as &$t) {
            $itemStmt = $db->prepare("SELECT * FROM order_ticket_items WHERE order_ticket_id = :tid");
            $itemStmt->execute([':tid' => $t['id']]);
            $t['items'] = $itemStmt->fetchAll();
        }

        // Fetch items
        $itemStmt = $db->prepare("
            SELECT oi.*, s.name AS station_name
            FROM order_items oi
            LEFT JOIN stations s ON oi.station_id = s.id
            WHERE oi.order_id = :oid
        ");
        $itemStmt->execute([':oid' => $orderId]);
        $items = $itemStmt->fetchAll();

        return [
            'order' => $order,
            'routes' => $routes,
            'tickets' => $tickets,
            'items' => $items
        ];
    }

    /**
     * Get list of active stations for a branch
     */
    public static function getStations(int $branchId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM stations WHERE branch_id = :bid AND status = 'ACTIVE' ORDER BY display_order ASC, name ASC");
        $stmt->execute([':bid' => $branchId]);
        return $stmt->fetchAll();
    }
}
