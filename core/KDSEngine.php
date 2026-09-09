<?php
/**
 * SMARTRESTA Core Kitchen Display System (KDS) & Multi-Station Production Engine
 * Prompt 10: Kitchen Display System, Counter Operations & Multi-Station Production Workflow
 */

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/AuditLogger.php';
require_once __DIR__ . '/RoutingEngine.php';
require_once __DIR__ . '/InventoryEngine.php';

class KDSEngine {

    /**
     * Get active station tickets queue for a branch & optional station
     * Sort order: Priority (URGENT -> HIGH -> NORMAL), then oldest queued (created_at ASC)
     */
    public static function getStationQueue(int $branchId, ?int $stationId = null, string $statusFilter = '', string $priorityFilter = ''): array {
        $db = Database::getConnection();
        $params = [':bid' => $branchId];

        $sql = "
            SELECT 
                t.*, 
                s.name AS station_name, 
                s.badge_code AS station_badge,
                s.type AS station_type,
                s.is_paused AS station_is_paused,
                s.pause_reason AS station_pause_reason,
                o.order_number, 
                o.order_type, 
                o.order_status,
                o.notes AS order_notes,
                rt.table_number, 
                u.name AS waiter_name,
                NOW() AS server_now
            FROM order_tickets t
            JOIN stations s ON t.station_id = s.id
            JOIN orders o ON t.order_id = o.id
            LEFT JOIN restaurant_tables rt ON o.table_id = rt.id
            LEFT JOIN users u ON o.taken_by_user_id = u.id
            WHERE s.branch_id = :bid
        ";

        if ($stationId && $stationId > 0) {
            $sql .= " AND t.station_id = :sid";
            $params[':sid'] = $stationId;
        }

        if ($statusFilter) {
            $sql .= " AND t.status = :st";
            $params[':st'] = $statusFilter;
        } else {
            $sql .= " AND t.status IN ('NEW', 'PREPARING', 'READY')";
        }

        if ($priorityFilter) {
            $sql .= " AND t.priority = :prio";
            $params[':prio'] = $priorityFilter;
        }

        $sql .= " ORDER BY 
            CASE t.priority 
                WHEN 'URGENT' THEN 1 
                WHEN 'HIGH' THEN 2 
                WHEN 'NORMAL' THEN 3 
                ELSE 4 
            END ASC, 
            t.created_at ASC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $tickets = $stmt->fetchAll();

        foreach ($tickets as &$t) {
            $itemStmt = $db->prepare("SELECT * FROM order_ticket_items WHERE order_ticket_id = :tid ORDER BY id ASC");
            $itemStmt->execute([':tid' => $t['id']]);
            $t['items'] = $itemStmt->fetchAll();
        }

        return $tickets;
    }

