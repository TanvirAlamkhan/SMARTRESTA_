<?php
$endpoints = [
    'http://localhost:8089/public/health.php',
    'http://localhost:8089/api/v1/health.php?type=readiness',
    'http://localhost:8089/api/v1/dashboard/overview.php?preset=today',
    'http://localhost:8089/api/v1/categories/index.php',
    'http://localhost:8089/api/v1/orders/index.php'
];

foreach ($endpoints as $url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 3);
    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    echo "Endpoint: $url\nStatus: $code | Content-Type: $type\n";
}
