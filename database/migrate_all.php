<?php

require_once __DIR__ . '/../core/DB.php';

class MigrationRunner {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = DB::getConnection();
    }

    /**
     * Runs all pending migrations
     */
    public function runMigrations(): void {
        try {
            $executedMigrations = $this->getExecutedMigrations();
            $migrationFiles = glob(__DIR__ . '/migrations/*.php');

            foreach ($migrationFiles as $migrationFile) {
                $migrationName = basename($migrationFile, '.php');

                if (!in_array($migrationName, $executedMigrations)) {
                    $this->executeMigration($migrationFile, $migrationName);
                }
            }
        } catch (PDOException $e) {
            die("❌ Error executing migrations: " . $e->getMessage() . "\n");
        }
    }

    /**
     * Retrieves the list of already executed migrations
     */
    private function getExecutedMigrations(): array {
        $stmt = $this->pdo->query("SELECT migration_name FROM migrations");
        return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    }

    /**
     * Executes a specific migration
     */
    private function executeMigration(string $migrationFile, string $migrationName): void {
        require_once $migrationFile;
        $className = $this->getMigrationClassName($migrationFile);

        if ($className && class_exists($className)) {
            $migrationInstance = new $className();
            $migrationInstance->up();
            $this->registerMigration($migrationName);
            echo "✅ Migration '$migrationName' executed successfully.\n";
        } else {
            echo "⚠️ Migration class not found in '$migrationFile'.\n";
        }
    }

    /**
     * Registers the migration as executed in the database
     */
    private function registerMigration(string $migrationName): void {
        $stmt = $this->pdo->prepare("INSERT INTO migrations (migration_name) VALUES (:migration_name)");
        $stmt->execute(['migration_name' => $migrationName]);
    }

    /**
     * Extracts the class name from a migration file by analyzing its content
     */
    private function getMigrationClassName(string $filePath): ?string {
        $content = file_get_contents($filePath);
        if (preg_match('/class\s+([a-zA-Z0-9_]+)\s+/', $content, $matches)) {
            return $matches[1];
        }
        return null;
    }
}

// Execute all migrations
$migrationRunner = new MigrationRunner();
$migrationRunner->runMigrations();
