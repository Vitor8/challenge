<?php
    require_once __DIR__ . '/../env.php';

    $host = getenv('DB_HOST');
    $dbname = getenv('DB_NAME');
    $user = getenv('DB_USER');
    $pass = getenv('DB_PASS');

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if (php_sapi_name() === "cli") { 
            fwrite(STDOUT, "Conexão com o banco de dados '$dbname' foi bem-sucedida!\n");
        }
    } catch (PDOException $e) {
        die("Erro na conexão: " . $e->getMessage());
    }
?>