<?php
$pass = 'amer25';
$hash = '$2y$12$SM5oqtBP5RkOWru5STO6QuTWX83yrxaCE.e20b0TgIwelj.vVM1KK';
echo "Verify result for amer25: " . (password_verify($pass, $hash) ? 'TRUE' : 'FALSE') . "\n";
