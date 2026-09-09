<?php
/**
 * SMARTRESTA Production Database Connection Manager
 * Railway-Ready PDO Singleton with Multi-URL & Priority Environment Auto-Detection
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

            // Priority 1: Check Railway MySQL URL (MYSQLURL / MYSQL_URL / DATABASE_URL)
            $dbUrl = getenv('MYSQLURL') ?: getenv('MYSQL_URL') ?: getenv('DATABASE_URL');
            
            // Priority 2: Check Railway explicit MySQL environment variables (MYSQLHOST / MYSQL_HOST)
            $railwayHost = getenv('MYSQLHOST') ?: getenv('MYSQL_HOST');
            $railwayPort = getenv('MYSQLPORT') ?: getenv('MYSQL_PORT');
            $railwayDb   = getenv('MYSQLDATABASE') ?: getenv('MYSQL_DATABASE');
            $railwayUser = getenv('MYSQLUSER') ?: getenv('MYSQL_USER');
            $railwayPass = getenv('MYSQLPASSWORD') !== false ? getenv('MYSQLPASSWORD') : (getenv('MYSQL_PASSWORD') !== false ? getenv('MYSQL_PASSWORD') : null);

            if (!empty($dbUrl)) {
                $parsed = parse_url($dbUrl);
                if ($parsed && isset($parsed['host'])) {
                    $host = $parsed['host'];
                    $port = $parsed['port'] ?? '3306';
                    $user = $parsed['user'] ?? 'root';
                    $pass = $parsed['pass'] ?? '';
                    $dbname = ltrim($parsed['path'] ?? 'smartresta_db', '/');
                }
            } elseif (!empty($railwayHost)) {
                $host = $railwayHost;
                $port = $railwayPort ?: '3306';
                $dbname = $railwayDb ?: 'smartresta_db';
                $user = $railwayUser ?: 'root';
                $pass = $railwayPass !== null ? $railwayPass : '';
            } else {
                // Priority 3: Fallback to local DB_HOST / DB_USERNAME in .env
                $host = getenv('DB_HOST') ?: 'localhost';
                $port = getenv('DB_PORT') ?: '3306';
                $dbname = getenv('DB_DATABASE') ?: 'smartresta_db';
                $user = getenv('DB_USERNAME') ?: 'root';
                $pass = getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : '';
            }

            try {
                $pdoOptions = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ];

                // Safe initialization of MySQL charset attribute constant
                if (defined('PDO::MYSQL_ATTR_INIT_COMMAND')) {
                    $pdoOptions[PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES utf8mb4";
                } else {
                    $pdoOptions[1002] = "SET NAMES utf8mb4";
                }

                self::$conn = new PDO(
                    "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4",
                    $user,
                    $pass,
                    $pdoOptions
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
