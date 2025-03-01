<?php
require_once __DIR__ . '/../core/DB.php';

try {
    $pdo = DB::getConnection();

    $stmt = $pdo->query("SELECT migration_name FROM migrations");
    $executedMigrations = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $migrationFiles = glob(__DIR__ . '/migrations/*.php');

    foreach ($migrationFiles as $migrationFile) {
        $migrationName = basename($migrationFile, '.php');

        if (!in_array($migrationName, $executedMigrations)) {
            require_once $migrationFile;

            $className = getMigrationClassName($migrationFile);

            if ($className && class_exists($className)) {
                $migrationInstance = new $className();
                $migrationInstance->up();

                $stmt = $pdo->prepare("INSERT INTO migrations (migration_name) VALUES (:migration_name)");
                $stmt->execute(['migration_name' => $migrationName]);

                echo "✅ Migration '$migrationName' executada com sucesso!\n";
            } else {
                echo "⚠️ Classe da migration não encontrada em '$migrationFile'.\n";
            }
        }
    }
} catch (PDOException $e) {
    die("❌ Erro ao executar as migrations: " . $e->getMessage() . "\n");
}

function getMigrationClassName($filePath) {
    $content = file_get_contents($filePath);
    if (preg_match('/class\s+([a-zA-Z0-9_]+)\s+/', $content, $matches)) {
        return $matches[1];
    }
    return null;
}
