<?php
require_once __DIR__ . '/../../core/DB.php';

try {
    $pdo = DB::getConnection();
    
    $sql = "CREATE TABLE IF NOT EXISTS usuarios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        login VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        is_activated TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    $pdo->exec($sql);
    echo "Tabela 'usuarios' criada com sucesso!\n";
} catch (PDOException $e) {
    die("Erro ao criar tabela: " . $e->getMessage());
}
