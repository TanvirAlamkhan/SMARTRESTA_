<?php
/**
 * SMARTRESTA API Endpoint: CRM Customers Management
 * GET/POST/PUT /api/v1/crm/customers.php
 */

require_once __DIR__ . '/../../../core/Response.php';
require_once __DIR__ . '/../../../core/CRMEngine.php';

header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        $filters = $_GET;
        $customers = CRMEngine::searchCustomers($filters);
        Response::json(true, 200, "Customers retrieved successfully.", [
            'count' => count($customers),
            'customers' => $customers
        ]);
    } elseif ($method === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $customer = CRMEngine::createCustomer($input, 1);
        Response::json(true, 201, "Customer created successfully.", $customer);
    } elseif ($method === 'PUT') {
        $input = json_decode(file_get_contents('php://input'), true);
        $id = (int)($input['id'] ?? $_GET['id'] ?? 0);
        if (!$id) {
            Response::json(false, 400, "Customer ID is required for update.");
        }
        $customer = CRMEngine::updateCustomer($id, $input, 1);
        Response::json(true, 200, "Customer profile updated successfully.", $customer);
    } else {
        Response::json(false, 405, "Method Not Allowed");
    }
} catch (Exception $e) {
    $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
    Response::json(false, $code, $e->getMessage());
}
