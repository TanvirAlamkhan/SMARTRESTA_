<?php
/**
 * SMARTRESTA Production Database Connection Manager
 * Railway-Ready PDO Singleton with Transaction & Exception Management
 */

require_once __DIR__ . '/env.php';

class Database {
    private static $conn = null;

    public static function getConnection() {
        if (self::$conn === null) {
            $host = getenv('DB_HOST') ?: getenv('MYSQLHOST') ?: 'localhost';
            $port = getenv('DB_PORT') ?: getenv('MYSQLPORT') ?: '3306';
            $dbname = getenv('DB_DATABASE') ?: getenv('MYSQLDATABASE') ?: 'smartresta_db';
            $user = getenv('DB_USERNAME') ?: getenv('MYSQLUSER') ?: 'root';
            $pass = getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : (getenv('MYSQLPASSWORD') !== false ? getenv('MYSQLPASSWORD') : '');

            try {
                self::$conn = new PDO(
                    "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4",
                    $user,
                    $pass,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                    ]
                );
            } catch (PDOException $e) {
                error_log("SMARTRESTA DB Connection Error: " . $e->getMessage());
                self::$conn = null;
            }
        }
        return self::$conn;
    }

    public static function beginTransaction() {
        $conn = self::getConnection();
        if ($conn && !$conn->inTransaction()) {
            return $conn->beginTransaction();
        }
        return false;
    }

    public static function commit() {
        $conn = self::getConnection();
        if ($conn && $conn->inTransaction()) {
            return $conn->commit();
        }
        return false;
    }

    public static function rollBack() {
        $conn = self::getConnection();
        if ($conn && $conn->inTransaction()) {
            return $conn->rollBack();
        }
        return false;
    }
}
