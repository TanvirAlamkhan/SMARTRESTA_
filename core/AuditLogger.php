<?php
/**
 * SMARTRESTA Operational Audit Logging Engine
 * Records historical audit logs for compliance, financial tracking, and operational security.
 */

require_once __DIR__ . '/Database.php';

class AuditLogger {
    public static function log($arg1, $arg2, $arg3 = null, $arg4 = null, $arg5 = null, $arg6 = null) {
        try {
            if (is_numeric($arg1)) {
                // Signature A: log($userId, $action, $module, $recordId, $oldValue, $newValue)
                $userId = (int)$arg1;
                $action = (string)$arg2;
                $module = (string)$arg3;
                $recordId = $arg4 ? (int)$arg4 : null;
                $oldValue = $arg5;
                $newValue = $arg6;
            } else {
                // Signature B: log($action, $module, $recordId, $oldValue, $newValue, $userId)
                $action = (string)$arg1;
                $module = (string)$arg2;
                $recordId = $arg3 ? (int)$arg3 : null;
                $oldValue = $arg4;
                $newValue = $arg5;
                $userId = $arg6 ? (int)$arg6 : null;
            }

            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $userAgent = substr($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown', 0, 255);
            $userId = $userId ?: ($_SESSION['user_id'] ?? null);

            $oldJson = ($oldValue !== null) ? (is_string($oldValue) && json_decode($oldValue) !== null ? $oldValue : json_encode($oldValue)) : null;
            $newJson = ($newValue !== null) ? (is_string($newValue) && json_decode($newValue) !== null ? $newValue : json_encode($newValue)) : null;

            DB::insert("
                INSERT INTO audit_logs (user_id, action, module, record_id, old_value, new_value, ip_address, user_agent)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ", [
                $userId,
                $action,
                $module,
                $recordId,
                $oldJson,
                $newJson,
                $ipAddress,
                $userAgent
            ]);
            return true;
        } catch (Exception $e) {
            error_log("AuditLogger Error: " . $e->getMessage());
            return false;
        }
    }
}
