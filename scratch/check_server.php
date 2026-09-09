<?php
$ch = curl_init('http://localhost:8089/public/health.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 3);
$res = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
if ($code == 200) {
    echo "Server is RUNNING on http://localhost:8089! (Status: 200)\n";
} else {
    echo "Server not responding on 8089 (HTTP Code: $code, Error: " . curl_error($ch) . ")\n";
}
