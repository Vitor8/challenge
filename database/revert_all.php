<?php
require_once __DIR__ . '/../core/DB.php';

try {
    $pdo = DB::getConnection();

    $stmt = $pdo->query("SELECT migration_name FROM migrations ORDER BY id DESC");
    $executedMigrations = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($executedMigrations as $migrationName) {
        $migrationFile = __DIR__ . "/migrations/$migrationName.php";

        if (file_exists($migrationFile)) {
            require_once $migrationFile;

            $className = getMigrationClassName($migrationFile);

            if ($className && class_exists($className)) {
                $migrationInstance = new $className();
                $migrationInstance->down();

                $stmt = $pdo->prepare("DELETE FROM migrations WHERE migration_name = :migration_name");
                $stmt->execute(['migration_name' => $migrationName]);

                echo "⚠️ Migration '$migrationName' revertida com sucesso!\n";
            } else {
                echo "⚠️ Classe da migration não encontrada em '$migrationFile'.\n";
            }
        }
    }
} catch (PDOException $e) {
    die("❌ Erro ao reverter as migrations: " . $e->getMessage() . "\n");
}

function getMigrationClassName($filePath) {
    $content = file_get_contents($filePath);
    if (preg_match('/class\s+([a-zA-Z0-9_]+)\s+/', $content, $matches)) {
        return $matches[1];
    }
    return null;
}
