<?php

require_once __DIR__ . '/../env.php';

class DatabaseTest {
    private $pdo;

    public function __construct() {
        $host = getenv('DB_HOST');
        $dbname = getenv('DB_NAME');
        $user = getenv('DB_USER');
        $pass = getenv('DB_PASS');

        try {
            $this->pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "[✅] Conexão com o banco de dados '$dbname' foi bem-sucedida.\n";
        } catch (PDOException $e) {
            echo "[❌] Erro na conexão com MySQL: " . $e->getMessage() . "\n";
            exit(1);
        }

        $this->checkTableExists('migrations');
        $this->checkTableExists('usuarios');
    }

    private function checkTableExists($tableName) {
        $stmt = $this->pdo->query("SHOW TABLES LIKE '$tableName'");
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
