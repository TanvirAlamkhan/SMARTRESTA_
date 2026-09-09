<?php
/**
 * SMARTRESTA Table Management & Floor Map API Endpoint
 * GET /api/v1/tables/index.php  (List Tables with Active Session Details)
 * POST /api/v1/tables/index.php (Create Table)
 * PUT /api/v1/tables/index.php  (Update Table Status e.g. Cleaning, Out of Service)
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/Validation.php';
require_once __DIR__ . '/../../../core/AuditLogger.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../core/Response.php';

Auth::requireAuth();

$method = $_SERVER['REQUEST_METHOD'];
$db = Database::getConnection();

if ($method === 'GET') {
    Auth::requirePermissionOrEmpty('tables.view');
    $floorId = !empty($_GET['floor_id']) ? (int)$_GET['floor_id'] : null;

    if ($db) {
        try {
            $sql = "
                SELECT t.id, t.floor_id, t.table_number, t.capacity, t.status, f.name as floor_name,
                       ds.id as active_session_id, ds.guest_count, ds.opened_at, u.name as waiter_name
                FROM restaurant_tables t
                LEFT JOIN floors f ON t.floor_id = f.id
                LEFT JOIN dining_sessions ds ON t.id = ds.table_id AND ds.status = 'OPEN'
                LEFT JOIN users u ON ds.opened_by_user_id = u.id
                WHERE t.deleted_at IS NULL
            ";
            $params = [];

            if ($floorId) {
                $sql .= " AND t.floor_id = ?";
                $params[] = $floorId;
            }

            $sql .= " ORDER BY f.sort_order ASC, t.table_number ASC";

            $tables = DB::fetchAll($sql, $params);
            Response::json(true, 200, "Tables retrieved successfully", $tables);
        } catch (Exception $e) {
            Response::json(false, 500, "Database error: " . $e->getMessage());
        }
    } else {
        // Fallback Data Mode
        $tables = [
            ["id" => 1, "floor_id" => 1, "table_number" => "T-01", "capacity" => 4, "status" => "AVAILABLE", "floor_name" => "Main Dining Hall"],
            ["id" => 2, "floor_id" => 1, "table_number" => "T-02", "capacity" => 2, "status" => "OCCUPIED", "floor_name" => "Main Dining Hall", "active_session_id" => 101, "guest_count" => 2, "waiter_name" => "Rahim"],
            ["id" => 3, "floor_id" => 2, "table_number" => "T-05", "capacity" => 6, "status" => "OCCUPIED", "floor_name" => "Terrace Lounge", "active_session_id" => 102, "guest_count" => 5, "waiter_name" => "Karim"],
            ["id" => 4, "floor_id" => 2, "table_number" => "T-12", "capacity" => 8, "status" => "WAITING_PAYMENT", "floor_name" => "Terrace Lounge", "active_session_id" => 103, "guest_count" => 7, "waiter_name" => "Rahim"]
        ];
        Response::json(true, 200, "Tables retrieved (fallback mode)", $tables);
    }
} elseif ($method === 'POST') {
    Auth::requirePermission('tables.create');
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

    $validator = new Validation();
    $validator->require($input, 'floor_id', 'Floor Zone')
              ->require($input, 'table_number', 'Table Number')
              ->numeric($input, 'capacity', 1, 'Table Capacity');

    if (!$validator->isValid()) {
        Response::json(false, 422, "Validation failed", null, $validator->getErrors());
    }

    $floorId = (int)$input['floor_id'];
    $tableNumber = trim($input['table_number']);
    $capacity = (int)$input['capacity'];

    if ($db) {
        try {
            // Check uniqueness on floor
            $existing = DB::fetch("SELECT id FROM restaurant_tables WHERE floor_id = ? AND table_number = ? AND deleted_at IS NULL", [$floorId, $tableNumber]);
            if ($existing) {
                Response::json(false, 409, "Table '{$tableNumber}' already exists on this floor.");
            }

            $tableId = DB::insert("
                INSERT INTO restaurant_tables (floor_id, table_number, capacity, status)
                VALUES (?, ?, ?, 'AVAILABLE')
            ", [$floorId, $tableNumber, $capacity]);

            AuditLogger::log('TABLE_CREATED', 'Tables', $tableId, null, ['table_number' => $tableNumber, 'capacity' => $capacity]);

            Response::json(true, 201, "Table '{$tableNumber}' successfully created", ['tableId' => $tableId, 'tableNumber' => $tableNumber]);
        } catch (Exception $e) {
            Response::json(false, 500, "Failed to create table: " . $e->getMessage());
        }
    } else {
        Response::json(true, 201, "Table created (fallback mode)", ['tableId' => rand(100, 999), 'tableNumber' => $tableNumber]);
    }
} elseif ($method === 'PUT') {
    Auth::requirePermission('tables.status_update');
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

    $tableId = (int)($input['table_id'] ?? 0);
    $status = strtoupper(trim($input['status'] ?? ''));

    $allowedStatuses = [TABLE_STATUS_AVAILABLE, TABLE_STATUS_OCCUPIED, TABLE_STATUS_RESERVED, TABLE_STATUS_WAITING_PAYMENT, TABLE_STATUS_CLEANING, TABLE_STATUS_OUT_OF_SERVICE];
    if (!in_array($status, $allowedStatuses, true)) {
        Response::json(false, 422, "Invalid table operational status specified.");
    }

    if ($db) {
        try {
            DB::execute("UPDATE restaurant_tables SET status = ? WHERE id = ?", [$status, $tableId]);
            AuditLogger::log('TABLE_STATUS_CHANGED', 'Tables', $tableId, null, ['status' => $status]);
            Response::json(true, 200, "Table status updated to {$status}");
        } catch (Exception $e) {
            Response::json(false, 500, "Failed to update table status: " . $e->getMessage());
        }
    } else {
        Response::json(true, 200, "Table status updated (fallback mode)");
    }
} else {
    Response::json(false, 405, "Method Not Allowed");
}
