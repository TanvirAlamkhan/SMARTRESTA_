<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/RoutingEngine.php';

$db = Database::getConnection();

// Get an active order item and station
$item = $db->query("SELECT oi.id, oi.order_id, o.branch_id FROM order_items oi JOIN orders o ON oi.order_id = o.id LIMIT 1")->fetch();
$station = $db->query("SELECT id FROM stations WHERE branch_id = {$item['branch_id']} LIMIT 1")->fetch();

if (!$item || !$station) {
    die("No test item/station found.\n");
}

echo "Testing Reroute for Item ID: {$item['id']} to Station ID: {$station['id']}...\n";

try {
    $res = RoutingEngine::rerouteItem((int)$item['id'], (int)$station['id'], 1, 'Testing manual reroute fix');
    echo "REROUTE SUCCESSFUL!\n";
    echo "Order ID: " . $res['order']['id'] . "\n";
    echo "Routes Count: " . count($res['routes']) . "\n";
} catch (Exception $e) {
    echo "REROUTE ERROR: " . $e->getMessage() . "\n";
}
