<?php
/**
 * SMARTRESTA Production Logger Engine
 * Provides structured logging, correlation request IDs, secret redaction, and log rotation.
 */

class Logger {

    private static $requestId = null;
    private static $logDir = null;
    private static $maxLogSizeBytes = 5242880; // 5 MB

    public static function getRequestId(): string {
        if (self::$requestId === null) {
            self::$requestId = $_SERVER['HTTP_X_REQUEST_ID'] ?? bin2hex(random_bytes(8));
        }
        return self::$requestId;
    }

    private static function getLogDir(): string {
        if (self::$logDir === null) {
            self::$logDir = dirname(__DIR__) . '/storage/logs';
            if (!is_dir(self::$logDir)) {
                @mkdir(self::$logDir, 0755, true);
            }
        }
        return self::$logDir;
    }

    private static function redactSensitiveData(array $context): array {
        $sensitiveKeys = [
            'password', 'password_hash', 'password_confirm', 'token', 'secret',
            'db_password', 'card_number', 'cvv', 'authorization', 'api_key', 'bearer'
        ];

        foreach ($context as $key => &$value) {
            $keyLower = strtolower($key);
            if (in_array($keyLower, $sensitiveKeys, true)) {
                $value = '[REDACTED]';
            } elseif (is_array($value)) {
                $value = self::redactSensitiveData($value);
            }
        }
        return $context;
    }

    public static function log(string $level, string $message, array $context = []) {
        $level = strtoupper(trim($level));
        $timestamp = date('c');
        $reqId = self::getRequestId();
        $userId = $_SESSION['user_id'] ?? 'guest';
        $branchId = $_SESSION['branch_id'] ?? '1';

        $safeContext = self::redactSensitiveData($context);
        $contextJson = !empty($safeContext) ? ' ' . json_encode($safeContext, JSON_UNESCAPED_SLASHES) : '';

        $formatted = sprintf("[%s] [%s] [req:%s] [user:%s] [branch:%s] %s%s\n",
            $timestamp, $level, $reqId, $userId, $branchId, $message, $contextJson
        );

        $logDir = self::getLogDir();
        $targetFile = ($level === 'ERROR' || $level === 'CRITICAL') ? "{$logDir}/error.log" : "{$logDir}/app.log";

        // Log Rotation check (Rotate if > 5MB)
        if (file_exists($targetFile) && filesize($targetFile) > self::$maxLogSizeBytes) {
            @rename($targetFile, $targetFile . '.1');
        }

        @file_put_contents($targetFile, $formatted, FILE_APPEND | LOCK_EX);
    }

    public static function info(string $message, array $context = []) {
        self::log('INFO', $message, $context);
    }

    public static function warning(string $message, array $context = []) {
        self::log('WARNING', $message, $context);
    }

    public static function error(string $message, array $context = []) {
        self::log('ERROR', $message, $context);
    }
}
