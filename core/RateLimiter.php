<?php
/**
 * SMARTRESTA Login Attempt Rate Limiter Engine
 * Prevents brute-force attacks (Max 5 failed attempts per 15 minutes)
 */

require_once __DIR__ . '/Database.php';

class RateLimiter {
    private static $maxAttempts = 5;
    private static $decayMinutes = 15;

    public static function check($identifier) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $timeThreshold = date('Y-m-d H:i:s', strtotime('-' . self::$decayMinutes . ' minutes'));

        try {
            $row = DB::fetch("
                SELECT COUNT(*) as count 
                FROM login_attempts 
                WHERE (ip_address = ? OR identifier = ?) AND attempted_at >= ?
            ", [$ip, $identifier, $timeThreshold]);

            $attempts = (int)($row['count'] ?? 0);
            return $attempts < self::$maxAttempts;
        } catch (Exception $e) {
            // Fallback allow if table missing
            return true;
        }
    }

    public static function recordFailedAttempt($identifier) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        try {
            DB::insert("
                INSERT INTO login_attempts (identifier, ip_address) 
                VALUES (?, ?)
            ", [$identifier, $ip]);
        } catch (Exception $e) {
            error_log("RateLimiter Recording Error: " . $e->getMessage());
        }
    }

    public static function clear($identifier) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        try {
            DB::execute("DELETE FROM login_attempts WHERE ip_address = ? OR identifier = ?", [$ip, $identifier]);
        } catch (Exception $e) {
            error_log("RateLimiter Clear Error: " . $e->getMessage());
        }
    }
}
