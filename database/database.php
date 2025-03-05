<?php

require_once __DIR__ . '/../core/DB.php';

class DatabaseChecker {
    public static function checkConnection(): void {
        try {
            $pdo = DB::getConnection();
            $dbName = getenv('DB_NAME');

            if (php_sapi_name() === "cli") {
                fwrite(STDOUT, "✅ Successfully connected to the database '$dbName'.\n");
            }
        } catch (PDOException $e) {
            die("❌ Database connection error: " . $e->getMessage() . "\n");
        }
    }
}

DatabaseChecker::checkConnection();
