<?php

require_once __DIR__ . '/../env.php';

class DB {
    private static ?PDO $pdo = null;

    private function __construct() {}

    public static function getConnection(): PDO {
        if (self::$pdo === null) {
            self::initializeConnection();
        }
        return self::$pdo;
    }

    public static function getConnectionWithoutDB(): PDO {
        return new PDO(
            "mysql:host=" . getenv('DB_HOST') . ";charset=utf8",
            getenv('DB_USER'),
            getenv('DB_PASS'),
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }

    public static function resetConnection(): void {
        self::$pdo = null;
        self::initializeConnection();
    }

    private static function initializeConnection(): void {
        $host = getenv('DB_HOST');
        $dbname = getenv('DB_NAME');
        $user = getenv('DB_USER');
        $pass = getenv('DB_PASS');

        try {
            self::$pdo = new PDO(
                "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
                $user,
                $pass,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        } catch (PDOException $e) {
            die("❌ Database connection error: " . $e->getMessage() . "\n");
        }
    }
}
