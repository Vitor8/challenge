<?php
require_once __DIR__ . '/../../core/DB.php';

class CreateAddressesTable {
    public function up() {
        try {
            $pdo = DB::getConnection();

            $sql = "CREATE TABLE IF NOT EXISTS addresses (
                id INT AUTO_INCREMENT PRIMARY KEY,
                street VARCHAR(255) NOT NULL,
                number VARCHAR(10) NOT NULL,
                district VARCHAR(100) NOT NULL,
                city VARCHAR(100) NOT NULL,
                state VARCHAR(2) NOT NULL,
                zip_code VARCHAR(9) NOT NULL
            )";

            $pdo->exec($sql);
            echo "✅ Tabela 'addresses' criada com sucesso!\n";
        } catch (PDOException $e) {
            die("❌ Erro ao criar tabela 'addresses': " . $e->getMessage() . "\n");
        }
    }

    public function down() {
        try {
            $pdo = DB::getConnection();

            $sql = "DROP TABLE IF EXISTS addresses";
            $pdo->exec($sql);
            echo "⚠️ Tabela 'addresses' removida com sucesso!\n";
        } catch (PDOException $e) {
            die("❌ Erro ao remover tabela 'addresses': " . $e->getMessage() . "\n");
        }
    }
}
