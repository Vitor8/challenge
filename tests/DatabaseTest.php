<?php

require_once __DIR__ . '/../core/DB.php';

class DatabaseTest {
    private PDO $pdo;
    private const REQUIRED_TABLES = ['migrations', 'usuarios', 'clients', 'addresses', 'client_address'];

    public function __construct() {
        $this->connectToDatabase();
        $this->checkRequiredTables();
    }

    /**
     * Establishes a connection to the database.
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
     */
    private function checkRequiredTables(): void {
        foreach (self::REQUIRED_TABLES as $table) {
            $this->checkTableExists($table);
        }
    }

    /**
     * Verifies whether a specific table exists in the database.
     */
    private function checkTableExists(string $tableName): void {
        $stmt = $this->pdo->prepare("SHOW TABLES LIKE :tableName");
        $stmt->execute(['tableName' => $tableName]);
        $tableExists = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($tableExists) {
            echo "[✅] Table '$tableName' found.\n";
        } else {
            echo "[❌] Table '$tableName' NOT found!\n";
            exit(1);
        }
    }
}

// Run database tests
new DatabaseTest();
