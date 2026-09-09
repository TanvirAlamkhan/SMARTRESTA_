<?php
/**
 * SMARTRESTA Database Connection Handler
 * PHP 8.x PDO Driver with MySQL 8.x
 * Supports local & Railway MySQL environment variables.
 */

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
            } catch (PDOException $exception) {
                error_log("SMARTRESTA API DB Connection Error: " . $exception->getMessage());
                self::$conn = null;
            }
        }
        return self::$conn;
    }
}
