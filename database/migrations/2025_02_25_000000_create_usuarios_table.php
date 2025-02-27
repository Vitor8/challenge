<?php
require_once __DIR__ . '/../../core/DB.php';

class CreateUsuariosTable {
    private $pdo;

    public function __construct() {
        $this->pdo = DB::getConnection();
    }

    public function up() {
        try {
            $sql = "CREATE TABLE IF NOT EXISTS usuarios (
                id INT AUTO_INCREMENT PRIMARY KEY,
                login VARCHAR(50) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                is_activated TINYINT(1) DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            $this->pdo->exec($sql);
            echo "✅ Tabela 'usuarios' criada com sucesso!\n";
        } catch (PDOException $e) {
            die("❌ Erro ao criar tabela 'usuarios': " . $e->getMessage() . "\n");
        }
    }

    public function down() {
        try {
            $sql = "DROP TABLE IF EXISTS usuarios";
            $this->pdo->exec($sql);
            echo "⚠️ Tabela 'usuarios' removida com sucesso!\n";
        } catch (PDOException $e) {
            die("❌ Erro ao remover tabela 'usuarios': " . $e->getMessage() . "\n");
        }
    }
}
