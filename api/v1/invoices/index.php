<?php
/**
 * SMARTRESTA API Endpoint: Order Invoices Lookup
 * GET /api/v1/invoices/index.php?order_id={oid}
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/InvoiceEngine.php';
require_once __DIR__ . '/../../../core/Response.php';

Auth::requireAuth();

try {
    $invoiceId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

    if ($invoiceId) {
        $invoice = InvoiceEngine::getInvoice($invoiceId);
        Response::json(true, 200, "Invoice retrieved successfully", $invoice);
    } else if ($orderId) {
        $invoice = InvoiceEngine::generateInvoice($orderId);
        Response::json(true, 200, "Invoice generated successfully", $invoice);
    } else {
        Response::json(false, 400, "Invoice ID or order_id parameter required.");
    }
} catch (Exception $e) {
    Response::json(false, 404, "Invoice error: " . $e->getMessage());
}
