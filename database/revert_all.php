<?php

require_once __DIR__ . '/../core/DB.php';

class MigrationReverter {
    private PDO $pdo;

    /**
     * Initializes the database connection for reverting migrations.
     */
    public function __construct() {
        $this->pdo = DB::getConnection();
    }

    /**
     * Reverts all executed migrations in reverse order.
     * Retrieves the list of executed migrations and rolls them back using the `down()` method.
     *
     * @return void
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
     * Retrieves the list of executed migrations from the database, ordered from latest to earliest.
     *
     * @return array The names of executed migrations.
     */
    private function getExecutedMigrations(): array {
        $stmt = $this->pdo->query("SELECT migration_name FROM migrations ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    }

    /**
     * Reverts a specific migration by calling its `down()` method.
     *
     * @param string $migrationFile The file path of the migration.
     * @param string $migrationName The name of the migration.
     * @return void
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
     * Removes the migration record from the database after it has been reverted.
     *
     * @param string $migrationName The name of the reverted migration.
     * @return void
     */
    private function removeMigrationRecord(string $migrationName): void {
        $stmt = $this->pdo->prepare("DELETE FROM migrations WHERE migration_name = :migration_name");
        $stmt->execute(['migration_name' => $migrationName]);
    }

    /**
     * Extracts the class name from a migration file by analyzing its content.
     *
     * @param string $filePath The path to the migration file.
     * @return string|null The class name if found, otherwise null.
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
