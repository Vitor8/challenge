<?php

require_once __DIR__ . '/../core/DB.php';

try {
    $pdo = DB::getConnection();
    $sql = "CREATE TABLE IF NOT EXISTS migrations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        migration_name VARCHAR(255) NOT NULL,
        executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
    echo "Tabela 'migrations' criada com sucesso ou já existente!\n";
} catch (PDOException $e) {
    die("Erro ao criar a tabela 'migrations': " . $e->getMessage());
}
