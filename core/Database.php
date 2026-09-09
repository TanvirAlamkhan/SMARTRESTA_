<?php
/**
 * SMARTRESTA Core Prepared Statement Query Abstraction Layer
 * Enforces prepared statements and parameter binding for security.
 */

require_once __DIR__ . '/../config/database.php';

class DB {
    public static function query($sql, $params = []) {
        $pdo = Database::getConnection();
        if (!$pdo) {
            throw new Exception("Database connection unavailable.");
        }
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function fetch($sql, $params = []) {
        return self::query($sql, $params)->fetch();
    }

    public static function fetchAll($sql, $params = []) {
        return self::query($sql, $params)->fetchAll();
    }

    public static function insert($sql, $params = []) {
        self::query($sql, $params);
        return Database::getConnection()->lastInsertId();
    }

    public static function execute($sql, $params = []) {
        return self::query($sql, $params)->rowCount();
    }
}
