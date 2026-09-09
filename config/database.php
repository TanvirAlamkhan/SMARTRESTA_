<?php
/**
 * SMARTRESTA Production Database Connection Manager
 * Railway-Ready PDO Singleton with Multi-URL & Variable Auto-Detection
 */

require_once __DIR__ . '/env.php';

class Database {
    private static $conn = null;

    public static function getConnection() {
        if (self::$conn === null) {
            $host = 'localhost';
            $port = '3306';
            $dbname = 'smartresta_db';
            $user = 'root';
            $pass = '';

            // Check if Railway provides a connection URL (MYSQLURL / MYSQL_URL / DATABASE_URL)
            $dbUrl = getenv('MYSQLURL') ?: getenv('MYSQL_URL') ?: getenv('DATABASE_URL');
            if (!empty($dbUrl)) {
                $parsed = parse_url($dbUrl);
                if ($parsed && isset($parsed['host'])) {
                    $host = $parsed['host'];
                    $port = $parsed['port'] ?? '3306';
                    $user = $parsed['user'] ?? 'root';
                    $pass = $parsed['pass'] ?? '';
                    $dbname = ltrim($parsed['path'] ?? 'smartresta_db', '/');
                }
            } else {
                $host = getenv('DB_HOST') ?: getenv('MYSQLHOST') ?: getenv('MYSQL_HOST') ?: 'localhost';
                $port = getenv('DB_PORT') ?: getenv('MYSQLPORT') ?: getenv('MYSQL_PORT') ?: '3306';
                $dbname = getenv('DB_DATABASE') ?: getenv('MYSQLDATABASE') ?: getenv('MYSQL_DATABASE') ?: 'smartresta_db';
                $user = getenv('DB_USERNAME') ?: getenv('MYSQLUSER') ?: getenv('MYSQL_USER') ?: 'root';
                $pass = getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : (getenv('MYSQLPASSWORD') !== false ? getenv('MYSQLPASSWORD') : (getenv('MYSQL_PASSWORD') !== false ? getenv('MYSQL_PASSWORD') : ''));
            }

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
