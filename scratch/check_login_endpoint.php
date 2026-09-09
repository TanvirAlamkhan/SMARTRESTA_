<?php
$url = 'http://localhost:8089/api/v1/auth/login.php';
$data = json_encode(['email' => 'admin@smartresta.com', 'password' => 'Admin@SMARTRESTA2026!']);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'X-Requested-With: XMLHttpRequest']);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

echo "HTTP CODE: $httpCode\n";
echo "CONTENT TYPE: $contentType\n";
echo "RAW RESPONSE BODY:\n$response\n";
