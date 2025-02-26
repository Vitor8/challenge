<?php

require_once 'database/create_database.php';
require_once 'database/database.php';
require_once 'database/create_migrations_table.php';
require_once 'database/migrate_all.php';

echo "✅ Banco de dados e migrations configurados com sucesso!\n";

$host = "localhost";
$port = 8000;

echo "🚀 Iniciando servidor PHP em http://$host:$port...\n";
$command = "php -S $host:$port -t frontend router.php & sleep 2 && php open_index.php";
passthru($command); 

echo "⛔ Servidor encerrado. Feche o terminal para sair.\n";
