<?php
require_once __DIR__ . '/../../core/DB.php';

class CreateClientAddressTable {
    public function up() {
        try {
            $pdo = DB::getConnection();

            $sql = "CREATE TABLE IF NOT EXISTS client_address (
                client_id INT NOT NULL,
                address_id INT NOT NULL,
                PRIMARY KEY (client_id, address_id),
                FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
                FOREIGN KEY (address_id) REFERENCES addresses(id) ON DELETE CASCADE
            )";

            $pdo->exec($sql);
            echo "✅ Tabela 'client_address' criada com sucesso!\n";
        } catch (PDOException $e) {
            die("❌ Erro ao criar tabela 'client_address': " . $e->getMessage() . "\n");
        }
    }

    public function down() {
        try {
            $pdo = DB::getConnection();

            $sql = "DROP TABLE IF EXISTS client_address";
            $pdo->exec($sql);
            echo "⚠️ Tabela 'client_address' removida com sucesso!\n";
        } catch (PDOException $e) {
            die("❌ Erro ao remover tabela 'client_address': " . $e->getMessage() . "\n");
        }
    }
}
