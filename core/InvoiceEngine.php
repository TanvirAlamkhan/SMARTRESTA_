<?php
/**
 * SMARTRESTA Core Invoice Engine
 * Prompt 08: Payment & Billing Engine, Settlement, Receipts & Refunds
 *
 * Generates server-authoritative customer invoices with historical snapshots.
 */

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/BillingEngine.php';

class InvoiceEngine {

    /**
     * Generate or update invoice for an order
     */
    public static function generateInvoice(int $orderId): array {
        $db = Database::getConnection();

        $bill = BillingEngine::calculateOrderBill($orderId);

        // Check if invoice already exists
        $stmtExist = $db->prepare("SELECT * FROM invoices WHERE order_id = :oid");
        $stmtExist->execute([':oid' => $orderId]);
        $existing = $stmtExist->fetch();

        $invoiceNumber = $existing ? $existing['invoice_number'] : ('INV-' . date('Y') . '-' . sprintf('%06d', rand(100000, 999999)));

        $snapshotData = [
            'invoice_number' => $invoiceNumber,
            'order_number' => $bill['order_number'],
            'branch_name' => $bill['branch_name'],
            'table_number' => $bill['table_number'],
            'customer_name' => $bill['customer_name'],
            'waiter_name' => $bill['waiter_name'],
            'order_type' => $bill['order_type'],
            'order_status' => $bill['order_status'],
            'payment_status' => $bill['payment_status'],
            'items' => array_map(fn($i) => [
                'name' => $i['item_name'],
                'quantity' => $i['quantity'],
                'unit_price' => $i['unit_price'],
                'subtotal' => $i['subtotal']
            ], $bill['items']),
            'financials' => [
                'subtotal' => $bill['subtotal'],
                'discount' => $bill['discount'],
                'tax' => $bill['tax'],
                'service_charge' => $bill['service_charge'],
                'grand_total' => $bill['grand_total'],
                'gross_paid' => $bill['gross_paid'],
                'total_refunded' => $bill['total_refunded'],
                'net_paid' => $bill['net_paid'],
                'outstanding_balance' => $bill['outstanding_balance']
            ],
            'timestamp' => date('Y-m-d H:i:s')
        ];

        if ($existing) {
            $upStmt = $db->prepare("
                UPDATE invoices 
                SET subtotal = :sub, discount = :disc, tax = :tax, service_charge = :sc, total = :tot,
                    paid_amount = :paid, outstanding_amount = :out, status = :st, invoice_data = :snap
                WHERE id = :inv_id
            ");
            $upStmt->execute([
                ':sub' => $bill['subtotal'],
                ':disc' => $bill['discount'],
                ':tax' => $bill['tax'],
                ':sc' => $bill['service_charge'],
                ':tot' => $bill['grand_total'],
                ':paid' => $bill['net_paid'],
                ':out' => $bill['outstanding_balance'],
                ':st' => $bill['payment_status'] === 'PAID' ? 'PAID' : ($bill['payment_status'] === 'PARTIAL' ? 'PARTIAL' : 'UNPAID'),
                ':snap' => json_encode($snapshotData),
                ':inv_id' => $existing['id']
            ]);
            $invoiceId = (int)$existing['id'];
        } else {
            $insStmt = $db->prepare("
                INSERT INTO invoices 
                (invoice_number, branch_id, order_id, dining_session_id, customer_id, subtotal, discount, tax, service_charge, total, paid_amount, outstanding_amount, status, invoice_data, created_at)
                VALUES (:inum, :bid, :oid, :sid, :cid, :sub, :disc, :tax, :sc, :tot, :paid, :out, :st, :snap, NOW())
            ");
            $insStmt->execute([
                ':inum' => $invoiceNumber,
                ':bid' => $bill['branch_id'],
                ':oid' => $orderId,
                ':sid' => null,
                ':cid' => null,
                ':sub' => $bill['subtotal'],
                ':disc' => $bill['discount'],
                ':tax' => $bill['tax'],
                ':sc' => $bill['service_charge'],
                ':tot' => $bill['grand_total'],
                ':paid' => $bill['net_paid'],
                ':out' => $bill['outstanding_balance'],
                ':st' => $bill['payment_status'] === 'PAID' ? 'PAID' : ($bill['payment_status'] === 'PARTIAL' ? 'PARTIAL' : 'UNPAID'),
                ':snap' => json_encode($snapshotData)
            ]);
            $invoiceId = (int)$db->lastInsertId();
        }

        return [
            'id' => $invoiceId,
            'invoice_number' => $invoiceNumber,
            'order_id' => $orderId,
            'status' => $snapshotData['payment_status'],
            'total' => $snapshotData['financials']['grand_total'],
            'invoice_data' => $snapshotData
        ];
    }

    public static function generateInvoiceForOrder(int $orderId, int $userId = 1): array {
        return self::generateInvoice($orderId);
    }

    /**
     * Get persisted invoice by ID
     */
    public static function getInvoice(int $invoiceId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM invoices WHERE id = :inv_id");
        $stmt->execute([':inv_id' => $invoiceId]);
        $invoice = $stmt->fetch();

        if (!$invoice) {
            throw new Exception("Invoice #{$invoiceId} not found");
        }

        $invoice['invoice_data'] = json_decode($invoice['invoice_data'], true);
        return $invoice;
    }
}
