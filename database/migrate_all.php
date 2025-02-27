<?php
require_once __DIR__ . '/../core/DB.php';

try {
    $pdo = DB::getConnection();
    
    $stmt = $pdo->query("SELECT migration_name FROM migrations");
    $executedMigrations = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $migrationFiles = glob(__DIR__ . '/migrations/*.php');

    foreach ($migrationFiles as $migrationFile) {
        $migrationName = basename($migrationFile);
        
        if (!in_array($migrationName, $executedMigrations)) {
            require_once $migrationFile;
            
            $stmt = $pdo->prepare("INSERT INTO migrations (migration_name) VALUES (:migration_name)");
            $stmt->execute(['migration_name' => $migrationName]);
            
            echo "Migration '$migrationName' executada com sucesso!\n";
        }
    }
} catch (PDOException $e) {
    die("Erro ao executar as migrations: " . $e->getMessage());
}
