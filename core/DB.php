<?php

require_once __DIR__ . '/../env.php';

class DB {
    private static ?PDO $pdo = null;

    /**
     * Private constructor to prevent direct instantiation.
     */
    private function __construct() {}

    /**
     * Returns an active database connection. If not established, initializes a new one.
     *
     * @return PDO The database connection instance.
     */
    public static function getConnection(): PDO {
        if (self::$pdo === null) {
            self::initializeConnection();
        }
        return self::$pdo;
    }

    /**
     * Establishes a database connection without selecting a specific database.
     * Useful for dynamically creating databases.
     *
     * @return PDO A new database connection instance without a selected database.
     */
    public static function getConnectionWithoutDB(): PDO {
        return new PDO(
            "mysql:host=" . getenv('DB_HOST') . ";charset=utf8",
            getenv('DB_USER'),
            getenv('DB_PASS'),
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }

    /**
     * Resets the database connection, forcing a new one to be established.
     *
     * @return void
     */
    public static function resetConnection(): void {
        self::$pdo = null;
        self::initializeConnection();
    }

    /**
     * Initializes the database connection using environment variables.
     *
     * @return void
     */
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
