<?php

require_once __DIR__ . '/../env.php';

class DB {
    private static ?PDO $pdo = null;

    private function __construct() {}

    public static function getConnection(): PDO {
        if (self::$pdo === null) {
            $host = getenv('DB_HOST');
            $dbname = getenv('DB_NAME');
            $user = getenv('DB_USER');
            $pass = getenv('DB_PASS');

            try {
                self::$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                if (php_sapi_name() === "cli") { 
                    fwrite(STDOUT, "Conexão com o MySQL bem-sucedida!\n");
                }
            } catch (PDOException $e) {
                die("Erro na conexão: " . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}
