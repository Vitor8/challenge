<?php

require_once __DIR__ . '/../core/DB.php';

try {
    $pdo = DB::getConnection();
    if (php_sapi_name() === "cli") { 
        fwrite(STDOUT, "Conexão com o banco de dados '" . getenv('DB_NAME') . "' foi bem-sucedida!\n");
    }
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}
