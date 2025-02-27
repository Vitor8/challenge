<?php
require_once __DIR__ . '/../../core/DB.php';

try {
    $pdo = DB::getConnection();

    $sql = "CREATE TABLE IF NOT EXISTS clients (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        birth DATE NOT NULL,
        cpf VARCHAR(14) NOT NULL UNIQUE,
        rg VARCHAR(12) NOT NULL UNIQUE,
        phone VARCHAR(20) NOT NULL
    )";

    $pdo->exec($sql);
    echo "Tabela 'clients' criada com sucesso!\n";
} catch (PDOException $e) {
    die("Erro ao criar tabela 'clients': " . $e->getMessage());
}
