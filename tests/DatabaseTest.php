<?php

require_once __DIR__ . '/../core/DB.php';

class DatabaseTest {
    private $pdo;

    public function __construct() {
        try {
            $this->pdo = DB::getConnection();
            echo "[✅] Conexão com o banco de dados foi bem-sucedida.\n";
        } catch (PDOException $e) {
            echo "[❌] Erro na conexão com MySQL: " . $e->getMessage() . "\n";
            exit(1);
        }

        $this->checkTableExists('migrations');
        $this->checkTableExists('usuarios');
        $this->checkTableExists('clients');
        $this->checkTableExists('addresses');
        $this->checkTableExists('client_address');
    }

    private function checkTableExists($tableName) {
        $stmt = $this->pdo->prepare("SHOW TABLES LIKE :tableName");
        $stmt->execute(['tableName' => $tableName]);
        $tableExists = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($tableExists) {
            echo "[✅] Tabela '$tableName' encontrada.\n";
        } else {
            echo "[❌] Tabela '$tableName' NÃO encontrada!\n";
            exit(1);
        }
    }
}

new DatabaseTest();
