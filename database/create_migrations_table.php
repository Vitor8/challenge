<?php

require_once __DIR__ . '/../core/DB.php';

class MigrationTableSetup {
    /**
     * Creates the `migrations` table if it does not exist.
     * This table is used to track executed migrations.
     *
     * @return void
     */
    public static function createTable(): void {
        try {
            $pdo = DB::getConnection();

            $sql = "CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration_name VARCHAR(255) NOT NULL,
                executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";

            $pdo->exec($sql);
            echo "✅ 'migrations' table has been created or already exists.\n";
        } catch (PDOException $e) {
            die("❌ Error creating 'migrations' table: " . $e->getMessage() . "\n");
        }
    }
}

// Execute the creation of the `migrations` table
MigrationTableSetup::createTable();
