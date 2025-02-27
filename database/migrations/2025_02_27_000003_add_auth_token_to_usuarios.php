<?php
require_once __DIR__ . '/../../core/DB.php';

class AddAuthTokenToUsuarios {
    public function up() {
        $pdo = DB::getConnection();

        $sql = "ALTER TABLE usuarios 
                ADD COLUMN auth_token VARCHAR(255) NULL,
                ADD COLUMN token_expires_at DATETIME NULL";

        $pdo->exec($sql);
        echo "Migration 'AddAuthTokenToUsuarios' executada com sucesso!\n";
    }

    public function down() {
        $pdo = DB::getConnection();

        $sql = "ALTER TABLE usuarios 
                DROP COLUMN auth_token, 
                DROP COLUMN token_expires_at";

        $pdo->exec($sql);
        echo "Migration 'AddAuthTokenToUsuarios' revertida com sucesso!\n";
    }
}
