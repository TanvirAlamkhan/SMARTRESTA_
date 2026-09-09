<?php
/**
 * SMARTRESTA Environment Variable Loader
 * Parses .env file into getenv() and $_ENV
 */

function loadEnv($envPath = __DIR__ . '/../.env') {
    if (!file_exists($envPath)) {
        return false;
    }

    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || strpos($line, '#') === 0) {
            continue;
        }

        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value, " \t\n\r\0\x0B\"'");

            if (!getenv($name)) {
                putenv("{$name}={$value}");
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }
    return true;
}

// Auto-load .env on include
loadEnv();
