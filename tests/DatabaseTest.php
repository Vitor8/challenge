<?php

require_once __DIR__ . '/../core/DB.php';

/**
 * DatabaseTest - Ensures the database connection and required tables exist.
 */
class DatabaseTest {
    private PDO $pdo;
    private const REQUIRED_TABLES = ['migrations', 'usuarios', 'clients', 'addresses', 'client_address'];

    /**
     * Initializes the database test by establishing a connection and checking tables.
     */
    public function __construct() {
        $this->connectToDatabase();
        $this->checkRequiredTables();
    }

    /**
     * Establishes a connection to the database.
     *
     * @return void
     */
    private function connectToDatabase(): void {
        try {
            $this->pdo = DB::getConnection();
            echo "[✅] Successfully connected to the database.\n";
        } catch (PDOException $e) {
            echo "[❌] MySQL connection error: " . $e->getMessage() . "\n";
            exit(1);
        }
    }

    /**
     * Checks if all required tables exist in the database.
     *
     * @return void
     */
    private function checkRequiredTables(): void {
        foreach (self::REQUIRED_TABLES as $table) {
            $this->checkTableExists($table);
        }
    }

    /**
     * Verifies whether a specific table exists in the database.
     *
     * @param string $tableName The name of the table to check.
     * @return void
     */
    private function checkTableExists(string $tableName): void {
        try {
            $stmt = $this->pdo->prepare("SHOW TABLES LIKE :tableName");
            $stmt->execute(['tableName' => $tableName]);
            $tableExists = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($tableExists) {
                echo "[✅] Table '$tableName' found.\n";
            } else {
                echo "[❌] Table '$tableName' NOT found!\n";
                exit(1);
            }
        } catch (PDOException $e) {
            echo "[❌] Error checking table '$tableName': " . $e->getMessage() . "\n";
            exit(1);
        }
    }
}

// Run database tests
new DatabaseTest();
