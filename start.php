<?php

/**
 * Initializes the database and runs migrations before starting the PHP server.
 */

require_once 'database/create_database.php';
require_once 'database/database.php';
require_once 'database/create_migrations_table.php';
require_once 'database/migrate_all.php';

echo "✅ Database and migrations successfully set up!\n";

$host = "localhost";
$port = 8000;

echo "🚀 Starting PHP server at http://$host:$port...\n";

/**
 * Starts the PHP built-in server and opens the default browser.
 */
function startServer(string $host, int $port): void {
    $command = sprintf(
        'php -S %s:%d -t frontend router.php & sleep 2 && php open_index.php',
        escapeshellarg($host),
        $port
    );
    passthru($command);
}

startServer($host, $port);

echo "⛔ Server stopped. Close the terminal to exit.\n";
