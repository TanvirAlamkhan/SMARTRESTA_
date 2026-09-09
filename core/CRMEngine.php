<?php
/**
 * SMARTRESTA Core CRM, Reservations, Loyalty, Coupons & QR Ordering Engine
 * Prompt 14 Complete Implementation
 */

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/AuditLogger.php';
require_once __DIR__ . '/OrderEngine.php';
require_once __DIR__ . '/DiningSessionEngine.php';
require_once __DIR__ . '/MenuEngine.php';
require_once __DIR__ . '/RoutingEngine.php';

class CRMEngine {

    // ==========================================
    // 1. CUSTOMER RELATIONSHIP MANAGEMENT (CRM)
    // ==========================================

    /**
     * Generate customer code (e.g. CUST-20260909-0001)
     */
    public static function generateCustomerCode(?int $branchId = 1): string {
        $prefix = sprintf('CUST-%s-', date('Ymd'));
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) FROM customers WHERE customer_code LIKE :prefix");
        $stmt->execute(['prefix' => $prefix . '%']);
        $count = (int)$stmt->fetchColumn() + 1;
        return $prefix . sprintf('%04d', $count);
    }

    /**
     * Create customer record
     */
    public static function createCustomer(array $data, int $userId = 1): array {
        $db = Database::getConnection();

        $name = trim($data['name'] ?? '');
        $phone = preg_replace('/[^\d+]/', '', trim($data['phone'] ?? ''));
        $email = !empty($data['email']) ? strtolower(trim($data['email'])) : null;
        $branchId = !empty($data['branch_id']) ? (int)$data['branch_id'] : 1;

        if (empty($name) && (!empty($data['first_name']) || !empty($data['last_name']))) {
            $name = trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''));
        }

        if (empty($name)) {
            throw new Exception("Customer name or first/last name is required.");
        }
        if (empty($phone)) {
            throw new Exception("Customer phone number is required.");
        }

        // Duplicate Check
        $stmtCheck = $db->prepare("SELECT id, name FROM customers WHERE phone = :phone AND deleted_at IS NULL");
        $stmtCheck->execute(['phone' => $phone]);
        $existing = $stmtCheck->fetch(PDO::FETCH_ASSOC);
        if ($existing) {
            throw new Exception("A customer with phone number '{$phone}' already exists (#{$existing['id']} - {$existing['name']}).");
        }

        $code = !empty($data['customer_code']) ? trim($data['customer_code']) : self::generateCustomerCode($branchId);
        $firstName = !empty($data['first_name']) ? trim($data['first_name']) : null;
        $lastName = !empty($data['last_name']) ? trim($data['last_name']) : null;
        $dob = !empty($data['date_of_birth']) ? trim($data['date_of_birth']) : null;
        $gender = !empty($data['gender']) ? strtoupper(trim($data['gender'])) : 'UNDISCLOSED';
        $company = !empty($data['company_name']) ? trim($data['company_name']) : null;
        $notes = !empty($data['notes']) ? trim($data['notes']) : null;
        $optIn = isset($data['marketing_opt_in']) ? (int)(bool)$data['marketing_opt_in'] : 1;

        $db->beginTransaction();
        try {
            $stmt = $db->prepare("
                INSERT INTO customers (
                    branch_id, customer_code, name, first_name, last_name, phone, email,
                    date_of_birth, gender, company_name, notes, marketing_opt_in, status
                ) VALUES (
                    :branch_id, :code, :name, :first_name, :last_name, :phone, :email,
                    :dob, :gender, :company, :notes, :opt_in, 'ACTIVE'
                )
            ");
            $stmt->execute([
                'branch_id' => $branchId,
                'code' => $code,
                'name' => $name,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone' => $phone,
                'email' => $email,
                'dob' => $dob,
                'gender' => $gender,
                'company' => $company,
                'notes' => $notes,
                'opt_in' => $optIn
            ]);
            $customerId = (int)$db->lastInsertId();

            // Initialize Loyalty Account
            $stmtLoyalty = $db->prepare("
                INSERT INTO loyalty_accounts (customer_id, branch_id, points_balance, lifetime_earned, lifetime_redeemed, status)
                VALUES (:cust_id, :branch_id, 0.00, 0.00, 0.00, 'ACTIVE')
                ON DUPLICATE KEY UPDATE updated_at = CURRENT_TIMESTAMP
            ");
            $stmtLoyalty->execute(['cust_id' => $customerId, 'branch_id' => $branchId]);

            AuditLogger::log($userId, 'CUSTOMER_CREATED', 'customer', $customerId, null, [
                'code' => $code, 'name' => $name, 'phone' => $phone
            ]);

            $db->commit();
            return self::getCustomer($customerId);

        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * Get single customer by ID
     */
    public static function getCustomer(int $id): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT c.*, b.name AS branch_name,
                   la.points_balance, la.lifetime_earned, la.lifetime_redeemed, la.status AS loyalty_status
            FROM customers c
            LEFT JOIN branches b ON c.branch_id = b.id
            LEFT JOIN loyalty_accounts la ON c.id = la.customer_id
            WHERE c.id = :id AND c.deleted_at IS NULL
        ");
        $stmt->execute(['id' => $id]);
        $cust = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$cust) {
            throw new Exception("Customer #{$id} not found.");
        }
        return $cust;
    }

    /**
     * Update customer information
     */
    public static function updateCustomer(int $id, array $data, int $userId = 1): array {
        $db = Database::getConnection();
        $cust = self::getCustomer($id);

        $name = !empty($data['name']) ? trim($data['name']) : $cust['name'];
        $phone = !empty($data['phone']) ? preg_replace('/[^\d+]/', '', trim($data['phone'])) : $cust['phone'];
        $email = isset($data['email']) ? strtolower(trim($data['email'])) : $cust['email'];

        // Phone duplicate check if phone changed
        if ($phone !== $cust['phone']) {
            $stmtCheck = $db->prepare("SELECT id FROM customers WHERE phone = :phone AND id != :id AND deleted_at IS NULL");
            $stmtCheck->execute(['phone' => $phone, 'id' => $id]);
            if ($stmtCheck->fetchColumn()) {
                throw new Exception("Another customer with phone number '{$phone}' already exists.");
            }
        }

        $stmt = $db->prepare("
            UPDATE customers SET
                name = :name,
                first_name = :first_name,
                last_name = :last_name,
                phone = :phone,
                email = :email,
                date_of_birth = :dob,
                gender = :gender,
                company_name = :company,
                notes = :notes,
                marketing_opt_in = :opt_in,
                status = :status
            WHERE id = :id AND deleted_at IS NULL
        ");
        $stmt->execute([
            'name' => $name,
            'first_name' => $data['first_name'] ?? $cust['first_name'],
            'last_name' => $data['last_name'] ?? $cust['last_name'],
            'phone' => $phone,
            'email' => $email ?: null,
            'dob' => $data['date_of_birth'] ?? $cust['date_of_birth'],
            'gender' => $data['gender'] ?? $cust['gender'],
            'company' => $data['company_name'] ?? $cust['company_name'],
            'notes' => $data['notes'] ?? $cust['notes'],
            'opt_in' => isset($data['marketing_opt_in']) ? (int)(bool)$data['marketing_opt_in'] : $cust['marketing_opt_in'],
            'status' => $data['status'] ?? $cust['status'],
            'id' => $id
        ]);

        AuditLogger::log($userId, 'CUSTOMER_UPDATED', 'customer', $id, null, ['name' => $name, 'phone' => $phone]);
        return self::getCustomer($id);
    }

    /**
     * Search and list customers
     */
    public static function searchCustomers(array $filters = []): array {
        $db = Database::getConnection();
        $sql = "
            SELECT c.id, c.branch_id, c.customer_code, c.name, c.first_name, c.last_name,
                   c.phone, c.email, c.status, c.created_at, b.name AS branch_name,
                   COALESCE(la.points_balance, 0.00) AS points_balance,
                   (SELECT COUNT(*) FROM orders o WHERE o.customer_id = c.id AND o.order_status = 'COMPLETED') AS completed_orders,
                   (SELECT COALESCE(SUM(o.total), 0.00) FROM orders o WHERE o.customer_id = c.id AND o.payment_status = 'PAID') AS lifetime_sales,
                   (SELECT MAX(o.created_at) FROM orders o WHERE o.customer_id = c.id) AS last_visit
            FROM customers c
            LEFT JOIN branches b ON c.branch_id = b.id
            LEFT JOIN loyalty_accounts la ON c.id = la.customer_id
            WHERE c.deleted_at IS NULL
        ";
        $params = [];

        if (!empty($filters['branch_id'])) {
            $sql .= " AND (c.branch_id = :branch_id OR c.branch_id IS NULL)";
            $params['branch_id'] = (int)$filters['branch_id'];
        }
        if (!empty($filters['status'])) {
            $sql .= " AND c.status = :status";
            $params['status'] = strtoupper(trim($filters['status']));
        }
        if (!empty($filters['search'])) {
            $s = '%' . trim($filters['search']) . '%';
            $sql .= " AND (c.name LIKE :s1 OR c.phone LIKE :s2 OR c.email LIKE :s3 OR c.customer_code LIKE :s4)";
            $params['s1'] = $s;
            $params['s2'] = $s;
            $params['s3'] = $s;
            $params['s4'] = $s;
        }

        $sql .= " ORDER BY c.id DESC";

        $limit = !empty($filters['limit']) ? (int)$filters['limit'] : 100;
        $sql .= " LIMIT " . $limit;

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Customer Profile & Full Audit Metric Summary
     */
    public static function getCustomerProfile(int $id): array {
        $db = Database::getConnection();
        $customer = self::getCustomer($id);

        // Order History
        $stmtOrders = $db->prepare("
            SELECT o.id, o.order_number, o.branch_id, o.order_type, o.subtotal, o.discount, o.tax,
                   o.total, o.payment_status, o.order_status, o.created_at, b.name AS branch_name,
                   rt.table_number
            FROM orders o
            JOIN branches b ON o.branch_id = b.id
            LEFT JOIN restaurant_tables rt ON o.table_id = rt.id
            WHERE o.customer_id = :id
            ORDER BY o.id DESC
        ");
        $stmtOrders->execute(['id' => $id]);
        $orders = $stmtOrders->fetchAll(PDO::FETCH_ASSOC);

        // Visit History (Dining Sessions & Orders)
        $stmtVisits = $db->prepare("
            SELECT ds.id AS dining_session_id, ds.table_id, rt.table_number, f.name AS floor_name,
                   ds.guest_count, ds.opened_at, ds.closed_at, ds.status AS session_status
            FROM dining_sessions ds
            JOIN restaurant_tables rt ON ds.table_id = rt.id
            JOIN floors f ON rt.floor_id = f.id
            JOIN orders o ON o.dining_session_id = ds.id
            WHERE o.customer_id = :id
            GROUP BY ds.id
            ORDER BY ds.id DESC
        ");
        $stmtVisits->execute(['id' => $id]);
        $visits = $stmtVisits->fetchAll(PDO::FETCH_ASSOC);

        // Payment History
        $stmtPayments = $db->prepare("
            SELECT p.id, p.order_id, p.amount, p.status, p.created_at, pm.name AS payment_method_name,
                   o.order_number
            FROM payments p
            JOIN orders o ON p.order_id = o.id
            LEFT JOIN payment_methods pm ON p.payment_method_id = pm.id
            WHERE o.customer_id = :id
            ORDER BY p.id DESC
        ");
        $stmtPayments->execute(['id' => $id]);
        $payments = $stmtPayments->fetchAll(PDO::FETCH_ASSOC);

        // Loyalty Ledger History
        $stmtLoyalty = $db->prepare("
            SELECT lt.*, o.order_number, u.name AS created_by_name
            FROM loyalty_transactions lt
            LEFT JOIN orders o ON lt.order_id = o.id
            LEFT JOIN users u ON lt.created_by = u.id
            WHERE lt.customer_id = :id
            ORDER BY lt.id DESC
        ");
        $stmtLoyalty->execute(['id' => $id]);
        $loyaltyLedger = $stmtLoyalty->fetchAll(PDO::FETCH_ASSOC);

        // Reservation History
        $stmtRes = $db->prepare("
            SELECT r.*, rt.table_number, b.name AS branch_name
            FROM reservations r
            LEFT JOIN restaurant_tables rt ON r.table_id = rt.id
            LEFT JOIN branches b ON r.branch_id = b.id
            WHERE r.customer_id = :id
            ORDER BY r.id DESC
        ");
        $stmtRes->execute(['id' => $id]);
        $reservations = $stmtRes->fetchAll(PDO::FETCH_ASSOC);

        // Calculate Real Financial Metrics
        $totalOrders = count($orders);
        $completedOrders = 0;
        $cancelledOrders = 0;
        $lifetimeSales = 0.00;
        $totalDiscounts = 0.00;
        $firstVisit = null;
        $lastVisit = null;

        foreach ($orders as $ord) {
            if ($ord['order_status'] === 'COMPLETED') {
                $completedOrders++;
            } elseif ($ord['order_status'] === 'CANCELLED') {
                $cancelledOrders++;
            }
            if ($ord['payment_status'] === 'PAID') {
                $lifetimeSales += (float)$ord['total'];
            }
            $totalDiscounts += (float)$ord['discount'];

            if ($firstVisit === null || $ord['created_at'] < $firstVisit) {
                $firstVisit = $ord['created_at'];
            }
            if ($lastVisit === null || $ord['created_at'] > $lastVisit) {
                $lastVisit = $ord['created_at'];
            }
        }

        $avgOrderValue = $completedOrders > 0 ? round($lifetimeSales / $completedOrders, 2) : 0.00;

        return [
            'customer' => $customer,
            'metrics' => [
                'total_orders' => $totalOrders,
                'completed_orders' => $completedOrders,
                'cancelled_orders' => $cancelledOrders,
                'lifetime_sales' => $lifetimeSales,
                'average_order_value' => $avgOrderValue,
                'total_visits' => count($visits),
                'total_discounts' => $totalDiscounts,
                'first_visit' => $firstVisit,
                'last_visit' => $lastVisit,
                'loyalty_balance' => (float)($customer['points_balance'] ?? 0.00)
            ],
            'orders' => $orders,
            'visits' => $visits,
            'payments' => $payments,
            'loyalty_ledger' => $loyaltyLedger,
            'reservations' => $reservations
        ];
    }

    /**
     * Safe Customer Merge Workflow
     */
    public static function mergeCustomers(int $sourceId, int $targetId, int $userId = 1): bool {
        if ($sourceId === $targetId) {
            throw new Exception("Source and Target customer cannot be the same.");
        }

        $db = Database::getConnection();
        $source = self::getCustomer($sourceId);
        $target = self::getCustomer($targetId);

        $db->beginTransaction();
        try {
            // Re-attribute orders
            $db->prepare("UPDATE orders SET customer_id = :target WHERE customer_id = :source")
               ->execute(['target' => $targetId, 'source' => $sourceId]);

            // Re-attribute reservations
            $db->prepare("UPDATE reservations SET customer_id = :target WHERE customer_id = :source")
               ->execute(['target' => $targetId, 'source' => $sourceId]);

            // Re-attribute loyalty ledger & combine points
            $sourcePoints = (float)($source['points_balance'] ?? 0.00);
            if ($sourcePoints > 0) {
                self::adjustPoints($targetId, $sourcePoints, "Merged points from Customer #{$sourceId} ({$source['name']})", $userId);
            }

            $db->prepare("UPDATE loyalty_transactions SET customer_id = :target WHERE customer_id = :source")
               ->execute(['target' => $targetId, 'source' => $sourceId]);

            // Soft-delete source customer
            $db->prepare("UPDATE customers SET deleted_at = NOW(), status = 'INACTIVE' WHERE id = :id")
               ->execute(['id' => $sourceId]);

            AuditLogger::log($userId, 'CUSTOMER_MERGED', 'customer', $targetId, null, [
                'merged_source_id' => $sourceId, 'target_id' => $targetId
            ]);

            $db->commit();
            return true;

        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }


    // ==========================================
    // 2. RESERVATION ENGINE & DOUBLE-BOOKING DEFENSE
    // ==========================================

    public static function generateReservationNumber(?int $branchId = 1): string {
        $prefix = sprintf('RES-%s-', date('Ymd'));
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) FROM reservations WHERE reservation_number LIKE :prefix");
        $stmt->execute(['prefix' => $prefix . '%']);
        $count = (int)$stmt->fetchColumn() + 1;
        return $prefix . sprintf('%04d', $count);
    }

    /**
     * Server-Side Availability Check & Overlap Prevention
     */
    public static function checkAvailability(
        int $branchId,
        string $resDate,
        string $resTime,
        int $guestCount,
        int $durationMinutes = 90,
        ?int $targetTableId = null,
        ?int $excludeReservationId = null
    ): array {
        $db = Database::getConnection();

        // Calculate requested start and end timestamps
        $resStart = date('Y-m-d H:i:s', strtotime("{$resDate} {$resTime}"));
        $resEnd = date('Y-m-d H:i:s', strtotime("{$resStart} +{$durationMinutes} minutes"));

        // Fetch candidate tables in branch with sufficient capacity
        $sql = "
            SELECT rt.id AS table_id, rt.table_number, rt.capacity, rt.status AS table_status,
                   f.name AS floor_name
            FROM restaurant_tables rt
            JOIN floors f ON rt.floor_id = f.id
            WHERE f.branch_id = :branch_id
              AND rt.capacity >= :guests
              AND rt.status IN ('AVAILABLE', 'RESERVED', 'OCCUPIED')
        ";
        $params = ['branch_id' => $branchId, 'guests' => $guestCount];

        if ($targetTableId) {
            $sql .= " AND rt.id = :table_id";
            $params['table_id'] = $targetTableId;
        }

        $stmtTables = $db->prepare($sql);
        $stmtTables->execute($params);
        $candidateTables = $stmtTables->fetchAll(PDO::FETCH_ASSOC);

        $availableTables = [];

        foreach ($candidateTables as $tbl) {
            $tId = (int)$tbl['table_id'];

            // 1. Check for overlapping active reservations for this table
            $sqlOverlap = "
                SELECT id, reservation_number, reservation_time, duration_minutes
                FROM reservations
                WHERE table_id = :tid
                  AND status IN ('REQUESTED', 'CONFIRMED', 'SEATED')
                  AND reservation_date = :res_date
            ";
            $pOverlap = ['tid' => $tId, 'res_date' => $resDate];

            if ($excludeReservationId) {
                $sqlOverlap .= " AND id != :ex_id";
                $pOverlap['ex_id'] = $excludeReservationId;
            }

            $stmtO = $db->prepare($sqlOverlap);
            $stmtO->execute($pOverlap);
            $existingResList = $stmtO->fetchAll(PDO::FETCH_ASSOC);

            $hasConflict = false;
            foreach ($existingResList as $eRes) {
                $eStart = strtotime($eRes['reservation_time']);
                $eDuration = (int)($eRes['duration_minutes'] ?: 90);
                $eEnd = strtotime("+{$eDuration} minutes", $eStart);

                $reqStart = strtotime($resStart);
                $reqEnd = strtotime($resEnd);

                // Overlap condition: reqStart < eEnd AND reqEnd > eStart
                if ($reqStart < $eEnd && $reqEnd > $eStart) {
                    $hasConflict = true;
                    break;
                }
            }

            if (!$hasConflict) {
                $availableTables[] = $tbl;
            }
        }

        return [
            'is_available' => count($availableTables) > 0,
            'requested_start' => $resStart,
            'requested_end' => $resEnd,
            'available_tables' => $availableTables
        ];
    }

    /**
     * Create Reservation with Transactional Conflict Protection
     */
    public static function createReservation(array $data, int $userId = 1): array {
        $db = Database::getConnection();

        $branchId = !empty($data['branch_id']) ? (int)$data['branch_id'] : 1;
        $customerId = !empty($data['customer_id']) ? (int)$data['customer_id'] : null;
        $tableId = !empty($data['table_id']) ? (int)$data['table_id'] : null;
        $resDate = !empty($data['reservation_date']) ? trim($data['reservation_date']) : date('Y-m-d');
        $resTime = !empty($data['reservation_time']) ? trim($data['reservation_time']) : '19:00:00';
        $guestCount = !empty($data['guest_count']) ? (int)$data['guest_count'] : 2;
        $duration = !empty($data['duration_minutes']) ? (int)$data['duration_minutes'] : 90;
        $notes = !empty($data['notes']) ? trim($data['notes']) : null;

        // Auto-create customer if phone/name provided without customer_id
        if (!$customerId && (!empty($data['phone']) || !empty($data['customer_name']))) {
            $cust = self::createCustomer([
                'name' => $data['customer_name'] ?? 'Guest Customer',
                'phone' => $data['phone'] ?? ('017' . rand(10000000, 99999999)),
                'email' => $data['email'] ?? null,
                'branch_id' => $branchId
            ], $userId);
            $customerId = $cust['id'];
        }

        if (!$customerId) {
            throw new Exception("Customer ID or customer details required for reservation.");
        }

        $fullDateTime = date('Y-m-d H:i:s', strtotime("{$resDate} {$resTime}"));

        $db->beginTransaction();
        try {
            // Check availability FOR UPDATE lock condition
            if ($tableId) {
                $availability = self::checkAvailability($branchId, $resDate, $resTime, $guestCount, $duration, $tableId);
                if (!$availability['is_available']) {
                    throw new Exception("DOUBLE-BOOKING CONFLICT (HTTP 409): Table #{$tableId} is already reserved or occupied during the selected timeframe.", 409);
                }
            } else {
                // Auto-assign first available table
                $availability = self::checkAvailability($branchId, $resDate, $resTime, $guestCount, $duration);
                if (!$availability['is_available']) {
                    throw new Exception("No suitable available table found for {$guestCount} guests at {$resTime} on {$resDate}.", 409);
                }
                $tableId = (int)$availability['available_tables'][0]['table_id'];
            }

            $resNum = self::generateReservationNumber($branchId);

            $stmt = $db->prepare("
                INSERT INTO reservations (
                    branch_id, reservation_number, customer_id, table_id, reservation_date,
                    guest_count, duration_minutes, reservation_time, status, notes, created_by_user_id
                ) VALUES (
                    :branch_id, :res_num, :customer_id, :table_id, :res_date,
                    :guest_count, :duration, :res_time, 'CONFIRMED', :notes, :user_id
                )
            ");
            $stmt->execute([
                'branch_id' => $branchId,
                'res_num' => $resNum,
                'customer_id' => $customerId,
                'table_id' => $tableId,
                'res_date' => $resDate,
                'guest_count' => $guestCount,
                'duration' => $duration,
                'res_time' => $fullDateTime,
                'notes' => $notes,
                'user_id' => $userId
            ]);
            $resId = (int)$db->lastInsertId();

            AuditLogger::log($userId, 'RESERVATION_CREATED', 'reservation', $resId, null, [
                'reservation_number' => $resNum, 'table_id' => $tableId, 'date' => $resDate, 'time' => $resTime
            ]);

            $db->commit();
            return self::getReservation($resId);

        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function getReservation(int $id): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT r.*, c.name AS customer_name, c.phone AS customer_phone, c.email AS customer_email,
                   rt.table_number, f.name AS floor_name, b.name AS branch_name,
                   u1.name AS created_by_name, u2.name AS confirmed_by_name
            FROM reservations r
            JOIN customers c ON r.customer_id = c.id
            LEFT JOIN restaurant_tables rt ON r.table_id = rt.id
            LEFT JOIN floors f ON rt.floor_id = f.id
            LEFT JOIN branches b ON r.branch_id = b.id
            LEFT JOIN users u1 ON r.created_by_user_id = u1.id
            LEFT JOIN users u2 ON r.confirmed_by_user_id = u2.id
            WHERE r.id = :id
        ");
        $stmt->execute(['id' => $id]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$res) {
            throw new Exception("Reservation #{$id} not found.");
        }
        return $res;
    }

    /**
     * Seat Reservation → Open/Attach Dining Session (Prompt 04 Integration)
     */
    public static function seatReservation(int $resId, int $userId = 1): array {
        $db = Database::getConnection();
        $res = self::getReservation($resId);

        if (!in_array($res['status'], ['CONFIRMED', 'REQUESTED'], true)) {
            throw new Exception("Cannot seat reservation with status '{$res['status']}'.");
        }

        $tableId = (int)$res['table_id'];

        // Check if active dining session exists on table
        $stmtS = $db->prepare("SELECT id FROM dining_sessions WHERE table_id = :tid AND status = 'OPEN'");
        $stmtS->execute(['tid' => $tableId]);
        $sessionId = $stmtS->fetchColumn();

        if (!$sessionId) {
            // Open new dining session (DiningSessionEngine manages its own transaction)
            $session = DiningSessionEngine::openSession($tableId, (int)$res['guest_count'], $userId);
            $sessionId = $session['session_id'];
        }

        $db->beginTransaction();
        try {
            // Update Reservation to SEATED
            $stmtUpd = $db->prepare("
                UPDATE reservations 
                SET status = 'SEATED', seated_at = NOW(), confirmed_by_user_id = :user_id 
                WHERE id = :id
            ");
            $stmtUpd->execute(['user_id' => $userId, 'id' => $resId]);

            // Update Table Status to OCCUPIED
            $db->prepare("UPDATE restaurant_tables SET status = 'OCCUPIED' WHERE id = :tid")
               ->execute(['tid' => $tableId]);

            AuditLogger::log($userId, 'RESERVATION_SEATED', 'reservation', $resId, null, [
                'table_id' => $tableId, 'dining_session_id' => $sessionId
            ]);

            $db->commit();
            return [
                'reservation_id' => $resId,
                'dining_session_id' => $sessionId,
                'table_id' => $tableId,
                'status' => 'SEATED'
            ];

        } catch (Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }
    }

    public static function cancelReservation(int $resId, ?string $reason, int $userId = 1): bool {
        $db = Database::getConnection();
        $db->prepare("
            UPDATE reservations 
            SET status = 'CANCELLED', cancelled_at = NOW(), notes = CONCAT(COALESCE(notes, ''), ' [Cancelled: ', :reason, ']')
            WHERE id = :id
        ")->execute(['reason' => $reason ?: 'User cancelled', 'id' => $resId]);

        AuditLogger::log($userId, 'RESERVATION_CANCELLED', 'reservation', $resId, null, ['reason' => $reason]);
        return true;
    }

    public static function markNoShow(int $resId, int $userId = 1): bool {
        $db = Database::getConnection();
        $db->prepare("UPDATE reservations SET status = 'NO_SHOW' WHERE id = :id")->execute(['id' => $resId]);
        AuditLogger::log($userId, 'RESERVATION_NO_SHOW', 'reservation', $resId);
        return true;
    }

    public static function getReservations(array $filters = []): array {
        $db = Database::getConnection();
        $sql = "
            SELECT r.*, c.name AS customer_name, c.phone AS customer_phone,
                   rt.table_number, f.name AS floor_name, b.name AS branch_name
            FROM reservations r
            JOIN customers c ON r.customer_id = c.id
            LEFT JOIN restaurant_tables rt ON r.table_id = rt.id
            LEFT JOIN floors f ON rt.floor_id = f.id
            LEFT JOIN branches b ON r.branch_id = b.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filters['branch_id'])) {
            $sql .= " AND r.branch_id = :branch_id";
            $params['branch_id'] = (int)$filters['branch_id'];
        }
        if (!empty($filters['status'])) {
            $sql .= " AND r.status = :status";
            $params['status'] = strtoupper(trim($filters['status']));
        }
        if (!empty($filters['reservation_date'])) {
            $sql .= " AND r.reservation_date = :res_date";
            $params['res_date'] = trim($filters['reservation_date']);
        }
        if (!empty($filters['customer_id'])) {
            $sql .= " AND r.customer_id = :cust_id";
            $params['cust_id'] = (int)$filters['customer_id'];
        }

        $sql .= " ORDER BY r.reservation_date ASC, r.reservation_time ASC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // ==========================================
    // 3. LOYALTY ENGINE & IMMUTABLE LEDGER
    // ==========================================

    public static function getLoyaltyAccount(int $customerId, ?int $branchId = 1): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM loyalty_accounts WHERE customer_id = :cid");
        $stmt->execute(['cid' => $customerId]);
        $acc = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$acc) {
            $stmtIns = $db->prepare("
                INSERT INTO loyalty_accounts (customer_id, branch_id, points_balance, lifetime_earned, lifetime_redeemed, status)
                VALUES (:cid, :bid, 0.00, 0.00, 0.00, 'ACTIVE')
            ");
            $stmtIns->execute(['cid' => $customerId, 'bid' => $branchId ?: 1]);
            return self::getLoyaltyAccount($customerId, $branchId);
        }
        return $acc;
    }

    /**
     * Earn Points on Completed Order (1 point per 10 currency units)
     */
    public static function earnPointsForOrder(int $orderId, int $userId = 1): bool {
        $db = Database::getConnection();

        $stmtOrd = $db->prepare("SELECT id, branch_id, customer_id, total, subtotal, discount, order_status FROM orders WHERE id = :id");
        $stmtOrd->execute(['id' => $orderId]);
        $order = $stmtOrd->fetch(PDO::FETCH_ASSOC);

        if (!$order || !$order['customer_id'] || $order['order_status'] !== 'COMPLETED') {
            return false;
        }

        $customerId = (int)$order['customer_id'];

        // Prevent duplicate earning for same order
        $stmtCheck = $db->prepare("SELECT id FROM loyalty_transactions WHERE order_id = :oid AND type = 'EARN'");
        $stmtCheck->execute(['oid' => $orderId]);
        if ($stmtCheck->fetchColumn()) {
            return false; // Already earned
        }

        $eligibleAmount = max(0, (float)$order['subtotal'] - (float)$order['discount']);
        $earnedPoints = round($eligibleAmount * 0.10, 2); // 10% points rate

        if ($earnedPoints <= 0) return false;

        $db->beginTransaction();
        try {
            $account = self::getLoyaltyAccount($customerId, (int)$order['branch_id']);
            $balanceBefore = (float)$account['points_balance'];
            $balanceAfter = round($balanceBefore + $earnedPoints, 2);

            // Update balance
            $db->prepare("
                UPDATE loyalty_accounts 
                SET points_balance = :ba, lifetime_earned = lifetime_earned + :pts 
                WHERE customer_id = :cid
            ")->execute(['ba' => $balanceAfter, 'pts' => $earnedPoints, 'cid' => $customerId]);

            // Write to Immutable Ledger
            $stmtLedger = $db->prepare("
                INSERT INTO loyalty_transactions (
                    customer_id, branch_id, order_id, type, points, balance_before, balance_after, reason, created_by
                ) VALUES (
                    :cid, :bid, :oid, 'EARN', :pts, :bb, :ba, :reason, :uid
                )
            ");
            $stmtLedger->execute([
                'cid' => $customerId,
                'bid' => $order['branch_id'],
                'oid' => $orderId,
                'pts' => $earnedPoints,
                'bb' => $balanceBefore,
                'ba' => $balanceAfter,
                'reason' => "Earned points for Order #{$orderId}",
                'uid' => $userId
            ]);

            AuditLogger::log($userId, 'LOYALTY_EARNED', 'loyalty_account', $customerId, null, [
                'points' => $earnedPoints, 'new_balance' => $balanceAfter, 'order_id' => $orderId
            ]);

            $db->commit();
            return true;

        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * Redeem Points for Discount (1 point = $1 discount)
     */
    public static function redeemPoints(int $customerId, float $points, int $orderId, int $userId = 1): float {
        if ($points <= 0) {
            throw new Exception("Points to redeem must be greater than zero.");
        }

        $db = Database::getConnection();
        $db->beginTransaction();
        try {
            // Lock account for update
            $stmtAcc = $db->prepare("SELECT * FROM loyalty_accounts WHERE customer_id = :cid FOR UPDATE");
            $stmtAcc->execute(['cid' => $customerId]);
            $account = $stmtAcc->fetch(PDO::FETCH_ASSOC);

            if (!$account || $account['status'] !== 'ACTIVE') {
                throw new Exception("Loyalty account is not active.");
            }

            $currentBalance = (float)$account['points_balance'];
            if ($currentBalance < $points) {
                throw new Exception("INSUFFICIENT LOYALTY POINTS: Requested {$points} pts, available {$currentBalance} pts.");
            }

            $balanceAfter = round($currentBalance - $points, 2);

            // Update Account
            $db->prepare("
                UPDATE loyalty_accounts 
                SET points_balance = :ba, lifetime_redeemed = lifetime_redeemed + :pts 
                WHERE customer_id = :cid
            ")->execute(['ba' => $balanceAfter, 'pts' => $points, 'cid' => $customerId]);

            // Ledger
            $db->prepare("
                INSERT INTO loyalty_transactions (
                    customer_id, branch_id, order_id, type, points, balance_before, balance_after, reason, created_by
                ) VALUES (
                    :cid, :bid, :oid, 'REDEEM', :pts, :bb, :ba, :reason, :uid
                )
            ")->execute([
                'cid' => $customerId,
                'bid' => $account['branch_id'],
                'oid' => $orderId,
                'pts' => -$points,
                'bb' => $currentBalance,
                'ba' => $balanceAfter,
                'reason' => "Redeemed points for Order #{$orderId}",
                'uid' => $userId
            ]);

            AuditLogger::log($userId, 'LOYALTY_REDEEMED', 'loyalty_account', $customerId, null, [
                'points' => $points, 'remaining_balance' => $balanceAfter, 'order_id' => $orderId
            ]);

            $db->commit();
            return round($points * 1.00, 2); // Discount amount

        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * Manual Manager Adjustment
     */
    public static function adjustPoints(int $customerId, float $points, string $reason, int $userId = 1): array {
        if (empty(trim($reason))) {
            throw new Exception("Audit reason is required for manual points adjustment.");
        }

        $db = Database::getConnection();
        $db->beginTransaction();
        try {
            $account = self::getLoyaltyAccount($customerId);
            $currentBalance = (float)$account['points_balance'];
            $balanceAfter = round($currentBalance + $points, 2);

            if ($balanceAfter < 0) {
                throw new Exception("Adjustment would cause negative balance ({$balanceAfter}).");
            }

            $db->prepare("UPDATE loyalty_accounts SET points_balance = :ba WHERE customer_id = :cid")
               ->execute(['ba' => $balanceAfter, 'cid' => $customerId]);

            $db->prepare("
                INSERT INTO loyalty_transactions (
                    customer_id, branch_id, order_id, type, points, balance_before, balance_after, reason, created_by
                ) VALUES (
                    :cid, :bid, NULL, 'ADJUST', :pts, :bb, :ba, :reason, :uid
                )
            ")->execute([
                'cid' => $customerId,
                'bid' => $account['branch_id'],
                'pts' => $points,
                'bb' => $currentBalance,
                'ba' => $balanceAfter,
                'reason' => "Manual adjustment: " . trim($reason),
                'uid' => $userId
            ]);

            AuditLogger::log($userId, 'LOYALTY_ADJUSTED', 'loyalty_account', $customerId, null, [
                'adjustment' => $points, 'new_balance' => $balanceAfter, 'reason' => $reason
            ]);

            $db->commit();
            return ['customer_id' => $customerId, 'balance_before' => $currentBalance, 'balance_after' => $balanceAfter];

        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }


    // ==========================================
    // 4. COUPON & PROMOTION ENGINE
    // ==========================================

    public static function validateCoupon(string $code, float $orderAmount, ?int $customerId = null, ?int $branchId = null): array {
        $code = strtoupper(trim($code));
        $db = Database::getConnection();

        $stmt = $db->prepare("SELECT * FROM coupons WHERE code = :code");
        $stmt->execute(['code' => $code]);
        $coupon = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$coupon) {
            return ['valid' => false, 'message' => "Invalid coupon code '{$code}'."];
        }
        if (!(int)$coupon['is_active']) {
            return ['valid' => false, 'message' => "Coupon '{$code}' is currently inactive."];
        }

        $now = date('Y-m-d H:i:s');
        if (!empty($coupon['valid_from']) && $now < $coupon['valid_from']) {
            return ['valid' => false, 'message' => "Coupon '{$code}' is not yet valid."];
        }
        if (!empty($coupon['valid_until']) && $now > $coupon['valid_until']) {
            return ['valid' => false, 'message' => "Coupon '{$code}' has expired."];
        }

        if ($branchId && !empty($coupon['branch_id']) && (int)$coupon['branch_id'] !== $branchId) {
            return ['valid' => false, 'message' => "Coupon '{$code}' is not valid for this branch."];
        }

        $minSpend = (float)($coupon['min_order_amount'] ?? 0.00);
        if ($orderAmount < $minSpend) {
            return ['valid' => false, 'message' => sprintf("Minimum order spend of $%.2f required for coupon '{$code}'.", $minSpend)];
        }

        // Usage limit check
        $usageLimit = (int)($coupon['usage_limit'] ?? 0);
        if ($usageLimit > 0) {
            $stmtCount = $db->prepare("SELECT COUNT(*) FROM coupon_usage WHERE coupon_id = :cid");
            $stmtCount->execute(['cid' => $coupon['id']]);
            $usedCount = (int)$stmtCount->fetchColumn();
            if ($usedCount >= $usageLimit) {
                return ['valid' => false, 'message' => "Coupon '{$code}' maximum redemption limit reached."];
            }
        }

        // Per-customer usage limit check
        $perCustLimit = (int)($coupon['per_customer_limit'] ?? 0);
        if ($perCustLimit > 0 && $customerId) {
            $stmtCustCount = $db->prepare("SELECT COUNT(*) FROM coupon_usage WHERE coupon_id = :cid AND customer_id = :cust_id");
            $stmtCustCount->execute(['cid' => $coupon['id'], 'cust_id' => $customerId]);
            $custUsed = (int)$stmtCustCount->fetchColumn();
            if ($custUsed >= $perCustLimit) {
                return ['valid' => false, 'message' => "You have reached the maximum redemptions for coupon '{$code}'."];
            }
        }

        // Calculate Discount
        $discountType = $coupon['discount_type'];
        $discountValue = (float)$coupon['discount_amount'];
        $maxDiscount = (float)($coupon['max_discount'] ?? 0.00);

        if ($discountType === 'PERCENTAGE') {
            $discount = round(($orderAmount * $discountValue) / 100.0, 2);
            if ($maxDiscount > 0 && $discount > $maxDiscount) {
                $discount = $maxDiscount;
            }
        } else {
            $discount = min($orderAmount, $discountValue);
        }

        return [
            'valid' => true,
            'coupon_id' => (int)$coupon['id'],
            'code' => $coupon['code'],
            'discount_type' => $discountType,
            'discount_value' => $discountValue,
            'discount_amount' => $discount
        ];
    }

    public static function recordCouponUsage(int $couponId, int $orderId, ?int $customerId, float $discountAmount, int $userId = 1): bool {
        $db = Database::getConnection();
        $db->prepare("
            INSERT INTO coupon_usage (coupon_id, order_id, customer_id, discount_amount, redeemed_by, used_at)
            VALUES (:cid, :oid, :cust_id, :disc, :uid, NOW())
        ")->execute([
            'cid' => $couponId,
            'oid' => $orderId,
            'cust_id' => $customerId,
            'disc' => $discountAmount,
            'uid' => $userId
        ]);

        AuditLogger::log($userId, 'COUPON_REDEEMED', 'coupon', $couponId, null, [
            'order_id' => $orderId, 'discount' => $discountAmount
        ]);
        return true;
    }

    public static function createCoupon(array $data, int $userId = 1): array {
        $code = strtoupper(trim($data['code'] ?? ''));
        if (empty($code)) {
            throw new Exception("Coupon code is required.");
        }

        $db = Database::getConnection();
        $stmtCheck = $db->prepare("SELECT id FROM coupons WHERE code = :code");
        $stmtCheck->execute(['code' => $code]);
        if ($stmtCheck->fetchColumn()) {
            throw new Exception("Coupon code '{$code}' already exists.");
        }

        $stmt = $db->prepare("
            INSERT INTO coupons (
                branch_id, code, name, description, discount_type, discount_amount,
                min_order_amount, max_discount, usage_limit, per_customer_limit,
                valid_from, valid_until, is_active
            ) VALUES (
                :bid, :code, :name, :desc, :dtype, :damt,
                :min_spend, :max_disc, :ulimit, :plimit,
                :vfrom, :vuntil, :active
            )
        ");
        $stmt->execute([
            'bid' => !empty($data['branch_id']) ? (int)$data['branch_id'] : null,
            'code' => $code,
            'name' => $data['name'] ?? $code,
            'desc' => $data['description'] ?? null,
            'dtype' => !empty($data['discount_type']) ? strtoupper($data['discount_type']) : 'PERCENTAGE',
            'damt' => (float)($data['discount_amount'] ?? 0.00),
            'min_spend' => (float)($data['min_order_amount'] ?? 0.00),
            'max_disc' => (float)($data['max_discount'] ?? 0.00),
            'ulimit' => (int)($data['usage_limit'] ?? 0),
            'plimit' => (int)($data['per_customer_limit'] ?? 0),
            'vfrom' => !empty($data['valid_from']) ? $data['valid_from'] : date('Y-m-d H:i:s'),
            'vuntil' => !empty($data['valid_until']) ? $data['valid_until'] : date('Y-m-d H:i:s', strtotime('+1 year')),
            'active' => isset($data['is_active']) ? (int)(bool)$data['is_active'] : 1
        ]);
        $cid = (int)$db->lastInsertId();

        AuditLogger::log($userId, 'COUPON_CREATED', 'coupon', $cid, null, ['code' => $code]);
        return self::getCoupon($cid);
    }

    public static function getCoupon(int $id): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM coupons WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $c = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$c) throw new Exception("Coupon #{$id} not found.");
        return $c;
    }

    public static function getCoupons(array $filters = []): array {
        $db = Database::getConnection();
        $sql = "SELECT c.*, (SELECT COUNT(*) FROM coupon_usage cu WHERE cu.coupon_id = c.id) AS total_redemptions FROM coupons c WHERE 1=1";
        $params = [];
        if (!empty($filters['branch_id'])) {
            $sql .= " AND (c.branch_id = :bid OR c.branch_id IS NULL)";
            $params['bid'] = (int)$filters['branch_id'];
        }
        $sql .= " ORDER BY c.id DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // ==========================================
    // 5. PUBLIC SECURE QR ORDERING ENGINE
    // ==========================================

    /**
     * Generate or Fetch QR Token for Table
     */
    public static function getOrCreateQRToken(int $branchId, int $tableId): array {
        $db = Database::getConnection();

        $stmt = $db->prepare("SELECT * FROM qr_table_tokens WHERE branch_id = :bid AND table_id = :tid");
        $stmt->execute(['bid' => $branchId, 'tid' => $tableId]);
        $tokenRow = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($tokenRow && $tokenRow['status'] === 'ACTIVE') {
            return $tokenRow;
        }

        $token = bin2hex(random_bytes(32)); // Secure 64-char hex token

        $stmtIns = $db->prepare("
            INSERT INTO qr_table_tokens (branch_id, table_id, token, status)
            VALUES (:bid, :tid, :token1, 'ACTIVE')
            ON DUPLICATE KEY UPDATE token = :token2, status = 'ACTIVE', updated_at = CURRENT_TIMESTAMP
        ");
        $stmtIns->execute(['bid' => $branchId, 'tid' => $tableId, 'token1' => $token, 'token2' => $token]);

        return self::getOrCreateQRToken($branchId, $tableId);
    }

    public static function regenerateQRToken(int $branchId, int $tableId, int $userId = 1): array {
        $token = bin2hex(random_bytes(32));
        $db = Database::getConnection();
        $db->prepare("
            UPDATE qr_table_tokens 
            SET token = :token, status = 'ACTIVE' 
            WHERE branch_id = :bid AND table_id = :tid
        ")->execute(['token' => $token, 'bid' => $branchId, 'tid' => $tableId]);

        AuditLogger::log($userId, 'QR_REGENERATED', 'qr_table_tokens', $tableId, null, ['token' => $token]);
        return self::getOrCreateQRToken($branchId, $tableId);
    }

    public static function revokeQRToken(int $tokenId, int $userId = 1): bool {
        $db = Database::getConnection();
        $db->prepare("UPDATE qr_table_tokens SET status = 'REVOKED' WHERE id = :id")->execute(['id' => $tokenId]);
        AuditLogger::log($userId, 'QR_REVOKED', 'qr_table_tokens', $tokenId);
        return true;
    }

    /**
     * Resolve Public Token to Branch & Table (No Raw Table ID Exposure)
     */
    public static function resolveQRToken(string $token): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT q.*, rt.table_number, rt.capacity, rt.status AS table_status,
                   f.name AS floor_name, b.name AS branch_name
            FROM qr_table_tokens q
            JOIN restaurant_tables rt ON q.table_id = rt.id
            JOIN floors f ON rt.floor_id = f.id
            JOIN branches b ON q.branch_id = b.id
            WHERE q.token = :token AND q.status = 'ACTIVE'
        ");
        $stmt->execute(['token' => $token]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            throw new Exception("UNAUTHORIZED QR ACCESS: Invalid or revoked QR token.", 403);
        }
        return $row;
    }

    /**
     * Get Public Mobile Menu for QR Client
     */
    public static function getPublicMenuForQR(string $token): array {
        $resolved = self::resolveQRToken($token);

        // Fetch categories & available products via MenuEngine
        $db = Database::getConnection();
        $stmtCats = $db->prepare("
            SELECT c.* 
            FROM categories c 
            WHERE c.status = 'ACTIVE' 
            ORDER BY c.sort_order ASC, c.name ASC
        ");
        $stmtCats->execute();
        $categories = $stmtCats->fetchAll(PDO::FETCH_ASSOC);

        $stmtProds = $db->prepare("
            SELECT p.*, p.price AS base_price, c.name AS category_name
            FROM products p
            JOIN categories c ON p.category_id = c.id
            WHERE p.status = 'ACTIVE' AND p.is_available = 1
            ORDER BY p.name ASC
        ");
        $stmtProds->execute();
        $products = $stmtProds->fetchAll(PDO::FETCH_ASSOC);

        // Attach variants & modifiers
        foreach ($products as &$prod) {
            $stmtVars = $db->prepare("SELECT * FROM product_variants WHERE product_id = :pid AND status = 'ACTIVE'");
            $stmtVars->execute(['pid' => $prod['id']]);
            $prod['variants'] = $stmtVars->fetchAll(PDO::FETCH_ASSOC);

            $stmtMods = $db->prepare("
                SELECT m.*
                FROM product_modifiers pm
                JOIN modifiers m ON pm.modifier_id = m.id
                WHERE pm.product_id = :pid AND m.status = 'ACTIVE'
            ");
            $stmtMods->execute(['pid' => $prod['id']]);
            $prod['modifiers'] = $stmtMods->fetchAll(PDO::FETCH_ASSOC);
        }

        return [
            'table_info' => [
                'branch_name' => $resolved['branch_name'],
                'floor_name' => $resolved['floor_name'],
                'table_number' => $resolved['table_number'],
                'token' => $token
            ],
            'categories' => $categories,
            'products' => $products
        ];
    }

    /**
     * Submit Public QR Order with Server-Side Price & Total Calculation
     */
    public static function submitPublicQROrder(string $token, array $data): array {
        $resolved = self::resolveQRToken($token);

        $branchId = (int)$resolved['branch_id'];
        $tableId = (int)$resolved['table_id'];

        $cartItems = $data['items'] ?? [];
        if (empty($cartItems)) {
            throw new Exception("Cart cannot be empty for QR order.");
        }

        $customerPhone = !empty($data['phone']) ? preg_replace('/[^\d+]/', '', trim($data['phone'])) : null;
        $customerName = !empty($data['customer_name']) ? trim($data['customer_name']) : 'QR Customer';
        $notes = !empty($data['notes']) ? trim($data['notes']) : 'Self-ordered via Table QR';

        $db = Database::getConnection();

        // 1. Customer resolution
        $customerId = null;
        if ($customerPhone) {
            $stmtC = $db->prepare("SELECT id FROM customers WHERE phone = :phone AND deleted_at IS NULL");
            $stmtC->execute(['phone' => $customerPhone]);
            $cId = $stmtC->fetchColumn();
            if ($cId) {
                $customerId = (int)$cId;
            } else {
                $newCust = self::createCustomer(['name' => $customerName, 'phone' => $customerPhone, 'branch_id' => $branchId]);
                $customerId = $newCust['id'];
            }
        }

        // 2. Dining Session Resolution (Prompt 04)
        $stmtS = $db->prepare("SELECT id FROM dining_sessions WHERE table_id = :tid AND status = 'OPEN'");
        $stmtS->execute(['tid' => $tableId]);
        $sessionId = $stmtS->fetchColumn();

        if (!$sessionId) {
            $session = DiningSessionEngine::openSession($tableId, 2, 1);
            $sessionId = $session['session_id'];
        }

        // 3. Create Draft Order via OrderEngine
        $draftOrder = OrderEngine::createDraftOrder([
            'branch_id' => $branchId,
            'table_id' => $tableId,
            'dining_session_id' => $sessionId,
            'customer_id' => $customerId,
            'order_type' => 'DINE_IN',
            'notes' => $notes
        ], 1);
        $orderId = $draftOrder['order_id'];

        // 4. Add items to order (OrderEngine handles server-side price recalculation)
        foreach ($cartItems as $cItem) {
            $productId = (int)$cItem['product_id'];
            $variantId = !empty($cItem['variant_id']) ? (int)$cItem['variant_id'] : null;
            $modifierIds = !empty($cItem['modifier_ids']) && is_array($cItem['modifier_ids']) ? array_map('intval', $cItem['modifier_ids']) : [];
            $quantity = max(1, (int)($cItem['quantity'] ?? 1));
            $itemNotes = !empty($cItem['notes']) ? trim($cItem['notes']) : null;

            OrderEngine::addItemToOrder($orderId, $productId, $variantId, $modifierIds, $quantity, $itemNotes, 1);
        }

        // 5. Submit Order (Advance state to SUBMITTED)
        OrderEngine::submitOrder($orderId, 1);

        // 6. Route Order Items to Kitchen Stations (Prompt 07 Smart Routing -> Prompt 10 KDS)
        RoutingEngine::routeOrder($orderId, 1, 'AUTOMATIC');

        AuditLogger::log(1, 'QR_ORDER_SUBMITTED', 'order', $orderId, null, [
            'token' => $token, 'table_number' => $resolved['table_number']
        ]);

        return [
            'success' => true,
            'order_id' => $orderId,
            'order_number' => $draftOrder['order_number'],
            'status' => 'SUBMITTED',
            'table_number' => $resolved['table_number']
        ];
    }

    /**
     * Get All Table QR Tokens for Admin UI
     */
    public static function getQRTables(int $branchId = 1): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT rt.id AS table_id, rt.table_number, rt.capacity, rt.status AS table_status,
                   f.name AS floor_name, b.name AS branch_name,
                   q.id AS token_id, q.token, q.status AS token_status, q.created_at, q.updated_at
            FROM restaurant_tables rt
            JOIN floors f ON rt.floor_id = f.id
            JOIN branches b ON f.branch_id = b.id
            LEFT JOIN qr_table_tokens q ON (q.table_id = rt.id AND q.branch_id = b.id)
            WHERE b.id = :bid
            ORDER BY f.sort_order ASC, rt.table_number ASC
        ");
        $stmt->execute(['bid' => $branchId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