    /**
     * Calculate live active ticket header counters for KDS
     */
    public static function getKDSHeaderCounters(int $branchId, ?int $stationId = null): array {
        $db = Database::getConnection();
        $params = [':bid' => $branchId];

        $sql = "
            SELECT 
                COUNT(IF(t.status IN ('NEW', 'PREPARING', 'READY'), 1, NULL)) AS total_active,
                COUNT(IF(t.status = 'NEW', 1, NULL)) AS count_new,
                COUNT(IF(t.status = 'PREPARING', 1, NULL)) AS count_preparing,
                COUNT(IF(t.status = 'READY', 1, NULL)) AS count_ready,
                COUNT(IF(t.status = 'SERVED', 1, NULL)) AS count_served,
                COUNT(IF(t.priority = 'URGENT' AND t.status IN ('NEW', 'PREPARING'), 1, NULL)) AS count_urgent
            FROM order_tickets t
            JOIN stations s ON t.station_id = s.id
            WHERE s.branch_id = :bid
        ";

        if ($stationId && $stationId > 0) {
            $sql .= " AND t.station_id = :sid";
            $params[':sid'] = $stationId;
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $res = $stmt->fetch();

        return [
            'total_active' => (int)($res['total_active'] ?? 0),
            'count_new' => (int)($res['count_new'] ?? 0),
            'count_preparing' => (int)($res['count_preparing'] ?? 0),
            'count_ready' => (int)($res['count_ready'] ?? 0),
            'count_served' => (int)($res['count_served'] ?? 0),
            'count_urgent' => (int)($res['count_urgent'] ?? 0)
        ];
    }

    /**
     * Controlled ticket status transition with strict state machine validation & concurrency protection
     */
    public static function updateTicketStatus(int $ticketId, string $newStatus, int $userId, string $reason = ''): array {
        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            // Lock ticket row
            $stmt = $db->prepare("SELECT * FROM order_tickets WHERE id = :tid FOR UPDATE");
            $stmt->execute([':tid' => $ticketId]);
            $ticket = $stmt->fetch();

            if (!$ticket) {
                throw new Exception("Ticket #{$ticketId} not found");
            }

            $currentStatus = $ticket['status'];

            // Duplicate transition check (Idempotent)
            if ($currentStatus === $newStatus) {
                $db->rollBack();
                return ['success' => true, 'ticket_id' => $ticketId, 'status' => $newStatus, 'message' => 'Status already set.'];
            }

            // Enforce State Machine Transitions
            $allowedTransitions = [
                'NEW' => ['PREPARING', 'CANCELLED'],
                'PREPARING' => ['READY', 'CANCELLED'],
                'READY' => ['SERVED', 'PREPARING', 'CANCELLED'],
                'SERVED' => ['READY', 'CANCELLED'],
                'CANCELLED' => []
            ];

            if (!isset($allowedTransitions[$currentStatus]) || !in_array($newStatus, $allowedTransitions[$currentStatus])) {
                throw new Exception("Invalid status transition from {$currentStatus} to {$newStatus}.", 409);
            }

            // Timestamp logic
            $timeClause = "";
            if ($newStatus === 'PREPARING' && empty($ticket['started_at'])) {
                $timeClause = ", started_at = NOW()";
            } else if ($newStatus === 'READY' && empty($ticket['ready_at'])) {
                $timeClause = ", ready_at = NOW()";
            } else if ($newStatus === 'SERVED' && empty($ticket['completed_at'])) {
                $timeClause = ", completed_at = NOW()";
            }

            // Update order_tickets
            $upTkt = $db->prepare("UPDATE order_tickets SET status = :st {$timeClause} WHERE id = :tid");
            $upTkt->execute([':st' => $newStatus, ':tid' => $ticketId]);

            // Update item statuses
            $upItems = $db->prepare("UPDATE order_ticket_items SET item_status = :st WHERE order_ticket_id = :tid");
            $upItems->execute([':st' => $newStatus, ':tid' => $ticketId]);

            // Insert into order_ticket_status_history
            $histStmt = $db->prepare("
                INSERT INTO order_ticket_status_history (order_ticket_id, from_status, to_status, changed_by_user_id, reason, created_at)
                VALUES (:tid, :from_st, :to_st, :uid, :reason, NOW())
            ");
            $histStmt->execute([
                ':tid' => $ticketId,
                ':from_st' => $currentStatus,
                ':to_st' => $newStatus,
                ':uid' => $userId,
                ':reason' => $reason ?: "Transition {$currentStatus} -> {$newStatus}"
            ]);

            // Sync parent order readiness
            self::syncOrderReadiness($ticket['order_id'], $db);

            // Trigger Recipe-Based Inventory Consumption on READY or SERVED
            if (in_array($newStatus, ['READY', 'SERVED'])) {
                try {
                    InventoryEngine::consumeForTicket($ticketId, $userId);
                } catch (Exception $ie) {
                    error_log("KDS Inventory Consumption Warning for Ticket #{$ticketId}: " . $ie->getMessage());
                }
            }

            AuditLogger::log('TICKET_STATUS_UPDATED', 'order_tickets', $ticketId, null, "Ticket #{$ticket['ticket_number']} status changed from {$currentStatus} to {$newStatus}", $userId);

            $db->commit();
            return [
                'success' => true,
                'ticket_id' => $ticketId,
                'ticket_number' => $ticket['ticket_number'],
                'from_status' => $currentStatus,
                'to_status' => $newStatus
            ];

        } catch (Exception $e) {
            if ($db && $db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Recall a READY or SERVED ticket back to PREPARING
     */
    public static function recallTicket(int $ticketId, int $userId, string $reason = ''): array {
        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            $stmt = $db->prepare("SELECT * FROM order_tickets WHERE id = :tid FOR UPDATE");
            $stmt->execute([':tid' => $ticketId]);
            $ticket = $stmt->fetch();

            if (!$ticket) {
                throw new Exception("Ticket #{$ticketId} not found");
            }

            if (!in_array($ticket['status'], ['READY', 'SERVED'])) {
                throw new Exception("Only READY or SERVED tickets can be recalled. Current status: {$ticket['status']}", 409);
            }

            $targetStatus = ($ticket['status'] === 'SERVED') ? 'READY' : 'PREPARING';

            $upStmt = $db->prepare("UPDATE order_tickets SET status = :st, recalled_at = NOW(), recalled_by = :uid WHERE id = :tid");
            $upStmt->execute([':st' => $targetStatus, ':uid' => $userId, ':tid' => $ticketId]);

            $upItems = $db->prepare("UPDATE order_ticket_items SET item_status = :st WHERE order_ticket_id = :tid");
            $upItems->execute([':st' => $targetStatus, ':tid' => $ticketId]);

            $histStmt = $db->prepare("
                INSERT INTO order_ticket_status_history (order_ticket_id, from_status, to_status, changed_by_user_id, reason, created_at)
                VALUES (:tid, :from_st, :to_st, :uid, :reason, NOW())
            ");
            $histStmt->execute([
                ':tid' => $ticketId,
                ':from_st' => $ticket['status'],
                ':to_st' => $targetStatus,
                ':uid' => $userId,
                ':reason' => "Ticket recalled: " . ($reason ?: 'Kitchen recall request')
            ]);

            self::syncOrderReadiness($ticket['order_id'], $db);

            AuditLogger::log('TICKET_RECALLED', 'order_tickets', $ticketId, null, "Ticket #{$ticket['ticket_number']} recalled from {$ticket['status']} to {$targetStatus}", $userId);

            $db->commit();
            return [
                'success' => true,
                'ticket_id' => $ticketId,
                'status' => $targetStatus,
                'recalled_at' => date('Y-m-d H:i:s')
            ];

        } catch (Exception $e) {
            if ($db && $db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Re-fire a ticket for remake (creates a new URGENT ticket preserving original ticket history)
     */
    public static function refireTicket(int $ticketId, int $userId, string $reason): array {
        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            $stmt = $db->prepare("
                SELECT t.*, s.badge_code 
                FROM order_tickets t 
                JOIN stations s ON t.station_id = s.id 
                WHERE t.id = :tid 
                FOR UPDATE
            ");
            $stmt->execute([':tid' => $ticketId]);
            $ticket = $stmt->fetch();

            if (!$ticket) {
                throw new Exception("Ticket #{$ticketId} not found");
            }

            if (empty($reason)) {
                throw new Exception("Re-fire reason is required (e.g. Burned, Cold, Quality issue).");
            }

            // Create new re-fire ticket
            $newTicketNum = 'TKT-' . strtoupper($ticket['badge_code']) . '-REFIRE-' . date('Ymd') . '-' . sprintf('%04d', rand(1000, 9999));
            $refireCount = (int)$ticket['refire_count'] + 1;

            $insTkt = $db->prepare("
                INSERT INTO order_tickets (order_id, station_id, order_route_id, ticket_number, status, priority, created_by, refire_count, original_ticket_id, created_at)
                VALUES (:oid, :sid, :orid, :tnum, 'NEW', 'URGENT', :uid, :rcount, :orig_id, NOW())
            ");
            $insTkt->execute([
                ':oid' => $ticket['order_id'],
                ':sid' => $ticket['station_id'],
                ':orid' => $ticket['order_route_id'],
                ':tnum' => $newTicketNum,
                ':uid' => $userId,
                ':rcount' => $refireCount,
                ':orig_id' => $ticketId
            ]);
            $newTicketId = (int)$db->lastInsertId();

            // Copy items
            $itemStmt = $db->prepare("SELECT * FROM order_ticket_items WHERE order_ticket_id = :tid");
            $itemStmt->execute([':tid' => $ticketId]);
            $items = $itemStmt->fetchAll();

            foreach ($items as $it) {
                $insItem = $db->prepare("
                    INSERT INTO order_ticket_items (order_ticket_id, order_item_id, product_name, variant_name, quantity, modifiers_snapshot, special_instructions, item_status)
                    VALUES (:tid, :oiid, :pname, :vname, :qty, :msnap, :notes, 'NEW')
                ");
                $insItem->execute([
                    ':tid' => $newTicketId,
                    ':oiid' => $it['order_item_id'],
                    ':pname' => $it['product_name'],
                    ':vname' => $it['variant_name'],
                    ':qty' => $it['quantity'],
                    ':msnap' => $it['modifiers_snapshot'],
                    ':notes' => "[RE-FIRE #" . $refireCount . "] " . $reason . ($it['special_instructions'] ? " | " . $it['special_instructions'] : "")
                ]);
            }

            // Log status history for original ticket and new re-fire ticket
            $histStmt = $db->prepare("
                INSERT INTO order_ticket_status_history (order_ticket_id, from_status, to_status, changed_by_user_id, reason, created_at)
                VALUES (:tid, :from_st, :to_st, :uid, :reason, NOW())
            ");
            $histStmt->execute([
                ':tid' => $ticketId,
                ':from_st' => $ticket['status'],
                ':to_st' => $ticket['status'],
                ':uid' => $userId,
                ':reason' => "Re-fire requested. Generated ticket #{$newTicketNum} (Reason: {$reason})"
            ]);

            $histStmt->execute([
                ':tid' => $newTicketId,
                ':from_st' => 'NONE',
                ':to_st' => 'NEW',
                ':uid' => $userId,
                ':reason' => "Re-fired from ticket #{$ticket['ticket_number']} (Reason: {$reason})"
            ]);

            // Update refire_count on original ticket
            $upOrig = $db->prepare("UPDATE order_tickets SET refire_count = :rcount WHERE id = :tid");
            $upOrig->execute([':rcount' => $refireCount, ':tid' => $ticketId]);

            // Sync order status to PREPARING
            $db->prepare("UPDATE orders SET order_status = 'PREPARING' WHERE id = :oid")->execute([':oid' => $ticket['order_id']]);

            AuditLogger::log('TICKET_REFIRED', 'order_tickets', $ticketId, null, "Re-fired ticket #{$ticket['ticket_number']} -> New Ticket #{$newTicketNum} (Reason: {$reason})", $userId);

            $db->commit();
            return [
                'success' => true,
                'original_ticket_id' => $ticketId,
                'refire_ticket_id' => $newTicketId,
                'refire_ticket_number' => $newTicketNum,
                'refire_count' => $refireCount
            ];

        } catch (Exception $e) {
            if ($db && $db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Cancel an active station ticket
     */
    public static function cancelTicket(int $ticketId, int $userId, string $reason): array {
        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            $stmt = $db->prepare("SELECT * FROM order_tickets WHERE id = :tid FOR UPDATE");
            $stmt->execute([':tid' => $ticketId]);
            $ticket = $stmt->fetch();

            if (!$ticket) {
                throw new Exception("Ticket #{$ticketId} not found");
            }

            if (empty($reason)) {
                throw new Exception("Cancellation reason is required.");
            }

            $currentStatus = $ticket['status'];
            if ($currentStatus === 'CANCELLED') {
                $db->rollBack();
                return ['success' => true, 'ticket_id' => $ticketId, 'status' => 'CANCELLED'];
            }

            $upTkt = $db->prepare("UPDATE order_tickets SET status = 'CANCELLED' WHERE id = :tid");
            $upTkt->execute([':tid' => $ticketId]);

            $upItems = $db->prepare("UPDATE order_ticket_items SET item_status = 'CANCELLED' WHERE order_ticket_id = :tid");
            $upItems->execute([':tid' => $ticketId]);

            $histStmt = $db->prepare("
                INSERT INTO order_ticket_status_history (order_ticket_id, from_status, to_status, changed_by_user_id, reason, created_at)
                VALUES (:tid, :from_st, 'CANCELLED', :uid, :reason, NOW())
            ");
            $histStmt->execute([
                ':tid' => $ticketId,
                ':from_st' => $currentStatus,
                ':uid' => $userId,
                ':reason' => "Ticket cancelled: {$reason}"
            ]);

            self::syncOrderReadiness($ticket['order_id'], $db);

            AuditLogger::log('TICKET_CANCELLED', 'order_tickets', $ticketId, null, "Ticket #{$ticket['ticket_number']} cancelled (Reason: {$reason})", $userId);

            $db->commit();
            return [
                'success' => true,
                'ticket_id' => $ticketId,
                'status' => 'CANCELLED'
            ];

        } catch (Exception $e) {
            if ($db && $db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Get detailed status transition history for a ticket
     */
    public static function getTicketHistory(int $ticketId): array {
        $db = Database::getConnection();

        $tktStmt = $db->prepare("
            SELECT t.*, s.name AS station_name, o.order_number 
            FROM order_tickets t
            JOIN stations s ON t.station_id = s.id
            JOIN orders o ON t.order_id = o.id
            WHERE t.id = :tid
        ");
        $tktStmt->execute([':tid' => $ticketId]);
        $ticket = $tktStmt->fetch();

        if (!$ticket) {
            return [];
        }

        $histStmt = $db->prepare("
            SELECT h.*, u.name AS user_name 
            FROM order_ticket_status_history h
            LEFT JOIN users u ON h.changed_by_user_id = u.id
            WHERE h.order_ticket_id = :tid
            ORDER BY h.created_at ASC
        ");
        $histStmt->execute([':tid' => $ticketId]);
        $history = $histStmt->fetchAll();

        return [
            'ticket' => $ticket,
            'history' => $history
        ];
    }

    /**
     * Manage operational station state (Pause, Resume, Create, Update)
     */
    public static function manageStation(array $data, int $userId): array {
        $db = Database::getConnection();
        $action = $data['action'] ?? 'update_status';

        if ($action === 'pause') {
            $stationId = (int)($data['station_id'] ?? 0);
            $reason = trim($data['reason'] ?? 'Temporary station pause');

            $stmt = $db->prepare("UPDATE stations SET status = 'PAUSED', is_paused = 1, pause_reason = :reason, paused_at = NOW() WHERE id = :sid");
            $stmt->execute([':reason' => $reason, ':sid' => $stationId]);

            AuditLogger::log('STATION_PAUSED', 'stations', $stationId, null, "Station ID #{$stationId} paused (Reason: {$reason})", $userId);
            return ['success' => true, 'station_id' => $stationId, 'status' => 'PAUSED'];
        }

        if ($action === 'resume') {
            $stationId = (int)($data['station_id'] ?? 0);

            $stmt = $db->prepare("UPDATE stations SET status = 'ACTIVE', is_paused = 0, pause_reason = NULL, paused_at = NULL WHERE id = :sid");
            $stmt->execute([':sid' => $stationId]);

            AuditLogger::log('STATION_ACTIVATED', 'stations', $stationId, null, "Station ID #{$stationId} activated", $userId);
            return ['success' => true, 'station_id' => $stationId, 'status' => 'ACTIVE'];
        }

        if ($action === 'create') {
            $branchId = (int)($data['branch_id'] ?? 1);
            $name = trim($data['name'] ?? '');
            $type = strtoupper(trim($data['type'] ?? 'KITCHEN'));
            $badgeCode = strtoupper(trim($data['badge_code'] ?? ('ST-' . substr($name, 0, 5))));

            if (!$name) {
                throw new Exception("Station name is required.");
            }

            $stmt = $db->prepare("
                INSERT INTO stations (branch_id, name, badge_code, type, status, is_paused)
                VALUES (:bid, :name, :code, :type, 'ACTIVE', 0)
            ");
            $stmt->execute([':bid' => $branchId, ':name' => $name, ':code' => $badgeCode, ':type' => $type]);
            $stationId = (int)$db->lastInsertId();

            AuditLogger::log('STATION_CREATED', 'stations', $stationId, null, "Operational Station '{$name}' ({$type}) created", $userId);
            return ['success' => true, 'station_id' => $stationId, 'name' => $name];
        }

        throw new Exception("Invalid station management action: {$action}");
    }

    /**
     * Recalculate parent order status based on all station tickets
     */
    private static function syncOrderReadiness(int $orderId, PDO $db): void {
        $stmt = $db->prepare("SELECT status FROM order_tickets WHERE order_id = :oid AND status != 'CANCELLED'");
        $stmt->execute([':oid' => $orderId]);
        $statuses = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (empty($statuses)) {
            return;
        }

        // All non-cancelled tickets are READY or SERVED
        $allReadyOrServed = array_reduce($statuses, fn($carry, $s) => $carry && in_array($s, ['READY', 'SERVED']), true);
        $anyPreparing = in_array('PREPARING', $statuses);

        if ($allReadyOrServed) {
            $upOrd = $db->prepare("UPDATE orders SET order_status = 'READY' WHERE id = :oid AND order_status IN ('SUBMITTED', 'ROUTED', 'PREPARING')");
            $upOrd->execute([':oid' => $orderId]);
        } else if ($anyPreparing) {
            $upOrd = $db->prepare("UPDATE orders SET order_status = 'PREPARING' WHERE id = :oid AND order_status IN ('SUBMITTED', 'ROUTED')");
            $upOrd->execute([':oid' => $orderId]);
        }
    }
}
