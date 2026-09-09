<?php
/**
 * SMARTRESTA API Endpoint: Table Reservations Engine
 * GET/POST /api/v1/crm/reservations.php
 */

require_once __DIR__ . '/../../../core/Response.php';
require_once __DIR__ . '/../../../core/CRMEngine.php';

header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        $action = $_GET['action'] ?? '';
        if ($action === 'availability') {
            $branchId = (int)($_GET['branch_id'] ?? 1);
            $resDate = $_GET['reservation_date'] ?? date('Y-m-d');
            $resTime = $_GET['reservation_time'] ?? '19:00:00';
            $guests = (int)($_GET['guest_count'] ?? 2);
            $duration = (int)($_GET['duration_minutes'] ?? 90);
            $tableId = !empty($_GET['table_id']) ? (int)$_GET['table_id'] : null;

            $result = CRMEngine::checkAvailability($branchId, $resDate, $resTime, $guests, $duration, $tableId);
            Response::json(true, 200, "Availability checked.", $result);
        } else {
            $filters = $_GET;
            $reservations = CRMEngine::getReservations($filters);
            Response::json(true, 200, "Reservations retrieved.", ['reservations' => $reservations]);
        }
    } elseif ($method === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $action = $input['action'] ?? 'create';

        if ($action === 'create') {
            $res = CRMEngine::createReservation($input, 1);
            Response::json(true, 201, "Reservation confirmed successfully.", $res);
        } elseif ($action === 'seat') {
            $resId = (int)($input['reservation_id'] ?? 0);
            if (!$resId) Response::json(false, 400, "Reservation ID required.");
            $seated = CRMEngine::seatReservation($resId, 1);
            Response::json(true, 200, "Reservation seated and dining session activated.", $seated);
        } elseif ($action === 'cancel') {
            $resId = (int)($input['reservation_id'] ?? 0);
            $reason = $input['reason'] ?? null;
            if (!$resId) Response::json(false, 400, "Reservation ID required.");
            CRMEngine::cancelReservation($resId, $reason, 1);
            Response::json(true, 200, "Reservation cancelled.");
        } elseif ($action === 'noshow') {
            $resId = (int)($input['reservation_id'] ?? 0);
            if (!$resId) Response::json(false, 400, "Reservation ID required.");
            CRMEngine::markNoShow($resId, 1);
            Response::json(true, 200, "Reservation marked as No-Show.");
        } else {
            Response::json(false, 400, "Invalid action.");
        }
    } else {
        Response::json(false, 405, "Method Not Allowed");
    }
} catch (Exception $e) {
    $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
    Response::json(false, $code, $e->getMessage());
}
