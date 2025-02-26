<?php

$indexPath = realpath(__DIR__ . '/frontend/index.html');

if (file_exists($indexPath)) {
    if (PHP_OS === 'Darwin') {
        // MacOS
        exec("open $indexPath");
    } elseif (PHP_OS === 'Linux') {
        // Linux
        exec("xdg-open $indexPath");
    } elseif (PHP_OS === 'WINNT') {
        // Windows
        exec("start $indexPath");
    }
    echo "Aplicação iniciada com sucesso! O arquivo index.html foi aberto no seu navegador.\n";
} else {
    echo "Erro: O arquivo index.html não foi encontrado.\n";
}
