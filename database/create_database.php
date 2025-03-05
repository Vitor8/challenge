<?php

require_once __DIR__ . '/../core/DB.php';

class DatabaseSetup {
    /**
     * Creates the database if it does not exist.
     * Uses a connection without selecting a specific database.
     *
     * @return void
     */
    public static function createDatabase(): void {
        $pdo = DB::getConnectionWithoutDB();
        $dbName = getenv('DB_NAME');

        try {
            // Create the database with UTF-8 support if it doesn't exist
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
            echo "✅ Database '$dbName' has been created or already exists.\n";

            // Reset connection to ensure it connects to the newly created database
            DB::resetConnection();
        } catch (PDOException $e) {
            die("❌ Error creating the database: " . $e->getMessage() . "\n");
        }
    }
}

// Execute database creation
DatabaseSetup::createDatabase();
