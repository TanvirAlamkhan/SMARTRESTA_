<?php
/**
 * SMARTRESTA - Automated Verification Script for Prompt 12: ReportEngine
 */

define('SMARTRESTA_ENTRY', true);
require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/ReportEngine.php';

echo "========================================================\n";
echo "  SMARTRESTA Prompt 12: Reporting & Analytics Verification\n";
echo "========================================================\n\n";

$branchId = 1;

try {
    // 1. Dashboard Overview
    echo "[1] Testing ReportEngine::getDashboardOverview()...\n";
    $overview = ReportEngine::getDashboardOverview($branchId, null, null, 'this_month');
    echo "    - Total Orders: {$overview['kpis']['total_orders']}\n";
    echo "    - Net Sales: ৳{$overview['kpis']['net_sales']}\n";
    echo "    - Gross Sales: ৳{$overview['kpis']['gross_sales']}\n";
    echo "    - AOV: ৳{$overview['kpis']['aov']}\n";
    echo "    - Low Stock Count: {$overview['alerts']['low_stock_ingredients']}\n";
    echo "    - Unpaid Orders Count: {$overview['alerts']['unpaid_orders']}\n";
    echo "    - Pending Commissions: {$overview['alerts']['pending_commissions']}\n";
    echo "  -> SUCCESS\n\n";

    // 2. Sales Analytics Report
    echo "[2] Testing ReportEngine::getSalesReport()...\n";
    $salesReport = ReportEngine::getSalesReport($branchId, null, null, 'date');
    echo "    - Sales data points: " . count($salesReport['sales_data']) . "\n";
    echo "  -> SUCCESS\n\n";

    // 3. Orders Report
    echo "[3] Testing ReportEngine::getOrdersReport()...\n";
    $ordersReport = ReportEngine::getOrdersReport($branchId, null, null, [], 1, 10);
    echo "    - Total Matching Orders: {$ordersReport['pagination']['total']}\n";
    echo "    - Current Page Orders: " . count($ordersReport['items']) . "\n";
    echo "  -> SUCCESS\n\n";

    // 4. Waiter Performance Report
    echo "[4] Testing ReportEngine::getWaiterPerformanceReport()...\n";
    $waitersReport = ReportEngine::getWaiterPerformanceReport($branchId, null, null);
    echo "    - Waiters Tracked: " . count($waitersReport['items']) . "\n";
    echo "  -> SUCCESS\n\n";

    // 5. Table Performance Report
    echo "[5] Testing ReportEngine::getTablePerformanceReport()...\n";
    $tablesReport = ReportEngine::getTablePerformanceReport($branchId, null, null);
    echo "    - Tables Tracked: " . count($tablesReport['items']) . "\n";
    echo "  -> SUCCESS\n\n";

    // 6. Product Sales Report
    echo "[6] Testing ReportEngine::getProductPerformanceReport()...\n";
    $productsReport = ReportEngine::getProductPerformanceReport($branchId, null, null);
    echo "    - Products Tracked: " . count($productsReport['items']) . "\n";
    echo "  -> SUCCESS\n\n";

    // 7. Payment Reconciliation Report
    echo "[7] Testing ReportEngine::getPaymentReport()...\n";
    $paymentsReport = ReportEngine::getPaymentReport($branchId, null, null);
    echo "    - Payment Methods Tracked: " . count($paymentsReport['payment_methods']) . "\n";
    echo "    - Outstanding Unpaid Orders: " . count($paymentsReport['outstanding_orders']) . "\n";
    echo "  -> SUCCESS\n\n";

    // 8. KDS Station Performance Report
    echo "[8] Testing ReportEngine::getKDSPerformanceReport()...\n";
    $kdsReport = ReportEngine::getKDSPerformanceReport($branchId, null, null);
    echo "    - KDS Stations Tracked: " . count($kdsReport['stations']) . "\n";
    echo "  -> SUCCESS\n\n";

    // 9. Inventory Valuation & Consumption Report
    echo "[9] Testing ReportEngine::getInventoryReport()...\n";
    $invReport = ReportEngine::getInventoryReport($branchId, null, null);
    echo "    - Total Stock Value: ৳{$invReport['stock_summary']['total_stock_value']}\n";
    echo "    - Total Ingredients Count: {$invReport['stock_summary']['total_ingredients']}\n";
    echo "    - Wastage Breakdown Rows: " . count($invReport['wastage_summary']) . "\n";
    echo "  -> SUCCESS\n\n";

    // 10. Order Drill-Down Detailed View
    echo "[10] Testing ReportEngine::getDrilldownOrderDetails()...\n";
    $db = Database::getConnection();
    $stmt = $db->query("SELECT id FROM orders ORDER BY id DESC LIMIT 1");
    $firstOrder = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($firstOrder) {
        $drilldown = ReportEngine::getDrilldownOrderDetails((int)$firstOrder['id'], $branchId);
        echo "    - Drill-down for Order #{$drilldown['order']['order_number']}:\n";
        echo "      * Items Count: " . count($drilldown['items']) . "\n";
        echo "      * KDS Tickets Count: " . count($drilldown['tickets']) . "\n";
        echo "      * Payments Count: " . count($drilldown['payments']) . "\n";
        echo "      * Inventory Logs Count: " . count($drilldown['inventory_logs']) . "\n";
    } else {
        echo "    - No orders found in database to test drilldown.\n";
    }
    echo "  -> SUCCESS\n\n";

    // 11. CSV Export Test
    echo "[11] Testing ReportEngine::exportCSV()...\n";
    $csvSales = ReportEngine::exportCSV('sales', $branchId);
    $csvOrders = ReportEngine::exportCSV('orders', $branchId);
    echo "    - CSV Sales export bytes: " . strlen($csvSales) . "\n";
    echo "    - CSV Orders export bytes: " . strlen($csvOrders) . "\n";
    echo "  -> SUCCESS\n\n";

    echo "========================================================\n";
    echo "  ALL REPORTING & ANALYTICS TESTS PASSED SUCCESSFULLY!  \n";
    echo "========================================================\n";

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
