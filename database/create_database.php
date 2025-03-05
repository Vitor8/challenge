<?php

require_once __DIR__ . '/../core/DB.php';

class DatabaseSetup {
    public static function createDatabase(): void {
        $pdo = DB::getConnectionWithoutDB();
        $dbName = getenv('DB_NAME');

        try {
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
            echo "✅ Database '$dbName' has been created or already exists.\n";

            DB::resetConnection();
        } catch (PDOException $e) {
            die("❌ Error creating the database: " . $e->getMessage() . "\n");
        }
    }
}

// Execute database creation
DatabaseSetup::createDatabase();
