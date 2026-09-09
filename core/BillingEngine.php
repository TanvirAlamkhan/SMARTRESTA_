<?php
/**
 * SMARTRESTA Core Billing Calculation Engine
 * Prompt 08: Payment & Billing Engine, Settlement, Receipts & Refunds
 *
 * Centralized server-authoritative financial calculation engine.
 * Calculates subtotals, discounts, tax, service charges, net paid amounts,
 * refunds, and outstanding balances with DECIMAL precision.
 */

require_once __DIR__ . '/Database.php';

class BillingEngine {

    /**
     * Calculate comprehensive financial bill for an order
     */
    public static function calculateOrderBill(int $orderId): array {
        $db = Database::getConnection();

        // 1. Fetch Order Details
        $ordStmt = $db->prepare("
            SELECT o.*, b.name AS branch_name, rt.table_number, u.name AS waiter_name, c.name AS customer_name
            FROM orders o
            LEFT JOIN branches b ON o.branch_id = b.id
            LEFT JOIN restaurant_tables rt ON o.table_id = rt.id
            LEFT JOIN users u ON o.taken_by_user_id = u.id
            LEFT JOIN customers c ON o.customer_id = c.id
            WHERE o.id = :oid
        ");
        $ordStmt->execute([':oid' => $orderId]);
        $order = $ordStmt->fetch();

        if (!$order) {
            throw new Exception("Order #{$orderId} not found");
        }

        // 2. Fetch Line Items
        $itemStmt = $db->prepare("
            SELECT oi.*
            FROM order_items oi
            WHERE oi.order_id = :oid AND oi.status != 'CANCELLED'
        ");
        $itemStmt->execute([':oid' => $orderId]);
        $items = $itemStmt->fetchAll();

        // 3. Financial Calculation
        $subtotal = 0.00;
        foreach ($items as $item) {
            $subtotal += (float)$item['subtotal'];
        }

        $discount = (float)($order['discount'] ?? 0.00);
        $taxableAmount = max(0.00, $subtotal - $discount);
        $taxRate = 0.05; // Standard 5% VAT
        $tax = round($taxableAmount * $taxRate, 2);
        $serviceCharge = (float)($order['service_charge'] ?? 0.00);
        $grandTotal = round($subtotal - $discount + $tax + $serviceCharge, 2);

        // 4. Fetch Payments & Allocations
        $payStmt = $db->prepare("
            SELECT p.*, pm.name AS payment_method_name, pm.code AS payment_method_code, pm.type AS payment_method_type
            FROM payments p
            JOIN payment_methods pm ON p.payment_method_id = pm.id
            WHERE p.order_id = :oid AND p.status = 'COMPLETED'
        ");
        $payStmt->execute([':oid' => $orderId]);
        $payments = $payStmt->fetchAll();

        $grossPaid = 0.00;
        foreach ($payments as $p) {
            $grossPaid += (float)$p['amount'];
        }

        // 5. Fetch Refunds
        $refStmt = $db->prepare("
            SELECT r.*
            FROM refunds r
            WHERE r.order_id = :oid AND r.status IN ('PROCESSED', 'APPROVED')
        ");
        $refStmt->execute([':oid' => $orderId]);
        $refunds = $refStmt->fetchAll();

        $totalRefunded = 0.00;
        foreach ($refunds as $r) {
            $totalRefunded += (float)$r['refund_amount'];
        }

        $netPaid = max(0.00, $grossPaid - $totalRefunded);
        $outstandingBalance = max(0.00, round($grandTotal - $netPaid, 2));

        // 6. Payment Status Derivation
        $calculatedPaymentStatus = 'UNPAID';
        if ($netPaid >= ($grandTotal - 0.01) && $grandTotal > 0) {
            $calculatedPaymentStatus = 'PAID';
        } else if ($netPaid > 0.00) {
            $calculatedPaymentStatus = 'PARTIAL';
        }

        if ($totalRefunded > 0.00 && $netPaid <= 0.00) {
            $calculatedPaymentStatus = 'REFUNDED';
        }

        return [
            'order_id' => $orderId,
            'order_number' => $order['order_number'],
            'branch_id' => (int)$order['branch_id'],
            'branch_name' => $order['branch_name'] ?? 'Main Outlet',
            'order_type' => $order['order_type'],
            'table_number' => $order['table_number'] ?? 'N/A',
            'waiter_name' => $order['waiter_name'] ?? 'Staff',
            'customer_name' => $order['customer_name'] ?? 'Guest',
            'order_status' => $order['order_status'],
            'payment_status' => $calculatedPaymentStatus,
            'subtotal' => round($subtotal, 2),
            'discount' => round($discount, 2),
            'taxable_amount' => round($taxableAmount, 2),
            'tax_rate_percent' => 5.00,
            'tax' => round($tax, 2),
            'service_charge' => round($serviceCharge, 2),
            'grand_total' => round($grandTotal, 2),
            'gross_paid' => round($grossPaid, 2),
            'total_refunded' => round($totalRefunded, 2),
            'net_paid' => round($netPaid, 2),
            'outstanding_balance' => round($outstandingBalance, 2),
            'items' => $items,
            'payments' => $payments,
            'refunds' => $refunds
        ];
    }

    /**
     * Calculate financial summary for an active dining session
     */
    public static function calculateSessionBill(int $sessionId): array {
        $db = Database::getConnection();

        $stmt = $db->prepare("SELECT id FROM orders WHERE dining_session_id = :sid AND order_status != 'CANCELLED'");
        $stmt->execute([':sid' => $sessionId]);
        $orders = $stmt->fetchAll();

        $subtotal = 0.00;
        $discount = 0.00;
        $tax = 0.00;
        $serviceCharge = 0.00;
        $grandTotal = 0.00;
        $netPaid = 0.00;
        $outstanding = 0.00;
        $orderBills = [];

        foreach ($orders as $ord) {
            $bill = self::calculateOrderBill((int)$ord['id']);
            $orderBills[] = $bill;

            $subtotal += $bill['subtotal'];
            $discount += $bill['discount'];
            $tax += $bill['tax'];
            $serviceCharge += $bill['service_charge'];
            $grandTotal += $bill['grand_total'];
            $netPaid += $bill['net_paid'];
            $outstanding += $bill['outstanding_balance'];
        }

        return [
            'dining_session_id' => $sessionId,
            'order_count' => count($orders),
            'subtotal' => round($subtotal, 2),
            'discount' => round($discount, 2),
            'tax' => round($tax, 2),
            'service_charge' => round($serviceCharge, 2),
            'grand_total' => round($grandTotal, 2),
            'net_paid' => round($netPaid, 2),
            'outstanding_balance' => round($outstanding, 2),
            'orders' => $orderBills
        ];
    }
}
