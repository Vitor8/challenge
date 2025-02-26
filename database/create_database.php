<?php
    require_once __DIR__ . '/../env.php';

    $host = getenv('DB_HOST');
    $user = getenv('DB_USER');
    $pass = getenv('DB_PASS');
    $dbname = getenv('DB_NAME');

    try {
        $pdo = new PDO("mysql:host=$host;charset=utf8", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "CREATE DATABASE IF NOT EXISTS $dbname";
        $pdo->exec($sql);

        echo "Banco de dados '$dbname' criado ou já existe!.\n";
    } catch (PDOException $e) {
        die("Erro na criação do banco de dados: " . $e->getMessage());
    }
?>