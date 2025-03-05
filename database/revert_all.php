<?php

require_once __DIR__ . '/../core/DB.php';

class MigrationReverter {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = DB::getConnection();
    }

    /**
     * Reverts all executed migrations in reverse order
     */
    public function revertMigrations(): void {
        try {
            $executedMigrations = $this->getExecutedMigrations();

            foreach (array_reverse($executedMigrations) as $migrationName) {
                $migrationFile = __DIR__ . "/migrations/{$migrationName}.php";

                if (file_exists($migrationFile)) {
                    $this->revertMigration($migrationFile, $migrationName);
                } else {
                    echo "⚠️ Migration file '$migrationName.php' not found.\n";
                }
            }
        } catch (PDOException $e) {
            die("❌ Error reverting migrations: " . $e->getMessage() . "\n");
        }
    }

    /**
     * Retrieves the list of executed migrations in order
     */
    private function getExecutedMigrations(): array {
        $stmt = $this->pdo->query("SELECT migration_name FROM migrations ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    }

    /**
     * Reverts a specific migration
     */
    private function revertMigration(string $migrationFile, string $migrationName): void {
        require_once $migrationFile;
        $className = $this->getMigrationClassName($migrationFile);

        if ($className && class_exists($className)) {
            $migrationInstance = new $className();
            $migrationInstance->down();
            $this->removeMigrationRecord($migrationName);
            echo "✅ Migration '$migrationName' reverted successfully.\n";
        } else {
            echo "⚠️ Migration class not found in '$migrationFile'.\n";
        }
    }

    /**
     * Removes the migration record from the database
     */
    private function removeMigrationRecord(string $migrationName): void {
        $stmt = $this->pdo->prepare("DELETE FROM migrations WHERE migration_name = :migration_name");
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

// Execute migration rollback
$migrationReverter = new MigrationReverter();
$migrationReverter->revertMigrations();
