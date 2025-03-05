<?php

require_once __DIR__ . '/../core/DB.php';

class DatabaseChecker {
    /**
     * Checks the database connection and prints a confirmation message if successful.
     * If the connection fails, it terminates the script with an error message.
     *
     * @return void
     */
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

// Execute database connection check
DatabaseChecker::checkConnection();
