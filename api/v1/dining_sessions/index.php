<?php
/**
 * SMARTRESTA API Endpoint: Active Dining Sessions List
 * GET /api/v1/dining_sessions/index.php
 */

require_once __DIR__ . '/../../../config/env.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/Response.php';

Auth::requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    Response::json(false, 405, "Method Not Allowed");
}

try {
    Auth::requirePermission('dining_sessions.view');

    $tableId = !empty($_GET['table_id']) ? (int)$_GET['table_id'] : null;
    $status = !empty($_GET['status']) ? strtoupper(trim($_GET['status'])) : 'OPEN';

    $db = Database::getConnection();
    
    $sql = "
        SELECT ds.*, rt.table_number, rt.capacity, f.name AS floor_name,
               u.name AS opened_by_name, c.name AS customer_name, c.phone AS customer_phone,
               o.id AS active_order_id, o.order_number AS active_order_number, o.total AS active_order_total
        FROM dining_sessions ds
        JOIN restaurant_tables rt ON ds.table_id = rt.id
        LEFT JOIN floors f ON rt.floor_id = f.id
        LEFT JOIN users u ON ds.opened_by_user_id = u.id
        LEFT JOIN customers c ON ds.customer_id = c.id
        LEFT JOIN orders o ON ds.id = o.dining_session_id AND o.payment_status != 'PAID' AND o.order_status != 'CANCELLED'
        WHERE 1=1
    ";
    $params = [];

    if ($status) {
        $sql .= " AND ds.status = :status";
        $params['status'] = $status;
    }

    if ($tableId) {
        $sql .= " AND ds.table_id = :table_id";
        $params['table_id'] = $tableId;
    }

    $sql .= " ORDER BY ds.id DESC";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    Response::json(true, 200, "Dining sessions retrieved successfully", $sessions);

} catch (Exception $e) {
    Response::json(false, 400, $e->getMessage());
}
